<?php
namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Entity\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(length=255, unique=true)
     */
    private $email;

     /** @ORM\ManyToMany(targetEntity="Role", inversedBy="users") */
    private $roles;


    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    /*public function removeRole($role)
    {
        //optionally add a check here to see that $role exists before removing it.
        return $this->roles->removeElement($role);
    }*/

    public function __toString()
    {
        return $this->email;
    }

    public function fromArray($data)
    {
		if (isset($data['email'])) {
			$this->email = $data['email'];
		}
	}

    public function toArray()
    {
		return array(
			'email'	=> $this->email
		);
	}
}