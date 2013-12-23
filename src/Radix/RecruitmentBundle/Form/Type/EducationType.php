<?php

namespace Radix\RecruitmentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EducationType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('school', 'text', array('required' => FALSE))
      ->add('year', 'text', array('label' => 'Jaar van afstuderen', 'required' => FALSE))
      ->add('type', 'text', array('required' => FALSE));
  }
  
  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
      'data_class' => 'Radix\RecruitmentBundle\Entity\Education',
    ));
  }
  
  public function getName() {
    return 'education';
  }

}