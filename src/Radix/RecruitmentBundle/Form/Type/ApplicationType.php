<?php

namespace Radix\RecruitmentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('name', 'text', array('label' => 'Naam'))
      ->add('city', 'text', array('label' => 'Woonplaats'))
      ->add('email', 'email', array('label' => 'E-mailadres'));
    $builder->add('work', 'collection', array(
      'type' => new WorkType(),
      'allow_add' => TRUE,
      'by_reference' => FALSE,
      'label' => FALSE,
    ));
    $builder->add('education', 'collection', array(
      'type' => new EducationType(),
      'allow_add' => TRUE,
      'by_reference' => FALSE,
      'label' => FALSE,
    ));
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
      'data_class' => 'Radix\RecruitmentBundle\Entity\Application',
    ));
  }
  
  public function getName() {
    return 'application';
  }

}