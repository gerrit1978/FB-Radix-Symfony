<?php

namespace Radix\RecruitmentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EducationType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('school', 'text', array('required' => FALSE, 'attr' => array('class' => 'school')))
      ->add('year', 'text', array('label' => 'Jaar van afstuderen', 'required' => FALSE, 'attr' => array('class' => 'year')))
      ->add('type', 'text', array('required' => FALSE, 'attr' => array('class' => 'type')));
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