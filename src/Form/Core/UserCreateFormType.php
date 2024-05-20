<?php

namespace App\Form\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserCreateFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $tr
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                                            'message' => $this->tr->trans('form.settings.email_not_blank', [], 'users'),
                                        ]),
                    new Assert\Email([
                                        'message' => $this->tr->trans('form.settings.email_not_valid', [], 'users'),
                                     ]),
                ],
                'attr' => [
                    'autocomplete' => 'email',
                    'autofocus' => true,
                    'class' => 'form-control',
                    'placeholder' => $this->tr->trans('form.settings.email_place_holder', [], 'users'),
                ],
                'label' => $this->tr->trans('form.settings.email_label', [], 'users'),
            ])
            ->add('roles', ChoiceType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                                            'message' => $this->tr->trans('form.settings.roles_not_blank', [], 'users'),
                                        ]),
                ],
                'choices' => $this->getRolesFormChoices(),
                'multiple' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                    'placeholder' => $this->tr->trans('form.settings.roles_place_holder', [], 'users'),
                ],
                'label' => $this->tr->trans('form.settings.roles_label', [], 'users'),
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank([
                                     'message' => $this->tr->trans('reset_password.form.error_blank'),
                                 ]),
                    new Length([
                                   'min' => 12,
                                   'minMessage' => $this->tr->trans('reset_password.form.error_min'),
                                   // max length allowed by Symfony for security reasons
                                   'max' => 4096,
                               ]),
                    new PasswordStrength(),
                    new NotCompromisedPassword(),
                ],
                'attr' => [
                    'autocomplete' => 'new-password',
                    'autofocus' => true,
                    'class' => 'form-control',
                    'placeholder' => 'reset_password.form.password',
                ],
                'label' => $options['label'] ? 'reset_password.form.password' : false,
            ])
            ->add('confirmPassword', PasswordType::class, [
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                    'placeholder' => 'reset_password.form.confirm_password',
                ],
                'label' => $options['label'] ? 'reset_password.form.confirm_password' : false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }

    private function getRolesFormChoices(): array
    {
        return [
            $this->tr->trans('roles.ROLE_USER', [], 'roles') => 'ROLE_USER',
            $this->tr->trans('roles.ROLE_ADMIN', [], 'roles') => 'ROLE_ADMIN',
            $this->tr->trans('roles.ROLE_SUPER_ADMIN', [], 'roles') => 'ROLE_SUPER_ADMIN',
        ];
    }
}
