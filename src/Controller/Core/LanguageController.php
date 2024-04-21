<?php

namespace App\Controller\Core;

use App\Dto\Core\LanguageNavbarDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/language')]
class LanguageController extends AbstractController
{
    #[Route('/change-language/{languageCode}', name: 'core_admin_change_language')]
    #[IsGranted('ROLE_USER')]
    public function dashboard(string $languageCode): Response
    {
        $lang = $languageCode == LanguageNavbarDto::LANGUAGE_CODE_ENGLISH
            ? LanguageNavbarDto::LANGUAGE_CODE_ENGLISH_ROUTE
            : $languageCode
        ;
        
        return $this->redirectToRoute('core_admin_dashboard.' . $lang);
    }
}
