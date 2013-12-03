<?php

namespace Radix\RecruitmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Work
 *
 * @ORM\Table("work")
 * @ORM\Entity
 */
class Work
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
     * @ORM\Column(name="employer", type="string", length=255)
     */
    private $employer;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=512)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="startdate", type="string", length=255)
     */
    private $startdate;

    /**
     * @var string
     *
     * @ORM\Column(name="enddate", type="string", length=255)
     */
    private $enddate;


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
     * Set employer
     *
     * @param string $employer
     * @return Work
     */
    public function setEmployer($employer)
    {
        $this->employer = $employer;
    
        return $this;
    }

    /**
     * Get employer
     *
     * @return string 
     */
    public function getEmployer()
    {
        return $this->employer;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return Work
     */
    public function setLocation($location)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return Work
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return string 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Work
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set startdate
     *
     * @param string $startdate
     * @return Work
     */
    public function setStartdate($startdate)
    {
        $this->startdate = $startdate;
    
        return $this;
    }

    /**
     * Get startdate
     *
     * @return string 
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * Set enddate
     *
     * @param string $enddate
     * @return Work
     */
    public function setEnddate($enddate)
    {
        $this->enddate = $enddate;
    
        return $this;
    }

    /**
     * Get enddate
     *
     * @return string 
     */
    public function getEnddate()
    {
        return $this->enddate;
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
