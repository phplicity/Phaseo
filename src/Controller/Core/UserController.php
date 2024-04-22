<?php

namespace App\Controller\Core;

use App\Dto\Core\BreadcrumbDto;
use App\Dto\Core\SearchParamsDto;
use App\Service\Core\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/list', name: 'core_admin_user_list')]
    #[IsGranted('ROLE_USER')]
    public function userList(Request $rq, UserService $userService): Response
    {
        // TODO: jogosultság - UserVoter.php

        return $this->render('core/users/users_list.html.twig', [
            'htmlPageTitle' => 'page.html_title_list',
            'pageTitle' => 'page.title_list',
            'breadcrumb' => [
                new BreadcrumbDto('core_admin_dashboard', 'breadcrumb.dashboard', false),
                new BreadcrumbDto('core_admin_user_list', 'breadcrumb.users', true)
            ],
            'list' => $userService->getList(new SearchParamsDto($rq)),
            'datatable' => [
                'title' => 'datatable.title',
                'headers' => [
                    'datatable.headers.id',
                    'datatable.headers.email',
                    'datatable.headers.roles',
                ],
            ],
        ]);
    }
}
