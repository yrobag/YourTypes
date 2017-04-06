<?php


namespace AppBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Type", mappedBy="user")
     */
    protected $types;


    public function __construct()
    {
        parent::__construct();
        $this->types = new \Doctrine\Common\Collections\ArrayCollection();

    }

    /**
     * Add types
     *
     * @param \AppBundle\Entity\Type $types
     * @return User
     */
    public function addType(\AppBundle\Entity\Type $types)
    {
        $this->types[] = $types;

        return $this;
    }

    /**
     * Remove types
     *
     * @param \AppBundle\Entity\Type $types
     */
    public function removeType(\AppBundle\Entity\Type $types)
    {
        $this->types->removeElement($types);
    }

    /**
     * Get types
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTypes()
    {
        return $this->types;
    }
}
