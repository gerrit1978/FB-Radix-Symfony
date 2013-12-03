<?php

namespace Radix\RecruitmentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WorkType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('employer')
      ->add('location', 'text', array('required' => FALSE))
      ->add('position', 'text', array('required' => FALSE))
      ->add('description', 'text', array('required' => FALSE))
      ->add('startdate', 'text', array('required' => FALSE))
      ->add('enddate', 'text', array('required' => FALSE));
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