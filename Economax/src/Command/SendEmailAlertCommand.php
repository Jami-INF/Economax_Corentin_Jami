<?php

namespace App\Command;

use App\Repository\AlertRepository;
use App\Repository\DealRepository;
use App\Repository\UserRepository;
use App\Service\EmailSender;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-email-alert',
    description: 'Add a short description for your command',
)]
class SendEmailAlertCommand extends Command
{
    public function __construct(
        protected EmailSender $emailSender,
        protected UserRepository $userRepository,
        protected DealRepository $dealRepository,
        protected AlertRepository $alertRepository
    )
    {
        parent::__construct();
    }


    protected function configure(): void
    {
        $this->setHelp('This command allows you to send email to all users who have alerts');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $numberEmailSent = 0;
        $allUsers = $this->userRepository->findAll();
        $allDeals = $this->dealRepository->findAll();
        foreach ($allUsers as $user) {
            $deals = [];
            $userAlerts = $user->getAlerts();
            foreach ($userAlerts as $alert) {
                if($alert->isIsNotify() === false) {
                    continue;
                }
                foreach ($allDeals as $deal) {
                    if (preg_match("/{$alert->getKeyWord()}/i", $deal->getTitle())) {
                        $alertTemp = $alert->getTemperatureValue();
                        $dealTemp = $deal->getSumTemperatures();
                        if ($alertTemp <= $dealTemp) {
                            // verifier si le deal date d'aujourd'hui
                            if($deal->getCreatedAt()->format('Y-m-d') === date('Y-m-d')) {
                                $deals[] = $deal;
                            }
                        }
                    }
                }
            }
            if (count($deals) > 0) {
                $email = $this->emailSender->createTemplatedEmail(
                    null,
                    $user->getEmail(),
                    'Alerte Deal',
                    'emails/alert.html.twig',
                    ['deals' => $deals]
                );

                if(!$this->emailSender->sendEmail($email)) {
                    $io->error('Email not sent');
                    return Command::FAILURE;
                }
                $numberEmailSent++;
            }

        }

        $io->success('Email sent to ' . $numberEmailSent . ' users');

        return Command::SUCCESS;
    }
}
