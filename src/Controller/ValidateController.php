<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ValidateController extends AbstractController
{
    #[Route('/validate', name: 'app_validate')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('validate/index.html.twig');
    }
}
