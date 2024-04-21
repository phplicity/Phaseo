<?php

namespace App\Form\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class ChangePasswordFormType extends AbstractType
{
    public function __construct(
        private TranslatorInterface $tr
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                'label' => false,
            ])
            ->add('confirmPassword', PasswordType::class, [
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                    'placeholder' => 'reset_password.form.confirm_password',
                ],
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
