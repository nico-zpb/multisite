<?php

namespace Sites\Admin\Zoo\FototekBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ZFImage
 *
 * @ORM\Table(name="zoo_fototek_images")
 * @ORM\Entity(repositoryClass="Sites\Admin\Zoo\FototekBundle\Entity\ZFImageRepository")
 */
class ZFImage
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
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"name"})
     *
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="integer", nullable=true)
     */
    private $width;

    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer", nullable=true)
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=10, nullable=true)
     */
    private $extension;

    /**
     * @var integer
     *
     * @ORM\Column(name="count_original", type="integer", nullable=true)
     */
    private $countOriginal;

    /**
     * @var integer
     *
     * @ORM\Column(name="count_mr", type="integer", nullable=true)
     */
    private $countMr;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_archived", type="boolean")
     */
    private $isArchived;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_displayed", type="boolean")
     */
    private $isDisplayed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="displayed_at", type="datetime", nullable=true)
     */
    private $displayedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="archived_at", type="datetime", nullable=true)
     */
    private $archivedAt;

    /**
     * @var integer
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="Sites\Admin\Zoo\FototekBundle\Entity\ZFCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    private $category;

    /**
     * @var string
     * @ORM\Column(name="absolute_path", type="text", nullable=false)
     */
    private $absolutePath;

    /**
     * @var string
     * @ORM\Column(name="web_path", type="text", nullable=false)
     *
     */
    private $webPath;

    /**
     * @var string
     * @ORM\Column(name="legend", type="text", nullable=true)
     */
    private $legend;

    /**
     * @var string
     * @ORM\Column(name="coypright", type="string")
     */
    private $copyright;

    public function __construct()
    {
        $this->setIsArchived(false);
        $this->setIsDisplayed(false);
    }

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
     * @return ZFImage
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
     * Set slug
     *
     * @param string $slug
     * @return ZFImage
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return ZFImage
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return ZFImage
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return ZFImage
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set extension
     *
     * @param string $extension
     * @return ZFImage
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set countOriginal
     *
     * @param integer $countOriginal
     * @return ZFImage
     */
    public function setCountOriginal($countOriginal)
    {
        $this->countOriginal = $countOriginal;

        return $this;
    }

    /**
     * Get countOriginal
     *
     * @return integer
     */
    public function getCountOriginal()
    {
        return $this->countOriginal;
    }

    /**
     * Set countMr
     *
     * @param integer $countMr
     * @return ZFImage
     */
    public function setCountMr($countMr)
    {
        $this->countMr = $countMr;

        return $this;
    }

    /**
     * Get countMr
     *
     * @return integer
     */
    public function getCountMr()
    {
        return $this->countMr;
    }

    /**
     * Set isArchived
     *
     * @param boolean $isArchived
     * @return ZFImage
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * Get isArchived
     *
     * @return boolean
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * Set isDisplayed
     *
     * @param boolean $isDisplayed
     * @return ZFImage
     */
    public function setIsDisplayed($isDisplayed)
    {
        $this->isDisplayed = $isDisplayed;

        return $this;
    }

    /**
     * Get isDisplayed
     *
     * @return boolean
     */
    public function getIsDisplayed()
    {
        return $this->isDisplayed;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ZFImage
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set displayedAt
     *
     * @param \DateTime $displayedAt
     * @return ZFImage
     */
    public function setDisplayedAt($displayedAt)
    {
        $this->displayedAt = $displayedAt;

        return $this;
    }

    /**
     * Get displayedAt
     *
     * @return \DateTime
     */
    public function getDisplayedAt()
    {
        return $this->displayedAt;
    }

    /**
     * Set archivedAt
     *
     * @param \DateTime $archivedAt
     * @return ZFImage
     */
    public function setArchivedAt($archivedAt)
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }

    /**
     * Get archivedAt
     *
     * @return \DateTime
     */
    public function getArchivedAt()
    {
        return $this->archivedAt;
    }

    /**
     * Set category
     *
     * @param ZFCategory $category
     * @return ZFImage
     */
    public function setCategory(ZFCategory $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return ZFCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return ZFImage
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
     * Set absolutePath
     *
     * @param string $absolutePath
     * @return ZFImage
     */
    public function setAbsolutePath($absolutePath)
    {
        $this->absolutePath = $absolutePath;

        return $this;
    }

    /**
     * Get absolutePath
     *
     * @return string 
     */
    public function getAbsolutePath()
    {
        return $this->absolutePath;
    }

    /**
     * Set webPath
     *
     * @param string $webPath
     * @return ZFImage
     */
    public function setWebPath($webPath)
    {
        $this->webPath = $webPath;

        return $this;
    }

    /**
     * Get webPath
     *
     * @return string 
     */
    public function getWebPath()
    {
        return $this->webPath;
    }

    /**
     * Set legend
     *
     * @param string $legend
     * @return ZFImage
     */
    public function setLegend($legend)
    {
        $this->legend = $legend;

        return $this;
    }

    /**
     * Get legend
     *
     * @return string 
     */
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     * Set copyright
     *
     * @param string $copyright
     * @return ZFImage
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;

        return $this;
    }

    /**
     * Get copyright
     *
     * @return string 
     */
    public function getCopyright()
    {
        return $this->copyright;
    }
}
