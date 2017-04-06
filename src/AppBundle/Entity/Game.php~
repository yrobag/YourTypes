<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 */
class Game
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="team1", type="string", length=60)
     */
    private $team1;

    /**
     * @var string
     *
     * @ORM\Column(name="team2", type="string", length=60)
     */
    private $team2;

    /**
     * @var int
     *
     * @ORM\Column(name="pointsTeam1", type="integer", nullable=true)
     */
    private $pointsTeam1;

    /**
     * @var int
     *
     * @ORM\Column(name="pointsTeam2", type="integer", nullable=true)
     */
    private $pointsTeam2;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data", type="datetime")
     */
    private $data;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Type", mappedBy="game")
     */
    protected $types;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->types = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set team1
     *
     * @param string $team1
     * @return Game
     */
    public function setTeam1($team1)
    {
        $this->team1 = $team1;

        return $this;
    }

    /**
     * Get team1
     *
     * @return string 
     */
    public function getTeam1()
    {
        return $this->team1;
    }

    /**
     * Set team2
     *
     * @param string $team2
     * @return Game
     */
    public function setTeam2($team2)
    {
        $this->team2 = $team2;

        return $this;
    }

    /**
     * Get team2
     *
     * @return string 
     */
    public function getTeam2()
    {
        return $this->team2;
    }

    /**
     * Set pointsTeam1
     *
     * @param integer $pointsTeam1
     * @return Game
     */
    public function setPointsTeam1($pointsTeam1)
    {
        $this->pointsTeam1 = $pointsTeam1;

        return $this;
    }

    /**
     * Get pointsTeam1
     *
     * @return integer 
     */
    public function getPointsTeam1()
    {
        return $this->pointsTeam1;
    }

    /**
     * Set pointsTeam2
     *
     * @param integer $pointsTeam2
     * @return Game
     */
    public function setPointsTeam2($pointsTeam2)
    {
        $this->pointsTeam2 = $pointsTeam2;

        return $this;
    }

    /**
     * Get pointsTeam2
     *
     * @return integer 
     */
    public function getPointsTeam2()
    {
        return $this->pointsTeam2;
    }

    /**
     * Set data
     *
     * @param \DateTime $data
     * @return Game
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Add types
     *
     * @param \AppBundle\Entity\Type $types
     * @return Game
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
