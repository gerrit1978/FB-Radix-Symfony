<?php

/*
 * Copyright 2014 - Gerrit Vos & Arne Vanvlasselaer
 * Radix Recruitment
 */

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Radix\RecruitmendBundle\Entity\Config;
use Radix\RecruitmendBundle\Entity\Job;

class CronController extends Controller
{
    public function cronAction($accountid) {

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid);

      /**** SERVICES END ****/
      
      $config = $carrot['config'];
      
      $accesstoken = $config->getAccesstoken();
      
      if (!$accesstoken) {
        exit('no access token - cannot post');
      } 
      
      // get jobs that have not yet been posted to wall
      $em = $this->getDoctrine()->getEntityManager();
      
			$query = $em->createQuery('SELECT j FROM Radix\RecruitmentBundle\Entity\Job j WHERE j.accountid = :accountid AND j.autopost= :autopost')
			  ->setParameter('accountid', $accountid)
			  ->setParameter('autopost', 0);
			$jobs_not_auto_posted = $query->getResult();

      if (count($jobs_not_auto_posted)) {
      
        $job = $jobs_not_auto_posted[0];
      
        $helper = $this->get('radix.helper.facebook');
        
        $action_link = $carrot['config']->getPageurl() . '?id=' . $carrot['config']->getPageid() . '&sk=app_600850943303218';
        
        $params = array(
          'message' => $job->getTitle() . ' #Jobs',
          'link' => 'http://fb.projects.radix-recruitment.be/job-redirect/' . $accountid . '/' . $job->getId(),
          'actions' => "{ 'name': 'view all jobs', 'link': '" . $action_link . "' }",
        );
        
        $helper->postFromCron($accountid, $carrot['config']->getPageid(), $carrot['config']->getAccesstoken(), $params);
        
        // and update this job hier
        $job->setAutopost(time());
        $em->persist($job);
        $em->flush();
        
      }
      
      exit('Einde cron controller');
      

      
/*
      $code_url = "https://www.facebook.com/dialog/oauth?client_id=600850943303218&redirect_uri=http://fb.projects.radix-recruitment.be/&scope=manage_pages,offline_access,publish_stream";
      $code = "AQDfIA-SOiumT-5oKmBDM_rh3859sinqHSLkjhShDaPUp-C2K-coXT7LNB9b6-bjaD85rVXaFLOwmjrfzLc_T5bpd-8ZFkMtwwzLzimMuqYCk169StcNhZDknkcLWpPBmCFufpzQA7J93ZJXIZe7JTiDTLIxZmg04iOrylkQNUsFKSzFKvhj3RAJOQLjggZ9P0cXeLSLcLEYp4i8yckqGeLKEKlEzS231mm8wDpqvp0bafHcxLEwtnniYK7CAEqH-O3MsROTPP1r8QttJzJYAEveXuAbxROTzcwr0YsPddC7jOHgyKz_Kp6YzFL8dRhxe-s#_=_";
      $url = "https://graph.facebook.com/oauth/access_token?client_id=600850943303218&redirect_uri=http://fb.projects.radix-recruitment.be/&client_secret=41938c8ed1d54041769cb346ffac04d2&code=" . $code;
*/

    }
}
