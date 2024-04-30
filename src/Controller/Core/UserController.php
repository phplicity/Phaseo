<?php

namespace App\Controller\Core;

use App\Dto\Core\BreadcrumbDto;
use App\Dto\Core\SearchParamsDto;
use App\Entity\Core\User;
use App\Form\Core\ChangePasswordFormType;
use App\Form\Core\UserFormType;
use App\Service\Core\DatatableService;
use App\Service\Core\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    #[Route(path: '/administration', name: 'core_admin_user_administration', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function userAdministration(): Response
    {
        // TODO: jogosultság - UserVoter.php

        $formPwd = $this->createForm(ChangePasswordFormType::class, null, [
            'label' => true,
            'action' => $this->generateUrl('core_admin_user_administration_password_change'),
            'method' => 'POST',
        ]);

        $formSettings = $this->createForm(UserFormType::class, null, [
            'action' => $this->generateUrl('core_admin_user_administration_user_settings_change'),
            'method' => 'POST',
        ]);

        return $this->render('core/users/user_form.html.twig', [
            'htmlPageTitle' => 'page.html_title_form',
            'pageTitle' => 'page.title_form',
            'breadcrumb' => [
                new BreadcrumbDto('core_admin_dashboard', 'breadcrumb.dashboard', false),
                new BreadcrumbDto('core_admin_user_list', 'breadcrumb.users', false),
                new BreadcrumbDto('core_admin_user_administration', 'breadcrumb.user_form', true),
            ],
            'formPassword' => [
                'title' => 'form.password.title',
                'button' => 'form.password.button',
                'data' => $formPwd->createView(),
            ],
            'formSettings' => [
                'title' => 'form.settings.title',
                'button' => 'form.settings.button',
                'data' => $formSettings->createView(),
            ],
        ]);
    }

    #[Route(path: '/administration/password-change', name: 'core_admin_user_administration_password_change', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function passwordChange(Request $rq, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        // TODO: jogosultság - UserVoter.php

        /** @var User $user */
        $user = $this->getUser();

        $formPwd = $this->createForm(ChangePasswordFormType::class, null, [
            'label' => true,
            'action' => $this->generateUrl('custom_form_submit'),
            'method' => 'POST',
        ]);

        $formPwd->handleRequest($rq);

        if ($formPwd->isSubmitted() && $formPwd->isValid()) {
            // Encode(hash) the plain password, and set it.
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $formPwd->get('password')->getData()
            );

            $user->setPassword($encodedPassword);
            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute('core_admin_user_administration');
    }

    #[Route(path: '/administration/user-settings-change', name: 'core_admin_user_administration_user_settings_change', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function userSettingsChange(Request $rq, EntityManagerInterface $em): Response
    {
        // TODO: jogosultság - UserVoter.php

        /** @var User $user */
        $user = $this->getUser();

        $formPwd = $this->createForm(ChangePasswordFormType::class, null, [
            'label' => true,
            'action' => $this->generateUrl('custom_form_submit'),
            'method' => 'POST',
        ]);

        $formPwd->handleRequest($rq);

        if ($formPwd->isSubmitted() && $formPwd->isValid()) {
            // Encode(hash) the plain password, and set it.
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $formPwd->get('password')->getData()
            );

            $user->setPassword($encodedPassword);
            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute('core_admin_user_administration');
    }
}
