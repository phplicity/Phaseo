<?php

namespace App\Form\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserEditFormType extends AbstractType
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
            ->add('roles', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                                            'message' => $this->tr->trans('form.settings.roles_not_blank', [], 'users'),
                                        ]),
                ],
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                    'placeholder' => $this->tr->trans('form.settings.roles_place_holder', [], 'users'),
                ],
                'label' => $this->tr->trans('form.settings.roles_label', [], 'users'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}