<?php

namespace Radix\RecruitmentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WorkType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('employer', 'text', array('label' => 'Werkgever', 'required' => FALSE))
      ->add('location', 'text', array('label' => 'Locatie', 'required' => FALSE))
      ->add('position', 'text', array('label' => 'Functie', 'required' => FALSE))
      ->add('description', 'text', array('label' => 'Beschrijving', 'required' => FALSE))
      ->add('startdate', 'text', array('label' => 'Startdatum', 'required' => FALSE))
      ->add('enddate', 'text', array('label' => 'Einddatum', 'required' => FALSE));
  }
  
  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
      'data_class' => 'Radix\RecruitmentBundle\Entity\Work',
    ));
  }
  
  public function getName() {
    return 'work';
  }

}