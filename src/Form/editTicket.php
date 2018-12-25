<?php
namespace App\Form;

use App\Entity\Tickets;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class editTicket extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('Name')
            ->add('Description')
            ->add('AssigneeId')
            ->add('Status', ChoiceType::class, array(
            'choices'=>array(
            'New'=>'New',
            'In progress'=>'In progress',
            'Testing'=>'Testing',
            'Done'=>'Done',
            ),))
            ->add('Submit', SubmitType::class);
    }
}
