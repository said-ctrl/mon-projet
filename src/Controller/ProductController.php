<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;
    private ProductService $productService;
    // Injection des services EntityManager et ProductRepository dans le constructeur
    public function __construct(ProductService $productService, ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    // Ajouter un produit
    #[Route('/add_product', name: 'add_product')]
    public function addProduct(Request $request, SluggerInterface $slugger)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productName = $product->getTitle();  // Supposons que vous ayez un champ "name" dans votre entité Product
            $slug = $slugger->slug($productName)->lower();  // Création du slug et conversion en minuscules
    
            // Affecter le slug au produit
            $product->setSlug($slug);

            $product->setUser($this->getUser());
            // Traitement du fichier image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                // dump($this->getParameter('uploads_directory') . '/' . $newFilename);

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $product->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }

            }
            $this->productRepository->save($product);

            $this->addFlash('success', 'Produit ajouté avec succès.');
            return $this->redirectToRoute('product_list');
        }

        return $this->render('product/add_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    // Affichage de tous les produits
    #[Route('/products', name: 'product_list')]
    public function list()
    {
        // Récupérer tous les produits via le repository
        $products = $this->productRepository->findAll();

        return $this->render('product/list.html.twig', [
            'products' => $products,
        ]);
    }

    // Affichage des détails d'un produit spécifique
    #[Route('/product/{slug}', name: 'product_details')]
    public function details(string $slug)
    {
        $product = $this->productRepository->findOneBy(['slug' => $slug]);
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
        if ($product->getReserved() && $this->productService->getRemainingTime($product) === 'Réservation expirée') {
            // Annuler la réservation (mettre à jour la base de données)
            $product->setReserved(false);
            $product->setReservationDate(null);
            $product->setReservedBy(null);
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }
        $articles = $this->entityManager->getRepository(Article::class)->findBy(['product' => $product]);
        $remainingTime = $this->productService->getRemainingTime($product); // Assurez-vous que vous utilisez le bon service

        return $this->render('product/details.html.twig', [
            'product' => $product,
            'articles' => $articles,
            'remainingTime' => $remainingTime,
        ]);
    }
    #[Route('/reserve/{id}', name: 'reserve_product')]
    public function reserveProduct(Product $product): Response
    {

        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash('error', 'Vous devez être connecté pour réserver un produit.');
            return $this->redirectToRoute('app_login');
        }

        try {
            $user = $this->getUser();
            $this->productService->reserveProduct($product, $user);
            $this->addFlash('success', 'Produit réservé avec succès.');
        } catch (\RuntimeException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('product_list');
    }

    #[Route('/unreserve/{id}', name: 'unreserve_product')]
    public function unreserveProduct(Product $product): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash('error', 'Vous devez être connecté pour annuler une réservation.');
            return $this->redirectToRoute('app_login');
        }

        try {
            $user = $this->getUser();
            $this->productService->unreserveProduct($product, $user);
            $this->addFlash('success', 'Réservation annulée avec succès.');
        } catch (\RuntimeException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('product_list');
    }

    #[Route('/entry', name: 'app_entry')]
    public function entry()
    {
        // Obtenir tous les produits
        $products = $this->productRepository->findAll();

        // Mélanger les produits pour obtenir un affichage aléatoire
        shuffle($products);

        // Limiter à un nombre de produits (par exemple, 6)
        $randomProducts = array_slice($products, 0, 6);

        return $this->render('entry/index.html.twig', [
            'products' => $randomProducts,
        ]);
    }
    #[Route('/products/search', name: 'product_search')]
    public function search(Request $request): Response
    {
        // Récupérer la valeur de la recherche directement avec GET
        $query = $request->query->get('query', '');

        $products = [];
        if ($query) {
            $products = $this->productRepository->findBySearch($query);
        }

        return $this->render('product/search.html.twig', [
            'products' => $products,
            'query' => $query,
        ]);
    }
}
