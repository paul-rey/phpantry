<?php

namespace App\Form;

use App\Entity\JobPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'placeholder' => 'job title',
                ],
                'label' => 'Job Title*',

            ])
            ->add('company', TextType::class, [
                'attr' => [
                    'placeholder' => 'company name',
                ],
                'label' => 'Company name*',
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Company logo (png, jpg, svg...)',
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'email to contact you'
                ],
                'label' => 'Email address*',
            ])
            ->add('location', TextType::class, [
                'attr' => [
                    'placeholder' => 'city'
                ],
                'label' => 'Location*',
            ])
            ->add('contract', ChoiceType::class, [
                'choices' => [
                    'full-time' => 'full-time',
                    'part-time' => 'part-time',
                    'internship' => 'internship',
                    'freelance' => 'freelance'
                ],
                'label' => 'Contract type*',
            ])
            ->add('experience', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 10,
                ],
                'label' => 'Year(s) of experience(s)',
            ])
            ->add('salary', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    '' => '',
                    '0-40,000' => '0-40,000',
                    '40-60,000' => '40-60,000',
                    '60-100,000' => '60-100,000',
                    '100,000+' => '100,000+'
                ],
                'label' => 'Salary',
            ])
            ->add('description', TextareaType::class, [
                'empty_data' => "",
                'required' => false,
                'attr' => [
                    'class' => 'editor',
                ],
                'label' => 'Job description*',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => JobPost::class,
        ]);
    }
}
