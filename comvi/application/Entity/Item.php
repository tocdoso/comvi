<?php
namespace Entity;

//use Comvi\Core\URI;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="item")
 * @ORM\Entity(repositoryClass="Entity\Repository\ItemRepository")
 * @Gedmo\TranslationEntity(class="Entity\ItemTranslation")
 */
class Item
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
     * @Gedmo\Translatable
     * @ORM\Column(length=255)
     */
    private $url;

    /** @ORM\Column(type="boolean") */
    private $visible = true;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(type="integer")
     */
    private $root;

    /*
     * @ORM\ManyToOne(targetEntity="ItemGroup", inversedBy="items")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    //private $group;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $level;

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\OneToMany(
     *   targetEntity="ItemTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    private $translations;

    /**
     * @ORM\OneToMany(targetEntity="Module", mappedBy="item")
     */
    private $modules;

    /**
     * @ORM\OneToMany(targetEntity="Module", mappedBy="module")
     */
    private $assigned_modules;

    /**
     * @ORM\OneToMany(targetEntity="Privilege", mappedBy="item")
     */
    private $privileges;

	private $active = false;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->translations = new ArrayCollection();
		$this->modules = new ArrayCollection();
		$this->assigned_modules = new ArrayCollection();
		$this->privileges = new ArrayCollection();
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(ItemTranslation $t)
    {
        if (!$this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }
    }

    public function getModules()
    {
        return $this->modules;
    }

	public function getAssignedModules()
    {
        return $this->assigned_modules;
    }

    public function getPrivileges()
    {
        return $this->privileges;
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

    public function setURL($url)
    {
        $this->url = $url;
    }

    public function getURL()
    {
        return $this->url;
    }

    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    public function isVisible()
    {
        return $this->visible;
    }

	public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getLeft()
    {
    	return $this->lft;
    }

	public function getRight()
    {
        return $this->rgt;
    }

    public function setRoot($root)
    {
		$this->root = $root;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function fromArray($data)
    {
		if (isset($data['name'])) {
			$this->name = $data['name'];
		}

		if (isset($data['url'])) {
			$this->url = $data['url'];
		}

		if (isset($data['visible'])) {
			$this->visible = (bool) $data['visible'];
		}
	}

    public function toArray()
    {
		return array(
			'parent'	=> isset($this->parent) ? $this->parent->getId() : null,
			'name'		=> $this->name,
			'url'		=> $this->url,
			'visible'	=> $this->visible,
		);
	}

	public function getAncestors($include_this = false, $desc = false)
	{
		$ancestors = array();

		if ($include_this) {
			$ancestors[] = &$this;
		}

		if ($this->parent !== null) {
			if ($desc === true) {
				foreach ($this->parent->getAncestors(true, true) as $ancestor) {
					$ancestors[] = $ancestor;
				}
				
			}
			else {
				foreach ($this->parent->getAncestors(true, true) as $ancestor) {
					array_unshift($ancestors, $ancestor);
				}
			}
		}

		return $ancestors;
	}

	/*public function findURL($url)
	{
		if ($url->equal(new URI($this->url))) {
			return $this;
		}

		foreach ($this->children as $child) {
			if (($result = $child->findURL($url)) !== null) {
				return $result;
			}
		}

		return null;
	}*/

	public function active()
	{
		$this->active = true;

		if ($this->parent !== null) {
			$this->parent->active();
		}
	}

	public function isActive()
	{
		return $this->active;
	}
}