<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\Product;
use App\Form\ArticleType;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    /**
     * Afficher la liste de tous les articles
     */
    #[Route('/articles', name: 'article_index')]
    public function index(): Response
    {
        $articles = $this->entityManager->getRepository(Article::class)->findAll();
        $articlesWithImagePaths = [];
        foreach ($articles as $article) {
            $productImagePath = $article->getProduct()
                ? str_replace('uploads/uploads/', 'uploads/', $article->getProduct()->getImage())
                : null;
            $articlesWithImagePaths[] = [
                'article' => $article,
                'productImagePath' => $productImagePath,
            ];
        }

        return $this->render('articles/index.html.twig', [
            'articlesWithImagePaths' => $articlesWithImagePaths,
        ]);
    }

    /**
     * Afficher le formulaire pour soumettre un nouvel article
     */
    #[Route('/articles/new', name: 'article_new')]
    public function new(Request $request): Response
   {

    // Vérifier si l'utilisateur a le bon rôle
    if (!$this->isGranted('ROLE_ARTIST')) {
        $this->addFlash('error', 'Vous n\'avez pas l\'autorisation pour publier un nouvel article.');
        return $this->redirectToRoute('article_index');
    }

    // Créer un nouvel article
    $article = new Article();
    $form = $this->createForm(ArticleType::class, $article, [ 'user' => $this->getUser() // Passer l'utilisateur connecté ici
]);


    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est bien le propriétaire du produit
        $product = $article->getProduct();  // Assurez-vous que vous récupérez le produit lié à l'article
        if ($product && $product->getUser() !== $user) {
            // Si l'utilisateur n'est pas le propriétaire du produit, afficher un message d'erreur
            $this->addFlash('error', 'Vous ne pouvez publier un article que pour vos propres produits.');
            return $this->redirectToRoute('article_index');
        }

        // Définir l'auteur de l'article
        $article->setAuthor($user);

        // Générer le slug à partir du titre de l'article
        $slug = $this->slugger->slug($article->getTitle())->lower();
        $article->setSlug($slug);

        // Définir la date de création et de mise à jour
        $article->setCreatedAt(new \DateTimeImmutable());
        $article->setUpdateAt(new \DateTimeImmutable());

        // Persister l'article et enregistrer en base
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // Afficher un message de succès
        $this->addFlash('success', 'Votre article a été publié avec succès.');
        return $this->redirectToRoute('article_index');
    }

    return $this->render('articles/new.html.twig', [
        'form' => $form->createView(),
    ]);
}
    /**
     * Afficher un article individuel
     */
    #[Route('/articles/{slug}', name: 'article_show')]
    public function show(string $slug, Request $request): Response
    {
        $article = $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('L\'article demandé n\'existe pas.');
        }
    
        $productImagePath = $article->getProduct()
            ? str_replace('uploads/uploads/', 'uploads/', $article->getProduct()->getImage())
            : null;
    
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
                $commentaire->setAuthor($this->getUser());
                $commentaire->setArticle($article);
                $commentaire->setCreatedAt(new \DateTimeImmutable());
    
                $this->entityManager->persist($commentaire);
                $this->entityManager->flush();
    
                $this->addFlash('success', 'Votre commentaire a été ajouté avec succès.');
                return $this->redirectToRoute('article_show', ['slug' => $slug]);
            }
    
            $this->addFlash('error', 'Vous devez être connecté pour commenter.');
        }
    
        $commentaires = $this->entityManager->getRepository(Commentaire::class)->findBy(['article' => $article]);
        return $this->render('articles/show.html.twig', [
            'article' => $article,
            'productImagePath' => $productImagePath,
            'form' => $form->createView(),
            'commentaires' => $commentaires,
        ]);
    }
    #[Route('/articles/{slug}/edit', name: 'article_edit')]
public function edit(Request $request, string $slug): Response
{
    $article = $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

    if (!$article) {
        throw $this->createNotFoundException('L\'article demandé n\'existe pas.');
    }

    // Vérifier si l'utilisateur est l'auteur de l'article ou un administrateur
    if ($article->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
        $this->addFlash('error', 'Vous n\'avez pas l\'autorisation de modifier cet article.');
        return $this->redirectToRoute('article_index');
    }

    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $article->setUpdateAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $this->addFlash('success', 'Votre article a été modifié avec succès.');
        return $this->redirectToRoute('article_index');
    }

    return $this->render('articles/edit.html.twig', [
        'form' => $form->createView(),
        'article' => $article,
    ]);
}
/**
 * Supprimer un article
 */
#[Route('/articles/{slug}/delete', name: 'article_delete')]
public function delete(string $slug): Response
{
    // Récupérer l'article à supprimer
    $article = $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

    if (!$article) {
        throw $this->createNotFoundException('L\'article demandé n\'existe pas.');
    }

    // Vérifier si l'utilisateur est l'auteur de l'article ou un administrateur
    if ($article->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
        $this->addFlash('error', 'Vous n\'avez pas l\'autorisation de supprimer cet article.');
        return $this->redirectToRoute('article_index');
    }

    // Supprimer les commentaires associés à l'article
    $commentaires = $article->getCommentaires();  // On récupère tous les commentaires de l'article
    foreach ($commentaires as $commentaire) {
        $this->entityManager->remove($commentaire);  // Supprimer chaque commentaire
    }

    // Supprimer l'article
    $this->entityManager->remove($article);
    $this->entityManager->flush();

    // Ajouter un message flash pour confirmer la suppression
    $this->addFlash('success', 'L\'article a été supprimé avec succès.');

    // Rediriger vers la liste des articles
    return $this->redirectToRoute('article_index');
}



}
