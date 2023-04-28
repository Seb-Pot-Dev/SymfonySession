<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Session;
use App\Entity\Teacher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SessionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('dateStart', DateType::class, [
                'label' => 'Débute',
                'widget' => 'single_text'
            ])
            ->add('dateEnd', DateType::class, [
                'label' => 'Fini',
                'widget' => 'single_text'
            ])
            ->add('nbPlace', IntegerType::class, [
                'label' => 'Places'
            ])
            //Select ici
            ->add('course', EntityType::class, [
                'class' => Course::class,
                'label' => 'Formation'
            ])
            //Select ici
            ->add('teacher', EntityType::class, [
                'class' => Teacher::class,
                'label' => 'Prof'
            ])
            //Ajouter ceci pour avoir l'input
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
