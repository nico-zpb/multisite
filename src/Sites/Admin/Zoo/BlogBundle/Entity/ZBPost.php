<?php

namespace Sites\Admin\Zoo\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ZBPost
 *
 * @ORM\Table(name="zoo_blog_posts")
 * @ORM\Entity(repositoryClass="Sites\Admin\Zoo\BlogBundle\Entity\ZBPostRepository")
 */
class ZBPost
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="excerpt", type="text",nullable=true)
     */
    private $excerpt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDraft", type="boolean")
     */
    private $isDraft;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPublished", type="boolean")
     */
    private $isPublished;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDropped", type="boolean")
     */
    private $isDropped;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDelayed", type="boolean")
     */
    private $isDelayed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publishedAt", type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isFront", type="boolean")
     */
    private $isFront;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isFrontBN", type="boolean")
     */
    private $isFrontBN;

    /**
     * @ORM\ManyToOne(targetEntity="Sites\Admin\Zoo\BlogBundle\Entity\ZBCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    private $category;

    /**
     * @var \DateTime
     * @ORM\Column(name="toBePublishedAt", type="datetime", nullable=true)
     */
    private $toBePublishedAt;

    /**
     * @ORM\ManyToMany(targetEntity="Sites\Admin\Zoo\BlogBundle\Entity\ZBTag", inversedBy="posts")
     * @ORM\JoinTable(name="zoo_blog_posts_tags")
     */
    private $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->isDraft = true;
        $this->isPublished = false;
        $this->isDelayed = false;
        $this->isDropped = false;
        $this->isFront = false;
        $this->isFrontBN = false;
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
     * Set title
     *
     * @param string $title
     * @return ZBPost
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
     * Set slug
     *
     * @param string $slug
     * @return ZBPost
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
     * Set excerpt
     *
     * @param string $excerpt
     * @return ZBPost
     */
    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    /**
     * Get excerpt
     *
     * @return string
     */
    public function getExcerpt()
    {
        return $this->excerpt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ZBPost
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
     * Set body
     *
     * @param string $body
     * @return ZBPost
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set isDraft
     *
     * @param boolean $isDraft
     * @return ZBPost
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    /**
     * Get isDraft
     *
     * @return boolean
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     * @return ZBPost
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set isDropped
     *
     * @param boolean $isDropped
     * @return ZBPost
     */
    public function setIsDropped($isDropped)
    {
        $this->isDropped = $isDropped;

        return $this;
    }

    /**
     * Get isDropped
     *
     * @return boolean
     */
    public function getIsDropped()
    {
        return $this->isDropped;
    }

    /**
     * Set isDelayed
     *
     * @param boolean $isDelayed
     * @return ZBPost
     */
    public function setIsDelayed($isDelayed)
    {
        $this->isDelayed = $isDelayed;

        return $this;
    }

    /**
     * Get isDelayed
     *
     * @return boolean
     */
    public function getIsDelayed()
    {
        return $this->isDelayed;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     * @return ZBPost
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set isFront
     *
     * @param boolean $isFront
     * @return ZBPost
     */
    public function setIsFront($isFront)
    {
        $this->isFront = $isFront;

        return $this;
    }

    /**
     * Get isFront
     *
     * @return boolean
     */
    public function getIsFront()
    {
        return $this->isFront;
    }

    /**
     * Set category
     *
     * @param ZBCategory $category
     * @return ZBPost
     */
    public function setCategory(ZBCategory $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return ZBCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set toBePublishedAt
     *
     * @param \DateTime $toBePublishedAt
     * @return ZBPost
     */
    public function setToBePublishedAt($toBePublishedAt)
    {
        $this->toBePublishedAt = $toBePublishedAt;

        return $this;
    }

    /**
     * Get toBePublishedAt
     *
     * @return \DateTime
     */
    public function getToBePublishedAt()
    {
        return $this->toBePublishedAt;
    }

    /**
     * Set isFrontBN
     *
     * @param boolean $isFrontBN
     * @return ZBPost
     */
    public function setIsFrontBN($isFrontBN)
    {
        $this->isFrontBN = $isFrontBN;

        return $this;
    }

    /**
     * Get isFrontBN
     *
     * @return boolean 
     */
    public function getIsFrontBN()
    {
        return $this->isFrontBN;
    }

    /**
     * Add tags
     *
     * @param ZBTag $tags
     * @return ZBPost
     */
    public function addTag(ZBTag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param ZBTag $tags
     */
    public function removeTag(ZBTag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }
}
