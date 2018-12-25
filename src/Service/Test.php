<?php
namespace App\Service;

use App\Form\SeacrhType;
use Symfony\Component\Form\FormFactoryInterface;

class Test
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }
    
    public function getForm()
    {
        $form = $this->formFactory->create(SeacrhType::class);
       // var_dump($form);
      //  $forms = array("formTag"=>$form->createView());
        return $form->createView();
    }
}
