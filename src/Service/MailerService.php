<?php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        
    }

    public function sendEmailAlert(string $subject, string $to, string $message): void
    {
        // Création de l'objet Email
        $email = (new Email())
            ->from('fifinabs@gmail.com')
            ->to($to)
            ->subject($subject)
            ->text($message);

        try {
            // Envoi de l'e-mail
            $this->mailer->send($email);
            // Log si tout va bien
            $this->logger->info('Email envoyé avec succès à ' . $to);
        } catch (TransportExceptionInterface $e) {
            // En cas d'erreur, logge l'erreur
            $this->logger->error('Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
        }
    }
}
