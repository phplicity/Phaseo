<?php

namespace App\Controller\Core;

use App\Dto\Core\BreadcrumbDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'core_admin_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function dashboard(): Response
    {
        return $this->render('core/dashboard/dashboard.html.twig', [
            'htmlPageTitle' => 'page.html_title',
            'pageTitle' => 'page.title',
            'breadcrumb' => [
                new BreadcrumbDto('core_admin_dashboard', 'breadcrumb.dashboard', true)
            ]
        ]);
    }
}
