<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SeacrhType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
       //     ->setAction($this->generateUrl('tickets_search'))
         //   ->setMethod('POST') 
            ->add('Name')
            ->add('Submit', SubmitType::class)
            ->getForm()
        ;
    }
}
