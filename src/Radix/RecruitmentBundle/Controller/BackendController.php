<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BackendController extends Controller
{
    public function backendAction($accountid)
    {
        return $this->render('RadixRecruitmentBundle:Backend:backend.html.twig', array('account' => $accountid));
    }
}
