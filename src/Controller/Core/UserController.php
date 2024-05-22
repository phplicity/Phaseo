<?php

namespace App\Controller\Core;

use App\Dto\Core\BreadcrumbDto;
use App\Dto\Core\SearchParamsDto;
use App\Entity\Core\User;
use App\Form\Core\ChangePasswordFormType;
use App\Form\Core\UserCreateFormType;
use App\Form\Core\UserEditFormType;
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
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $tr
    )
    {
    }

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
                $this->getUser(),
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

    #[Route(path: '/administration', name: 'core_admin_user_administration', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function userAdministration(Request $rq, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        // TODO: jogosultság - UserVoter.php

        $formCreate = $this->createForm(UserCreateFormType::class, null, [
            'label' => true,
            'action' => $this->generateUrl('core_admin_user_administration'),
            'method' => 'POST',
        ]);

        $formCreate->handleRequest($rq);

        if ($formCreate->isSubmitted() && $formCreate->isValid()) {
            // Create the new User
            $user = New User();

            $user->setEmail($formCreate->get('email')->getData());
            $user->setRoles([$formCreate->get('roles')->getData()]);

            // Encode(hash) the plain password, and set it.
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $formCreate->get('password')->getData()
            );

            $user->setPassword($encodedPassword);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', $this->tr->trans('flash.user_created', [], 'users'));

            return $this->redirectToRoute('core_admin_user_list');
        }

        return $this->render('core/users/user_create_form.html.twig', [
            'htmlPageTitle' => 'page.html_title_form',
            'pageTitle' => 'page.title_form',
            'breadcrumb' => [
                new BreadcrumbDto('core_admin_dashboard', 'breadcrumb.dashboard', false),
                new BreadcrumbDto('core_admin_user_list', 'breadcrumb.users', false),
                new BreadcrumbDto('core_admin_user_administration', 'breadcrumb.user_form', true),
            ],
            'formCreate' => [
                'title' => 'form.create.title',
                'button' => 'form.create.button',
                'data' => $formCreate->createView(),
            ],
        ]);
    }

    #[Route(path: '/administration/delete/{user}', name: 'core_admin_user_administration_delete', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function userAdministrationDelete(User $user, EntityManagerInterface $em): Response
    {
        // TODO: jogosultság - UserVoter.php
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', $this->tr->trans('flash.user_deleted', [], 'users'));

        return $this->redirectToRoute('core_admin_user_list');
    }

    #[Route(path: '/administration/edit/{user}', name: 'core_admin_user_administration_edit', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function userAdministrationEdit(User $user): Response
    {
        // TODO: jogosultság - UserVoter.php

        $formPwd = $this->createForm(ChangePasswordFormType::class, null, [
            'label' => true,
            'action' => $this->generateUrl('core_admin_user_administration_password_change', ['user' => $user->getId()]),
            'method' => 'POST',
        ]);

        $formSettings = $this->createForm(UserEditFormType::class, $user, [
            'action' => $this->generateUrl('core_admin_user_administration_user_settings_change', ['user' => $user->getId()]),
            'method' => 'POST',
        ]);

        return $this->render('core/users/user_edit_form.html.twig', [
            'htmlPageTitle' => 'page.html_title_form',
            'pageTitle' => 'page.title_form',
            'breadcrumb' => [
                new BreadcrumbDto('core_admin_dashboard', 'breadcrumb.dashboard', false),
                new BreadcrumbDto('core_admin_user_list', 'breadcrumb.users', false),
                new BreadcrumbDto('core_admin_user_administration', 'breadcrumb.user_form', false),
                new BreadcrumbDto('core_admin_user_administration_edit', 'breadcrumb.user_form', true),
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

    #[Route(path: '/administration/password-change/{user}', name: 'core_admin_user_administration_password_change', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function passwordChange(User $user, Request $rq, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        // TODO: jogosultság - UserVoter.php

        $formPwd = $this->createForm(ChangePasswordFormType::class, null, [
            'action' => $this->generateUrl('core_admin_user_administration_password_change', ['user' => $user->getId()]),
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

            $this->addFlash('success', $this->tr->trans('flash.user_password_updated', [], 'users'));
        }

        return $this->redirectToRoute('core_admin_user_administration_edit', ['user' => $user->getId()]);
    }

    #[Route(path: '/administration/user-settings-change/{user}', name: 'core_admin_user_administration_user_settings_change', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function userSettingsChange(User $user, Request $rq, EntityManagerInterface $em): Response
    {
        // TODO: jogosultság - UserVoter.php

        $formSettings = $this->createForm(UserEditFormType::class, null, [
            'action' => $this->generateUrl('core_admin_user_administration_user_settings_change', ['user' => $user->getId()]),
            'method' => 'POST',
        ]);

        $formSettings->handleRequest($rq);

        if ($formSettings->isSubmitted() && $formSettings->isValid()) {
            $user->setEmail($formSettings->get('email')->getData());
            $user->setRoles($formSettings->get('roles')->getData());

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', $this->tr->trans('flash.user_settings_updated', [], 'users'));
        }

        return $this->redirectToRoute('core_admin_user_administration_edit', ['user' => $user->getId()]);
    }
}
