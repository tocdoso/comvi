<?php
namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="privilege")
 * @ORM\Entity(repositoryClass="Entity\Repository\PrivilegeRepository")
 */
class Privilege
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="privileges")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="privileges")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $item;

    /**
     * @ORM\Column(length=16)
     */
    private $task;

    /** @ORM\Column(type="boolean") */
    private $allow = true;


    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setItem($item)
    {
        $this->item = $item;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function setTask($task)
    {
        $this->task = $task;
    }

    public function getTask()
    {
        return $this->task;
    }

    public function setAllow($allow)
    {
        $this->allow = $allow;
    }

    public function isAllowed()
    {
        return $this->allow;
    }

    public function __toString()
    {
        return $this->role->getName().'-'.$this->item->getId().'-'.$this->task.'-'.$this->allow;
    }

    public function fromArray($data)
    {
		if (isset($data['task'])) {
			$this->task = $data['task'];
		}

		if (isset($data['allow'])) {
			$this->allow = (bool) $data['allow'];
		}
	}

    public function toArray()
    {
		return array(
			'role'		=> isset($this->role) ? $this->role->getId() : null,
			'item'		=> isset($this->item) ? $this->item->getId() : null,
			'task'		=> $this->task,
			'allow'	=> $this->allow
		);
	}
}