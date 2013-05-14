<?php
namespace Entity\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="content_tag")
 * @ORM\Entity(repositoryClass="Entity\Content\Repository\TagRepository")
 * @Gedmo\TranslationEntity(class="Entity\Content\TagTranslation")
 */
class Tag
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
	 * @Gedmo\Translatable
     * @ORM\Column(length=64)
     */
    private $name;

    /**
     * @ORM\OneToMany(
     *   targetEntity="TagTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    private $translations;

    /**
     * @ORM\ManyToMany(targetEntity="Entity\Content", mappedBy="tags")
     */
    private $contents;


    public function __construct()
    {
		$this->translations = new ArrayCollection();
		$this->contents = new ArrayCollection();
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(TagTranslation $t)
    {
        if (!$this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }
    }

    public function getContents()
    {
        return $this->contents;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function fromArray($data)
    {
		if (isset($data['name'])) {
			$this->name = $data['name'];
		}
	}

    public function toArray()
    {
		return array(
			'name'	 => $this->name
		);
	}
}