<?php
namespace App\Form;
use App\Entity\Projects;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class editProject extends AbstractType{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('Name')
      ->add('Submit', SubmitType::class);
  }
}
