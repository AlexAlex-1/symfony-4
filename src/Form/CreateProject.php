<?php
namespace App\Form;
use App\Entity\Projects;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CreateProject extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){

    $builder
      ->add('UserId', HiddenType::class)
      ->add('Name')
      ->add('Submit', SubmitType::class);
  }
}

