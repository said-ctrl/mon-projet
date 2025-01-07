<?php
namespace App\Service;

use App\Entity\Product;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductService
{
    private $entityManager;
    private $slugger;
    private $uploadsDirectory;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger, string $uploadsDirectory)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
        $this->uploadsDirectory = $uploadsDirectory;
    }

    public function addProduct(Product $product, ?UploadedFile $imageFile)
    {
        if ($imageFile) {
            $this->uploadImage($product, $imageFile);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    private function uploadImage(Product $product, UploadedFile $imageFile)
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

        try {
            $imageFile->move(
                $this->uploadsDirectory,
                $newFilename
            );
            $product->setImage($newFilename);
        } catch (FileException $e) {
            throw new \RuntimeException('Erreur lors du téléchargement de l\'image.');
        }
    }

    public function reserveProduct(Product $product, $user)
    {
        if ($product->getReserved()) {
            throw new \RuntimeException('Ce produit est déjà réservé.');
        }

        $product->setReserved(true);
        $product->setReservationDate(new \DateTime());
        $product->setReservedBy($user);

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function unreserveProduct(Product $product, $user)
    {
        if (!$product->getReserved()) {
            throw new \RuntimeException('Ce produit n\'est pas réservé.');
        }

        if ($product->getReservedBy() !== $user) {
            throw new \RuntimeException('Vous ne pouvez pas annuler une réservation que vous n\'avez pas effectuée.');
        }

        $product->setReserved(false);
        $product->setReservationDate(null);
        $product->setReservedBy(null);

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function checkReservationExpiration()
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(Product::class, 'p')
            ->where('p.reserved = :reserved')
            ->andWhere('p.reservationDate < :expirationDate')
            ->setParameter('reserved', true)
            ->setParameter('expirationDate', (new \DateTime())->modify('-1 hours'));
    
        $products = $queryBuilder->getQuery()->getResult();
    
        foreach ($products as $product) {
            $product->setReserved(false);
            $product->setReservationDate(null);
            $this->entityManager->persist($product);
        }
    
        $this->entityManager->flush(); // Enregistrer les changements
    }

    public function getRemainingTime(Product $product)
    {
        if (!$product->getReserved()) {
            return 'Ce produit n\'est pas réservé.'; // Message si le produit n'est pas réservé
        }
        $reservationDate = $product->getReservationDate(); // Récupérer la date de réservation
    
        if (!$reservationDate instanceof \DateTime) {
            return 'Date de réservation invalide'; // Assurez-vous que la date est valide
        }
    
        // Définir le temps d'expiration de la réservation (par exemple, 30 minutes après la réservation)
        $expirationDate = clone $reservationDate; // Créer une copie de la réservation
        $expirationDate->modify('+30 minutes'); // Ajoute 30 minutes à la date de réservation
    
        // Calculer la différence entre la date actuelle et la date d'expiration
        $now = new \DateTime(); // Récupérer l'heure actuelle
        $interval = $now->diff($expirationDate); // Calculer la différence entre les deux dates
    
        // Si la date d'expiration est déjà dépassée, retournez un message ou un autre comportement
        if ($interval->invert === 1) {
            return 'Réservation expirée';
        }
        $remainingTime = '';
    if ($interval->h > 0) {
        $remainingTime .= $interval->h . ' heures, ';
    }
    if ($interval->i > 0) {
        $remainingTime .= $interval->i . ' minutes, ';
    }
    if ($interval->s > 0) {
        $remainingTime .= $interval->s . ' secondes';
    }
    
        // Retourner le temps restant au format "x minutes restantes"
        return sprintf('%d minutes restantes', $interval->h * 60 + $interval->i);
    }
    
    

}
