<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Component\HttpKernel\KernelEvents;
final class UserLocaleListener
{

    public function __construct(private readonly Security $security,  // Dnas le constructeur, on injecte le service Security pour accéder à l'utilisateur connecté
    private readonly LocaleSwitcher $localeSwitcher) // On injecte également le service LocaleSwitcher pour changer la locale de l'application
    {
    }
    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onRequestEvent(RequestEvent $event): void
    {
        $user = $this->security->getUser(); // On récupère l'utilisateur connecté
        if ($user && $user instanceof \App\Entity\User) { // On vérifie que l'utilisateur est bien connecté et qu'il est de type User
            $this->localeSwitcher->setLocale($user->getLocale()); // On change la locale de l'application en fonction de la locale de l'utilisateur 
        }
    }
}
