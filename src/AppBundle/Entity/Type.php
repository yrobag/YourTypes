<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Type
 *
 * @ORM\Table(name="type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TypeRepository")
 */
class Type
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
     * @var int
     *
     * @ORM\Column(name="typePointsTeam1", type="integer")
     */
    private $typePointsTeam1;

    /**
     * @var int
     *
     * @ORM\Column(name="typePointsTeam2", type="integer")
     */
    private $typePointsTeam2;


    /**
     * @var int
     *
     * @ORM\Column(name="points", type="integer", nullable=true)
     */
    private $points;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="types" )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Game", inversedBy="types" )
     * @ORM\JoinColumn(name="match_id", referencedColumnName="id")
     */
    private $game;



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
     * Set typePointsTeam1
     *
     * @param integer $typePointsTeam1
     * @return Type
     */
    public function setTypePointsTeam1($typePointsTeam1)
    {
        $this->typePointsTeam1 = $typePointsTeam1;

        return $this;
    }

    /**
     * Get typePointsTeam1
     *
     * @return integer 
     */
    public function getTypePointsTeam1()
    {
        return $this->typePointsTeam1;
    }

    /**
     * Set typePointsTeam2
     *
     * @param integer $typePointsTeam2
     * @return Type
     */
    public function setTypePointsTeam2($typePointsTeam2)
    {
        $this->typePointsTeam2 = $typePointsTeam2;

        return $this;
    }

    /**
     * Get typePointsTeam2
     *
     * @return integer 
     */
    public function getTypePointsTeam2()
    {
        return $this->typePointsTeam2;
    }

    /**
     * @return mixed
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param mixed $points
     */
    public function setPoints($points)
    {
        $this->points = $points;
    }


    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Type
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set game
     *
     * @param \AppBundle\Entity\Game $game
     * @return Type
     */
    public function setGame(\AppBundle\Entity\Game $game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \AppBundle\Entity\Game 
     */
    public function getGame()
    {
        return $this->game;
    }
}
