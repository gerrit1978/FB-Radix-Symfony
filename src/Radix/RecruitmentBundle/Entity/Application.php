<?php

namespace Radix\RecruitmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Application
 *
 * @ORM\Table("application")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks 
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $resumepath;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $resumefile;

    private $resumetemp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $coverpath;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $coverfile;

    private $covertemp;



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
    
    /**
     * Sets resume file.
     *
     * @param UploadedFile $resumefile
     */
    public function setResumefile(UploadedFile $file = null)
    {
        $this->resumefile = $file;
        // check if we have an old image path
        if (isset($this->resumepath)) {
            // store the old name to delete after the update
            $this->resumetemp = $this->resumepath;
            $this->resumepath = null;
        } else {
            $this->resumepath = 'initial';
        }
    }

    /**
     * Returns resume file.
     */
    public function getResumefile() {
      return $this->resumefile;
    }

    /**
     * Sets resume file.
     *
     * @param UploadedFile $resumefile
     */
    public function setCoverfile(UploadedFile $file = null)
    {
        $this->coverfile = $file;
        // check if we have an old image path
        if (isset($this->coverpath)) {
            // store the old name to delete after the update
            $this->covertemp = $this->coverpath;
            $this->coverpath = null;
        } else {
            $this->coverpath = 'initial';
        }
    }

    /**
     * Returns cover file.
     */
    public function getCoverfile() {
      return $this->coverfile;
    }




    public function getAbsolutePath($type)
    {
    
      if ($type == 'resume') {
        return null === $this->resumepath
            ? null
            : $this->getUploadRootDir($type).'/'.$this->resumepath;
      }
      if ($type == 'cover') {
        return null === $this->coverpath
            ? null
            : $this->getUploadRootDir($type).'/'.$this->coverpath;
      }
      
    }

    public function getWebPath($type)
    {
    
      if ($type == 'resume') {
        return null === $this->resumepath
            ? null
            : $this->getUploadDir($type).'/'.$this->resumepath;
      }
      if ($type == 'cover') {
        return null === $this->coverpath
            ? null
            : $this->getUploadDir($type).'/'.$this->coverpath;
      }
    }

    public function getPrivatePath($type)
    {
    
      if ($type == 'resume') {
        return null === $this->resumepath
            ? null
            : $this->getUploadRootDir($type).'/'.$this->resumepath;
      }
      if ($type == 'cover') {
        return null === $this->coverpath
            ? null
            : $this->getUploadRootDir($type).'/'.$this->coverpath;
      }
    }

    protected function getUploadRootDir($type)
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../attachments/'.$this->getUploadDir($type);
    }

    protected function getUploadDir($type)
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return $this->accountid . '/' . $type . '/';
    }


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {

        if (null !== $this->getResumefile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->resumepath = $filename.'.'.$this->getResumefile()->guessExtension();
        }

        if (null !== $this->getCoverfile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->coverpath = $filename.'.'.$this->getCoverfile()->guessExtension();
        }


    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if ($this->getResumefile() != null) {
	        // if there is an error when moving the file, an exception will
	        // be automatically thrown by move(). This will properly prevent
	        // the entity from being persisted to the database on error
	        $this->getResumefile()->move($this->getUploadRootDir('resume'), $this->resumepath);
	
	        // check if we have an old image
	        if (isset($this->resumetemp)) {
	            // delete the old image
	            unlink($this->getUploadRootDir('resume').'/'.$this->resumetemp);
	            // clear the temp image path
	            $this->resumetemp = null;
	        }
	        $this->resumefile = null;
        }

        if ($this->getCoverfile() != null) {
	        // if there is an error when moving the file, an exception will
	        // be automatically thrown by move(). This will properly prevent
	        // the entity from being persisted to the database on error
	        $this->getCoverfile()->move($this->getUploadRootDir('cover'), $this->coverpath);
	
	        // check if we have an old image
	        if (isset($this->covertemp)) {
	            // delete the old image
	            unlink($this->getUploadRootDir('cover').'/'.$this->covertemp);
	            // clear the temp image path
	            $this->covertemp = null;
	        }
	        $this->coverfile = null;
        }


    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($resumefile = $this->getAbsolutePath('resume')) {
            unlink($resumefile);
        }
        if ($coverfile = $this->getAbsolutePath('cover')) {
            unlink($coverfile);
        }
    }		
}