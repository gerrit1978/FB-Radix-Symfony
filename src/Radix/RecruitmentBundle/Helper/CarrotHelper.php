<?php

namespace Radix\RecruitmentBundle\Helper;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarrotHelper {
  
  private $doctrine;
  
  public function __construct($doctrine) {
    $this->doctrine = $doctrine;
  }
  
  public function bootstrap($accountid) {

      /* We get the config parameters */
      $config = $this->doctrine
        ->getRepository('RadixRecruitmentBundle:Config')
        ->findBy(array('accountid' => $accountid));
 
      if (!$config) {
        throw new NotFoundHttpException('No config found for this accountid.');
      }
      
      /* Other Bootstrap stuff comes here */

  }
  
}