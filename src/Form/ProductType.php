<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false
            ])

            ->add('content', TextType::class, [
                'required' => false
            ])

            ->add('price', MoneyType::class,[
                'required' => false,
            ])

            ->add('picture', FileType::class,
                array('data_class' => null),
                [
                'required' => false,
                'label' => "Photo du produit",
                'mapped' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/jpg',
                            'image/svg'
                        ],
                        'mimeTypesMessage' => "Formats accéptés: Jpeg/png/gif/jpg/svg",
                        'maxSizeMessage' => "Votre fichier ne doit pas dépasser 5Mo"
                    ])
                ]
            ])

            ->add('category', EntityType::class,[
                'required' => false,
                'placeholder' => " --- Veuillez choisir une catégorie ---",
                'class' => Category::class,
                'choice_label' => function (Category $category){
                    return strtoupper($category->getName());
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            
        ]);
    }
}
