<?php

namespace Radix\RecruitmentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WorkType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('employer', 'text', array('label' => 'Werkgever', 'required' => FALSE, 'attr' => array('class' => 'employer')))
      ->add('location', 'text', array('label' => 'Locatie', 'required' => FALSE, 'attr' => array('class' => 'location')))
      ->add('position', 'text', array('label' => 'Functie', 'required' => FALSE, 'attr' => array('class' => 'position')))
      ->add('description', 'text', array('label' => 'Beschrijving', 'required' => FALSE, 'attr' => array('class' => 'description')))
      ->add('startdate', 'text', array('label' => 'Startdatum', 'required' => FALSE, 'attr' => array('class' => 'startdate')))
      ->add('enddate', 'text', array('label' => 'Einddatum', 'required' => FALSE, 'attr' => array('class' => 'enddate')));
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