<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-test-email',
    description: 'Envoie un email de test pour vérifier la config Mailer.',
)]
class SendTestEmailCommand extends Command
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Test d’envoi de mail');

        try {
            $email = (new Email())
                ->from(new Address('contact@qualiclass.fr', 'QualiClass'))
                ->to(new Address('test@qualiclass.fr', 'Test User'))
                ->subject('🧪 Test Symfony Mailer')
                ->text('Voici un mail de test envoyé depuis Symfony.')
                ->html('<p><strong>Hello!</strong> Ceci est un mail de test.</p>');

            $io->text('Création de l’email OK.');

            $this->mailer->send($email);

            $io->success('Email envoyé avec succès !');

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $io->error('Erreur lors de l’envoi du mail : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
