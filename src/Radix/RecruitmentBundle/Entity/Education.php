<?php

namespace Radix\RecruitmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Education
 *
 * @ORM\Table("education")
 * @ORM\Entity
 */
class Education
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="accountid", type="string", length=255)
     */
    private $accountid;

    /**
     * @var string
     *
     * @ORM\Column(name="jobid", type="string", length=255)
     */
    private $jobid;

    /**
     * @var string
     *
     * @ORM\Column(name="applicationid", type="string", length=255)
     */
    private $applicationid;


    /**
     * @var string
     *
     * @ORM\Column(name="school", type="string", length=255)
     */
    private $school;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=255)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set school
     *
     * @param string $school
     * @return Education
     */
    public function setSchool($school)
    {
        $this->school = $school;
    
        return $this;
    }

    /**
     * Get school
     *
     * @return string 
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * Set year
     *
     * @param string $year
     * @return Education
     */
    public function setYear($year)
    {
        $this->year = $year;
    
        return $this;
    }

    /**
     * Get year
     *
     * @return string 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Education
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set accountid
     *
     * @param string $accountid
     * @return Work
     */
    public function setAccountid($accountid)
    {
        $this->accountid = $accountid;
    
        return $this;
    }

    /**
     * Get accountid
     *
     * @return string 
     */
    public function getAccountid()
    {
        return $this->accountid;
    }


    /**
     * Set jobid
     *
     * @param string $jobid
     * @return Work
     */
    public function setJobid($jobid)
    {
        $this->jobid = $jobid;
    
        return $this;
    }

    /**
     * Get jobid
     *
     * @return string 
     */
    public function getJobid()
    {
        return $this->jobid;
    }


    /**
     * Set applicationid
     *
     * @param string $applicationid
     * @return Work
     */
    public function setApplicationid($applicationid)
    {
        $this->applicationid = $applicationid;
    
        return $this;
    }

    /**
     * Get applicationid
     *
     * @return string 
     */
    public function getApplicationid()
    {
        return $this->applicationid;
    }

    
}
