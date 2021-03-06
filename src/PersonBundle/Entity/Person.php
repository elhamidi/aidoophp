<?php

namespace PersonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Person
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Person
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
     * @ORM\Column(name="zipCode", type="string", length=10)
     */
    private $zipCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="birthYear", type="integer")
     */
    private $birthYear;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string")
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=2)
     */

    private $language;

    /**
     * @ORM\Column(type="string",  options={"default" : "nopic"})
     *
     * @Assert\NotBlank(message="Please, upload the product brochure as a jpg , gif or png file.")
     */
    private $image;

    /**
     *@var integer
     * @ORM\Column(name ="image_enabled", type="integer",  options={"default" :0})
     *
     *
     */

    private  $imageEnabled ;

    /**
     * @return mixed
     */
    public function getImageEnabled()
    {
        return $this->imageEnabled;
    }

    /**
     * @param mixed $imageEnabled
     */
    public function setImageEnabled($imageEnabled)
    {
        $this->imageEnabled = $imageEnabled;
    }


    /**
     * @var
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User" , cascade={"persist"} , fetch="EAGER")
     */
    private $user;







    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }






    /**
     * @param string $accountNumber
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="accountNumber", type="string")
     */
    private $accountNumber;


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
     * Set zipCode
     *
     * @param string $zipCode
     * @return Person
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string 
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set birthYear
     *
     * @param integer $birthYear
     * @return Person
     */
    public function setBirthYear($birthYear)
    {
        $this->birthYear = $birthYear;

        return $this;
    }

    /**
     * Get birthYear
     *
     * @return integer 
     */
    public function getBirthYear()
    {
        return $this->birthYear;
    }
}
