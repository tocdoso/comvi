<?php
namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="role")
 * @ORM\Entity(repositoryClass="Entity\Repository\RoleRepository")
 */
class Role
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(length=32)
     */
    private $name;

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
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $level;

    /**
     * @ORM\OneToMany(targetEntity="Role", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity="Privilege", mappedBy="role")
     */
    private $privileges;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
     */
    private $users;


    public function __construct()
    {
		$this->children = new ArrayCollection();
		$this->privileges = new ArrayCollection();
		$this->users = new ArrayCollection();
    }

    public function getChildren()
    {
        return $this->children;
    }

	public function getPrivileges()
    {
        return $this->privileges;
    }

    public function getUsers()
    {
        return $this->users;
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

    public function getLeft()
    {
    	return $this->lft;
    }

	public function getRight()
    {
        return $this->rgt;
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
			'parent' => isset($this->parent) ? $this->parent->getId() : null,
			'name'	 => $this->name
		);
	}
}