<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire général',
                'attr'  => ['rows' => 5, 'placeholder' => 'Ton retour global sur ce code…'],
            ])
            ->add('status', ChoiceType::class, [
                'label'   => 'Verdict',
                'choices' => [
                    '✅ Approuvé'       => Review::STATUS_APPROVED,
                    '🔧 À retravailler' => Review::STATUS_NEEDS_WORK,
                ],
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Review::class]);
    }
}
