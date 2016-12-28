<?php
/**
 * Created by PhpStorm.
 * User: Natalie Piron
 * Date: 10/11/2016
 * Time: 19:22
 */

namespace AnnonceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use  Doctrine\ORM\Mapping\ManyToOne;


/**
 * Annonce
 *
 * @ORM\Table(name="zip_code_be")
 * @ORM\Entity
 */
class ZipCode
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
     * @ORM\Column (name="zip_code"  ,type="string", length=60)
     */
    private $zipCode;

    /**
     * @var string
     * @ORM\Column (name="zip_name"  ,type="string", length=60)
     */
    private $zipName;

    /**
     * @var float
     * @ORM\Column (name="Lat" , type="float", precision=8 , scale=6 )
     */
    private $latitude;

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getZipName()
    {
        return $this->zipName;
    }

    /**
     * @param string $zipName
     */
    public function setZipName($zipName)
    {
        $this->zipName = $zipName;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @var float
     * @ORM\Column (name="Lon" , type="float" , precision=8 , scale=6)
     */
    private $longitude;



}