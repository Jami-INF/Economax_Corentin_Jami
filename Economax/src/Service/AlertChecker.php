<?php

namespace App\Service;

use App\Entity\Deal;
use App\Repository\UserRepository;

class AlertChecker
{

    public function __construct(
        protected UserRepository $userRepository,
    )
    {
    }

    public function checkAlertsByDeal(Deal $deal): void
    {
        $allUsers = $this->userRepository->findAll();
        foreach ($allUsers as $user) {
            $userAlerts = $user->getAlerts();
            foreach ($userAlerts as $alert) {
                if (preg_match("/{$alert->getKeyWord()}/i", $deal->getTitle())) {
                    $alertTemp = $alert->getTemperatureValue();
                    $dealTemp = $deal->getSumTemperatures();
                    if ($alertTemp <= $dealTemp) {
                        $user->setIsNotify(true);
                        $this->userRepository->save($user);
                    }
                }
            }
        }
    }
}