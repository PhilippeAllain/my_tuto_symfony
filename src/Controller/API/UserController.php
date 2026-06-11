<?php

namespace App\Controller\API;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

#[Route('/api/user/me')]
    public function me()
    {
        return $this->json(['message' => 'Bonjour']);
    }
}