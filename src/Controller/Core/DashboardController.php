<?php

namespace App\Controller\Core;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'core_admin_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function dashboard(Request $request): Response
    {
        return $this->render('core/dashboard/dashboard.html.twig');
    }
}
