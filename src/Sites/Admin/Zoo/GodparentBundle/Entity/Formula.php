<?php

namespace Sites\Admin\Zoo\GodparentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Formula
 *
 * @ORM\Table(name="zoo_godparent_formulas")
 * @ORM\Entity(repositoryClass="Sites\Admin\Zoo\GodparentBundle\Entity\FormulaRepository")
 */
class Formula
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="integer", nullable=true)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     * @Gedmo\SortablePosition
     */
    private $position;

    /**
     * @ORM\Column(name="category", type="string", length=64)
     * @Gedmo\SortableGroup
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Gift", inversedBy="formulas")
     * @ORM\JoinTable(name="zoo_godparent_formulas_gifts")
     */
    private $gifts;


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
     * @return Formula
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
     * Set price
     *
     * @param integer $price
     * @return Formula
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Formula
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Formula
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
     * Constructor
     */
    public function __construct()
    {
        $this->gifts = new ArrayCollection();
        $this->category = "formula";
    }

    /**
     * Add gifts
     *
     * @param Gift $gifts
     * @return Formula
     */
    public function addGift(Gift $gifts)
    {
        $this->gifts[] = $gifts;

        return $this;
    }

    /**
     * Remove gifts
     *
     * @param Gift $gifts
     */
    public function removeGift(Gift $gifts)
    {
        $this->gifts->removeElement($gifts);
    }

    /**
     * Get gifts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGifts()
    {
        return $this->gifts;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return Formula
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
