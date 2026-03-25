<?php

namespace App\Form;

use App\Entity\Snippet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SnippetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr'  => ['placeholder' => 'Décris ton snippet en une phrase'],
            ])
            ->add('language', ChoiceType::class, [
                'label'   => 'Langage',
                'choices' => array_flip(Snippet::LANGUAGES),
            ])
            ->add('code', TextareaType::class, [
                'label' => 'Code',
                'attr'  => [
                    'rows'        => 20,
                    'placeholder' => 'Colle ton code ici…',
                    'class'       => 'code-editor',
                ],
            ])
            ->add('tagsInput', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Tags',
                'attr'     => ['placeholder' => 'php, performance, boucle (séparés par des virgules)'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Snippet::class]);
    }
}
