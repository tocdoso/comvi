<?php
namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="content")
 * @ORM\Entity(repositoryClass="Entity\Repository\ContentRepository")
 * @Gedmo\TranslationEntity(class="Entity\ContentTranslation")
 */
class Content
{
	static private $status_choices = array('Published', 'Unpublished', 'Trash');

	static public function getStatusChoices()
	{
		return static::$status_choices;
	}

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
	 * @Gedmo\Translatable
     * @ORM\Column(length=128)
     */
    private $title;

    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
	 * @Gedmo\Translatable
     * @ORM\Column(type="text")
     */
    private $description;

    /**
	 * @Gedmo\Translatable
     * @ORM\Column(type="text")
     */
    private $body;

    /** @ORM\Column(columnDefinition="ENUM('Published', 'Unpublished', 'Trash')") */
    private $status = 'Published';

    /** @ORM\Column(type="boolean") */
    private $featured = false;

    /**
     * @ORM\ManyToOne(targetEntity="Entity\Content\Category", inversedBy="contents")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $category;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
	 * @Gedmo\Timestampable(on="change", field="status", value="Published")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $published;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $created_by;

    /**
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    private $updated_by;

    /**
	 * @Gedmo\Blameable(on="change", field="status", value="Published")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="published_by", referencedColumnName="id")
     */
    private $published_by;

    /**
     * @ORM\OneToMany(
     *   targetEntity="ContentTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    private $translations;

    /**
     * @ORM\ManyToMany(targetEntity="Entity\Content\Tag", mappedBy="contents")
     */
    private $tags;

    public function __construct()
    {
		$this->translations = new ArrayCollection();
		$this->tags = new ArrayCollection();
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(ContentTranslation $t)
    {
        if (!$this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setStatus($status)
    {
        if (!in_array($status, static::getStatusChoices())) {
            throw new \InvalidArgumentException("Invalid status");
        }

        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setFeatured($featured)
    {
        $this->featured = $featured;
    }

    public function isFeatured()
    {
        return $this->featured;
    }

	public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function getCreatedBy()
    {
        return $this->created_by;
    }

    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    public function getPublishedBy()
    {
        return $this->published_by;
    }

    public function __toString()
    {
        return $this->title;
    }

    public function fromArray($data)
    {
		if (isset($data['title'])) {
			$this->title = $data['title'];
		}

		if (isset($data['slug'])) {
			$this->slug = $data['slug'];
		}

		if (isset($data['description'])) {
			$this->description = $data['description'];
		}

		if (isset($data['body'])) {
			$this->body = $data['body'];
		}

		if (isset($data['status'])) {
			$this->status = $data['status'];
		}

		if (isset($data['featured'])) {
			$this->featured = (bool) $data['featured'];
		}

	}

    public function toArray()
    {
		return array(
			'category'		=> isset($this->category) ? $this->category->getId() : null,
			'title'			=> $this->title,
			'slug'			=> $this->slug,
			'description'	=> $this->description,
			'body'			=> $this->body,
			'status'		=> $this->status,
			'featured'		=> $this->featured,
		);
	}
}