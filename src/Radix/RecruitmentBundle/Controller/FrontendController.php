<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontendController extends Controller
{
    public function frontendAction($accountid)
    {
        return $this->render('RadixRecruitmentBundle:Frontend:frontend.html.twig', array('account' => $accountid));
    }
}
