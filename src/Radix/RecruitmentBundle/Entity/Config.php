<?php

namespace Radix\RecruitmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Config
 *
 * @ORM\Table("config")
 * @ORM\Entity(repositoryClass="Radix\RecruitmentBundle\Entity\ConfigRepository")
 */
class Config
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
     * @ORM\Column(name="xmlurl", type="string", length=512)
     */
    private $xmlurl;

    /**
     * @var string
     *
     * @ORM\Column(name="xmluser", type="string", length=255)
     */
    private $xmluser;

    /**
     * @var string
     *
     * @ORM\Column(name="xmlpass", type="string", length=255)
     */
    private $xmlpass;

    /**
     * @var string
     *
     * @ORM\Column(name="xmlroot", type="string", length=255)
     */
    private $xmlroot;

    /**
     * @var string
     *
     * @ORM\Column(name="pageid", type="string", length=255)
     */
    private $pageid;

    /**
     * @var string
     *
     * @ORM\Column(name="pagetitle", type="string", length=255)
     */
    private $pagetitle;


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
     * Set accountid
     *
     * @param string $accountid
     * @return Config
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
     * Set xmlurl
     *
     * @param string $xmlurl
     * @return Config
     */
    public function setXmlurl($xmlurl)
    {
        $this->xmlurl = $xmlurl;
    
        return $this;
    }

    /**
     * Get xmlurl
     *
     * @return string 
     */
    public function getXmlurl()
    {
        return $this->xmlurl;
    }

    /**
     * Set xmluser
     *
     * @param string $xmluser
     * @return Config
     */
    public function setXmluser($xmluser)
    {
        $this->xmluser = $xmluser;
    
        return $this;
    }

    /**
     * Get xmluser
     *
     * @return string 
     */
    public function getXmluser()
    {
        return $this->xmluser;
    }

    /**
     * Set xmlpass
     *
     * @param string $xmlpass
     * @return Config
     */
    public function setXmlpass($xmlpass)
    {
        $this->xmlpass = $xmlpass;
    
        return $this;
    }

    /**
     * Get xmlpass
     *
     * @return string 
     */
    public function getXmlpass()
    {
        return $this->xmlpass;
    }

    /**
     * Set xmlroot
     *
     * @param string $xmlroot
     * @return Config
     */
    public function setXmlroot($xmlroot)
    {
        $this->xmlroot = $xmlroot;
    
        return $this;
    }

    /**
     * Get xmlroot
     *
     * @return string 
     */
    public function getXmlroot()
    {
        return $this->xmlroot;
    }

    /**
     * Set pageid
     *
     * @param string $pageid
     * @return Config
     */
    public function setPageid($pageid)
    {
        $this->pageid = $pageid;
    
        return $this;
    }

    /**
     * Get pageid
     *
     * @return string 
     */
    public function getPageid()
    {
        return $this->pageid;
    }

    /**
     * Set pagetitle
     *
     * @param string $pagetitle
     * @return Config
     */
    public function setPagetitle($pagetitle)
    {
        $this->pagetitle = $pagetitle;
    
        return $this;
    }

    /**
     * Get pagetitle
     *
     * @return string 
     */
    public function getPagetitle()
    {
        return $this->pagetitle;
    }
    
}
