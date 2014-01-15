<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Radix\RecruitmentBundle\Entity\Config;
use Radix\RecruitmentBundle\Entity\Job;
use Radix\RecruitmentBundle\Entity\Watchdog;

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
      $applymail = $config[0]->getApplymail();
   
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
	    $jobs_xml = array();
	    $file = file_get_contents($xmlurl, false, $context) or die('Kan niet laden');    
	    $xml = simplexml_load_string($file);
	    foreach ($xml->$xmlroot as $item) {
	      $jobs_xml[(string)$item->$mapping['guid']] = array(
	        'title' => (string)$item->$mapping['title'],
	        'description' => (string)$item->$mapping['description'],
	        'industry' => isset($mapping['industry']) ? (string)$item->$mapping['industry']: '',
	        'location' => isset($mapping['location']) ? (string)$item->$mapping['location']: '', 
	        'source' => 'import',
	      );
	    }

      /* We get all the current jobs */
      $jobs_original = $this->getDoctrine()
        ->getRepository('RadixRecruitmentBundle:Job')
        ->findBy(array('accountid' => $accountid));

      $jobs_db = array();
      foreach ($jobs_original as $job_original) {
        $guid = $job_original->getGuid();
        $title = $job_original->getTitle();
        $description = $job_original->getDescription();
        $industry = $job_original->getIndustry();
        $location = $job_original->getLocation();
        $source = $job_original->getSource();
        $jobs_db[$guid] = array(
          'title' => $title,
          'description' => $description,
          'industry' => $industry,
          'location' => $location,
          'source' => $source,
        );
      }

      /* TODO: optimize this */
      /* Define the new jobs to be added */
      $jobs_to_add = array();
      foreach ($jobs_xml as $guid_xml => $job_xml) {
        $add = TRUE;
        foreach ($jobs_db as $guid_db => $job_db) {
          if ($guid_xml == $guid_db) {
            $add = FALSE;
          }
        }
        if ($add) {
          $jobs_to_add[] = $guid_xml;
        }
      }

     /* Define the jobs to be removed */
     $jobs_to_remove = array();
     foreach ($jobs_db as $guid_db => $job_db) {
       // only for imported jobs
       if ($job_db['source'] == 'import') {
         $remove = TRUE;
         foreach ($jobs_xml as $guid_xml => $job_xml) {
           if ($guid_db == $guid_xml) {
             $remove = FALSE;
           }
         }
         if ($remove) {
           $jobs_to_remove[] = $guid_db;
         }
       }
     }

     /* Add the new jobs */
     foreach ($jobs_to_add as $guid) {
       $job_xml = $jobs_xml[$guid];
       $job = new Job();
       $job->setTitle($job_xml['title']);
       $job->setDescription($job_xml['description']);
       $job->setIndustry($job_xml['industry']);
       $job->setLocation($job_xml['location']);
       $job->setGuid($guid);
       $job->setSource($job_xml['source']);
       $job->setCreated(time());
       $job->setAccountid($accountid);
       $job->setApplymail($applymail);
       
       $em = $this->getDoctrine()->getManager();
       $em->persist($job);
       $em->flush();
     }
     
     /* Add the new jobs */
     foreach ($jobs_to_remove as $guid) {
     
       $job = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:job')
         ->findBy(array('guid' => $guid));

       $em = $this->getDoctrine()->getManager();
       $em->remove($job[0]);
       $em->flush();
		 }


   
      $custom = "Aantal jobs toegevoegd: " . count($jobs_to_add). "<br />"
        . "Aantal jobs verwijderd: " . count($jobs_to_remove);
   
      // Add the watchdog text - currently hardcoded - TODO: move this to a "log" function or so
      $watchdog = new Watchdog();
      $watchdog->setAccountid($accountid);
      $watchdog->setType('notice');
      $watchdog->setCreated(time());
      $watchdog->setMessage('Import voor ' . $accountid . ' uitgevoerd. ' . count($jobs_to_add) . ' jobs toegevoegd; ' . count($jobs_to_remove) . ' jobs verwijderd;');

      $em = $this->getDoctrine()->getManager();
      $em->persist($watchdog);
      $em->flush();

   
      return $this->render('RadixRecruitmentBundle:Import:import.html.twig', array('custom' => $custom));
    }
}
