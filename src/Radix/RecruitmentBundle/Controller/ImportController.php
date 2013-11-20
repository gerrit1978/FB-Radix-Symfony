<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Radix\RecruitmentBundle\Entity\Config;

class ImportController extends Controller
{
    public function importAction($accountid)
    {
    
      /* We get the config parameters (XML URL/USER/PASS) */
      $config = $this->getDoctrine()
        ->getRepository('RadixRecruitmentBundle:Config')
        ->findBy(array('accountid' => $accountid));
 
      if (!$config) {
        throw $this->createNotFoundException('No config found for this accountid.');
      }

      $xmlurl = $config[0]->getXmlurl();
      $xmluser = $config[0]->getXmluser();
      $xmlpass = $config[0]->getXmlpass();
      $xmlroot = $config[0]->getXmlroot();
   
	    $context = stream_context_create(
	      array(
	        'http' => array(
	          'header'  => "Authorization: Basic " . base64_encode($xmluser.":".$xmlpass)
	        )
	      )
	    ); 
	    
	    /* We get the mapping */
	    $mapping = array();
	    $mappings = $this->getDoctrine()
	      ->getRepository('RadixRecruitmentBundle:Mapping')
	      ->findBy(array('accountid' => $accountid));
	    
	    if (!$mappings) {
	      throw $this->createNotFoundException('No mapping found for this accountid.');
	    }
	    
      if (is_array($mappings)) {
        foreach ($mappings as $row) {
          $mapping[$row->getTarget()] = $row->getSrc();
        }
      }
      
	    
	    /* We import the XML source file */
	    $file = file_get_contents($xmlurl, false, $context) or die('Kan niet laden');    
	    $xml = simplexml_load_string($file);
	    foreach ($xml->$xmlroot as $item) {
	      print "nieuwe job<br />";
	    }
	    exit();


      /* We get all the current jobs */
      $jobs_original = $this->getDoctrine()
        ->getRepository('RadixRecruitmentBundle:Job')
        ->findBy(array('accountid' => $accountid));




   
      $custom = "XMLURL: " . $xmlurl . "<br />";
   
      return $this->render('RadixRecruitmentBundle:Import:import.html.twig', array('custom' => $custom));
    }
}
