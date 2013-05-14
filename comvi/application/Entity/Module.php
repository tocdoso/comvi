<?php
namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="module")
 * @ORM\Entity(repositoryClass="Entity\Repository\ModuleRepository")
 */
class Module
{
	static private $assign_choices = array('all', 'this', 'children');

	static public function getAssignChoices()
	{
		return static::$assign_choices;
	}

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="modules")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="assigned_modules")
     * @ORM\JoinColumn(name="module_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $module;

    /** @ORM\Column(columnDefinition="ENUM('all', 'this', 'children')") */
    private $assign = 'all';

    /** @ORM\Column(type="boolean") */
    private $enable = true;

    /**
     * @ORM\Column(length=32)
     */
    private $position;


    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setItem($item)
    {
        $this->item = $item;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function setModule($module)
    {
        $this->module = $module;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function setAssign($assign)
    {
        if (!in_array($assign, static::getAssignChoices())) {
            throw new \InvalidArgumentException("Invalid assign");
        }

        $this->assign = $assign;
    }

    public function getAssign()
    {
        return $this->assign;
    }

    public function setEnable($enable)
    {
        $this->enable = $enable;
    }

    public function isEnable()
    {
        return $this->enable;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function __toString()
    {
        return $this->item.'-'.$this->module;
    }

    public function fromArray($data)
    {
		if (isset($data['assign'])) {
			$this->assign = $data['assign'];
		}

		if (isset($data['enable'])) {
			$this->enable = (bool) $data['enable'];
		}

		if (isset($data['position'])) {
			$this->position = $data['position'];
		}
	}

    public function toArray()
    {
		return array(
			'item'		=> isset($this->item) ? $this->item->getId() : null,
			'module'	=> isset($this->module) ? $this->module->getId() : null,
			'assign'	=> $this->assign,
			'enable'	=> $this->enable,
			'position'	=> $this->position
		);
	}
}