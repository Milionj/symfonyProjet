<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('slug')
            ->add('state', ChoiceType::class, [
                'choices' => array_flip(Post::STATES)
            ])
            ->add('createdAt', null, [
                'widget' => 'single_text'
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text'
            ])
            ->add('category', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true, 
                            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
