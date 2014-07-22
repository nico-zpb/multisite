<?php

namespace Sites\Admin\Zoo\GodparentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Gift
 *
 * @ORM\Table(name="zoo_godparent_gifts")
 * @ORM\Entity(repositoryClass="Sites\Admin\Zoo\GodparentBundle\Entity\GiftRepository")
 */
class Gift
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
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
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
     * @ORM\Column(name="notabene", type="text", nullable=true)
     */
    private $notabene;

    /**
     * @ORM\ManyToMany(targetEntity="Formula", mappedBy="gifts")
     */
    private $formulas;


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
     * @return Gift
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
     * Set description
     *
     * @param string $description
     * @return Gift
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
     * Set position
     *
     * @param integer $position
     * @return Gift
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
     * Set notabene
     *
     * @param string $notabene
     * @return Gift
     */
    public function setNotabene($notabene)
    {
        $this->notabene = $notabene;

        return $this;
    }

    /**
     * Get notabene
     *
     * @return string
     */
    public function getNotabene()
    {
        return $this->notabene;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->formulas = new ArrayCollection();
        $this->category = "gift";
    }

    /**
     * Add formulas
     *
     * @param Formula $formulas
     * @return Gift
     */
    public function addFormula(Formula $formulas)
    {
        $this->formulas[] = $formulas;

        return $this;
    }

    /**
     * Remove formulas
     *
     * @param Formula $formulas
     */
    public function removeFormula(Formula $formulas)
    {
        $this->formulas->removeElement($formulas);
    }

    /**
     * Get formulas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFormulas()
    {
        return $this->formulas;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return Gift
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
