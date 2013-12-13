<?php

namespace Radix\RecruitmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Application
 *
 * @ORM\Table("application")
 * @ORM\Entity
 */
class Application
{

	  public function __construct() {
	    $this->work = new ArrayCollection();
	    $this->education = new ArrayCollection();
	  }

	  private $work;
	  private $education;


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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var integer
     *
     * @ORM\Column(name="accountid", type="integer")
     */
    private $accountid;

    /**
     * @var integer
     *
     * @ORM\Column(name="jobid", type="integer")
     */
    private $jobid;

    /**
     * @var string
     *
     * @ORM\Column(name="created", type="string", length=20)
     */
    private $created;


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
     * Set name
     *
     * @param string $name
     * @return Application
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Application
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Application
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set accountid
     *
     * @param integer $accountid
     * @return Application
     */
    public function setAccountid($accountid)
    {
        $this->accountid = $accountid;
    
        return $this;
    }

    /**
     * Get accountid
     *
     * @return integer 
     */
    public function getAccountid()
    {
        return $this->accountid;
    }

    /**
     * Set jobid
     *
     * @param integer $jobid
     * @return Application
     */
    public function setJobid($jobid)
    {
        $this->jobid = $jobid;
    
        return $this;
    }

    /**
     * Get jobid
     *
     * @return integer 
     */
    public function getJobid()
    {
        return $this->jobid;
    }
 
    /**
     * Set created
     *
     * @param string $created
     * @return Application
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return string 
     */
    public function getCreated()
    {
        return $this->created;
    }


	  public function getWork() {
	    return $this->work;
	  }
	  
	  public function setWork($work) {
	    $this->work = $work;
	  }
	  
	  public function getEducation() {
	    return $this->education;
	  }
	  
	  public function setEducation($education) {
	    $this->education = $education;
	  }
    


}
