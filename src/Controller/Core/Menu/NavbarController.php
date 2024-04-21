<?php

namespace App\Controller\Core\Menu;

use App\Dto\Core\LanguageNavbarDto;
use App\Dto\Core\LeftNavbarDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/menu')]
class NavbarController extends AbstractController
{
    #[Route('/left-navbar', name: 'core_admin_menu_left_navbar')]
    #[IsGranted('ROLE_USER')]
    public function leftNavbar(): Response
    {
        return $this->render('core/menu/left_navbar.html.twig', [
            'menuItems' => [
                new LeftNavbarDto('core_admin_dashboard', 'menu.left_navbar.title')
            ],
        ]);
    }

    #[Route('/language-navbar', name: 'core_admin_menu_language_navbar')]
    #[IsGranted('ROLE_USER')]
    public function languageNavbar(Request $request): Response
    {
        return $this->render('core/menu/language_navbar.html.twig', [
            'menuItems' => [
                new LanguageNavbarDto(
                    'core_admin_change_language',
                    'menu.language_navbar.title_english',
                    $request->getLocale() === LanguageNavbarDto::LANGUAGE_CODE_ENGLISH_ROUTE,
                    LanguageNavbarDto::LANGUAGE_CODE_ENGLISH
                ),
                new LanguageNavbarDto(
                    'core_admin_change_language',
                    'menu.language_navbar.title_hungarian',
                    $request->getLocale() === LanguageNavbarDto::LANGUAGE_CODE_HUNGARIAN,
                    LanguageNavbarDto::LANGUAGE_CODE_HUNGARIAN
                ),
            ],
        ]);
    }
}
