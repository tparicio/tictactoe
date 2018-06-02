<?php
// src/Entity/Move.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\OneToOne;
use App\Utils\Bitboard;

/**
 * Entity for database table moves
 * 
 * @ORM\Entity
 * @ORM\Table(name="moves")
 */
class Move
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Game")
     * @var integer
     */
    private $game;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $player;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $bitboard;


    /**
     * Get the value of Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of Id
     *
     * @param integer id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of Game
     *
     * @return integer
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set the value of Game
     *
     * @param integer game
     *
     * @return self
     */
    public function setGame($game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get the value of Bitboard
     *
     * @return integer
     */
    public function getBitboard()
    {
        return $this->bitboard;
    }

    /**
     * Set the value of Bitboard
     *
     * @param integer bitboard
     *
     * @return self
     */
    public function setBitboard($bitboard)
    {
        if ($bitboard instanceof Bitboard) {
            $bitboard = $bitboard->getInteger();
        }

        $this->bitboard = $bitboard;

        return $this;
    }


    /**
     * Get the value of Player
     *
     * @return integer
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set the value of Player
     *
     * @param integer player
     *
     * @return self
     */
    public function setPlayer($player)
    {
        $this->player = $player;

        return $this;
    }
}
