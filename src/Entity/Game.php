<?php
// src/Entity/Game.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Utils\Bitboard;

/**
 * Entity for database table games
 *
 * @ORM\Entity
 * @ORM\Table(name="games")
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $mode;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $player_one;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $player_two;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $bitboard_one = 0;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $bitboard_two = 0;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $turn;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $result;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $line;

    /**
     * @ORM\Column(type="datetime")
     * @var integer
     */
    private $created_at;

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
     * Get the value of Player One
     *
     * @return integer
     */
    public function getPlayerOne()
    {
        return $this->player_one;
    }

    /**
     * Set the value of Player One
     *
     * @param integer player_one
     *
     * @return self
     */
    public function setPlayerOne($player_one)
    {
        $this->player_one = $player_one;

        return $this;
    }

    /**
     * Get the value of Player Two
     *
     * @return integer
     */
    public function getPlayerTwo()
    {
        return $this->player_two;
    }

    /**
     * Set the value of Player Two
     *
     * @param integer player_two
     *
     * @return self
     */
    public function setPlayerTwo($player_two)
    {
        $this->player_two = $player_two;

        return $this;
    }

    /**
     * Get the value of Bitboard One
     *
     * @return integer
     */
    public function getBitboardOne()
    {
        return new Bitboard($this->bitboard_one);
    }

    /**
     * Set the value of Bitboard One
     *
     * @param integer|Bitboard bitboard_one
     *
     * @return self
     */
    public function setBitboardOne($bitboard_one)
    {
        if ($bitboard_one instanceof Bitboard) {
            $bitboard_one = $bitboard_one->getInteger();
        }

        $this->bitboard_one = $bitboard_one;

        return $this;
    }

    /**
     * Get the value of Bitboard Two
     *
     * @return integer
     */
    public function getBitboardTwo()
    {
        return new Bitboard($this->bitboard_two);
    }

    /**
     * Set the value of Bitboard Two
     *
     * @param integer bitboard_two
     *
     * @return self
     */
    public function setBitboardTwo($bitboard_two)
    {
        if ($bitboard_two instanceof Bitboard) {
            $bitboard_two = $bitboard_two->getInteger();
        }

        $this->bitboard_two = $bitboard_two;

        return $this;
    }

    /**
     * Get the value of Turn
     *
     * @return integer
     */
    public function getTurn()
    {
        return $this->turn;
    }

    /**
     * Set the value of Turn
     *
     * @param integer turn
     *
     * @return self
     */
    public function setTurn($turn)
    {
        $this->turn = $turn;

        return $this;
    }

    /**
     * Get the value of Result
     *
     * @return integer
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set the value of Result
     *
     * @param integer result
     *
     * @return self
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }


    /**
     * Get the value of Mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set the value of Mode
     *
     * @param string mode
     *
     * @return self
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Get the value of Created At
     *
     * @return integer
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set the value of Created At
     *
     * @param integer created_at
     *
     * @return self
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }


    /**
     * Get the value of Line
     *
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Set the value of Line
     *
     * @param string line
     *
     * @return self
     */
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }
}
