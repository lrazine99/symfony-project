<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'label' => "Nom",
            ])
            ->add('price', \Symfony\Component\Form\Extension\Core\Type\NumberType::class, [
                'label' => 'Prix',
            ])
            ->add('quantity', \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, [
                'label' => 'Quantité',
                ]
            )
            ->add('category', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'label' => 'Catégorie',
                'class' => \App\Entity\Category::class,
                'choice_label' => 'name',
                'attr' => [
                    'style' => 'margin-bottom: 20px;'
                ]
            ])
            ->add('Valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
