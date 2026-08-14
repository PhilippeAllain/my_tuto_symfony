<?php

namespace App\Form;

use App\DTO\ContactDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use function Symfony\Component\Translation\t;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'empty_data' => '',
                'label' => t('contactForm.name')
            ])
            ->add('email', EmailType::class, [
                'empty_data' => '',
                'label' => t('contactForm.email')
            ])
            ->add('message', TextareaType::class, [
                'empty_data' => '',
                'label' => t('contactForm.message')
            ])
            ->add('Save', SubmitType::class, [
                'label' => t('contactForm.save')
            ])
            ->add('service', ChoiceType::class, [
                'choices'  => [
                    'Compta' => 'compta@demo.fr',
                    'Support' => 'support@demo.fr',
                    'Marketing' => 'marketing@demo.fr',
                ],
                'label' => t('contactForm.service')
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactDTO::class,
        ]);
    }
}
