<?php

namespace App\Controller\Core\Menu;

use App\Dto\Core\LeftNavbarDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/menu')]
class LeftNavbarController extends AbstractController
{
    #[Route('', name: 'core_admin_menu_left_navbar')]
    #[IsGranted('ROLE_USER')]
    public function leftNavbar(): Response
    {
        return $this->render('core/menu/left_navbar.html.twig', [
            'menuItems' => [
                new LeftNavbarDto('core_admin_dashboard', 'menu.left_navbar.title')
            ],
        ]);
    }
}
