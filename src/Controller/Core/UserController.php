<?php

namespace App\Controller\Core;

use App\Dto\Core\BreadcrumbDto;
use App\Dto\Core\SearchParamsDto;
use App\Form\Core\UserFormType;
use App\Service\Core\DatatableService;
use App\Service\Core\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/list', name: 'core_admin_user_list')]
    #[IsGranted('ROLE_USER')]
    public function userList(UserService $userService, DatatableService $datatableService): Response
    {
        // TODO: jogosultság - UserVoter.php

        return $this->render('core/users/users_list.html.twig', [
            'htmlPageTitle' => 'page.html_title_list',
            'pageTitle' => 'page.title_list',
            'breadcrumb' => [
                new BreadcrumbDto('core_admin_dashboard', 'breadcrumb.dashboard', false),
                new BreadcrumbDto('core_admin_user_list', 'breadcrumb.users', true)
            ],
            'datatable' => [
                'title' => 'datatable.title',
                'headers' => $datatableService->getHeaders('datatable.headers', $userService::COLUMN_NAMES_IN_ORDER),
            ],
        ]);
    }

    #[Route('/datatable-list', name: 'core_admin_user_datatable_list')]
    #[IsGranted('ROLE_USER')]
    public function userDatatableList(Request $rq, UserService $userService, DatatableService $datatableService): JsonResponse
    {
        // TODO: jogosultság - UserVoter.php

        return new JsonResponse(
            $datatableService->convertSqlResultToDatatableResult(
                (int) $rq->get('draw', 1),
                $userService->getListWithCount(
                    new SearchParamsDto(
                        $rq,
                        $userService::COLUMN_NAMES_IN_ORDER
                    )
                )
            )
        );

    }

    #[Route('/administration', name: 'core_admin_user_administration')]
    #[IsGranted('ROLE_USER')]
    public function userNew(Request $rq, UserService $userService, DatatableService $datatableService): Response
    {
        // TODO: jogosultság - UserVoter.php

        $form = $this->createForm(UserFormType::class);

        $form->handleRequest($rq);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the form submission

            // Maybe redirect to a "thank you" page.
            return $this->redirectToRoute('Minden jó volt');
        }

        return $this->render('core/users/user_form.html.twig', [
            'htmlPageTitle' => 'page.html_title_form',
            'pageTitle' => 'page.title_form',
            'breadcrumb' => [
                new BreadcrumbDto('core_admin_dashboard', 'breadcrumb.dashboard', false),
                new BreadcrumbDto('core_admin_user_list', 'breadcrumb.users', false),
                new BreadcrumbDto('core_admin_user_administration', 'breadcrumb.user_form', true),
            ],
            'form' => [
                'title' => 'form.title',
                'data' => $form->createView(),
            ],
        ]);
    }
}
