<?php

// src/Utils/Bitboard.php
namespace App\Utils;

/**
* a bitboard is a board binary representation where 0s represent an empty
* squares and 1s represent an occupied square
*
* @see https://en.wikipedia.org/wiki/Bitboard
* @var array
*/
class Bitboard {
   /**
   * define constans for lines as bitboards
   *
   * @see https://en.wikipedia.org/wiki/Bitboard
   * @var array
   */
   const LINES = [
      'up'            => 448, //0b111000000,
      'middle'        => 56,  //0b000111000,
      'down'          => 7,   //0b000000111,
      'left'          => 292, //0b100100100,
      'center'        => 146, //0b010010010,
      'right'         => 73,  //0b001001001,
      'diagonal'      => 273, //0b100010001,
      'diagonal_inv'  => 84,  //0b001010100
   ];

   /**
   * bitboard value for full board to make some calculations
   *
   * @var integer
   */
   const BOARD = 511; // 0b111111111

   /**
   * bitboard value for only center square
   *
   * @var integer
   */
   const CENTER = 16; // 0b000010000

   /**
    * when bitboard is a line set line number
    *
    * @var null|string
    */
   private $line = null;

  /**
   * Bitboard as integer
   *
   * @var integer
   */
   private $integer;

   /**
    * Bitboard as binary representation
    *
    * @var string
    */
   private $binary;

    /**
     * Bitboard constructor
     *
     * @param  int $integer [description]
     */
    public function __construct(int $integer) {
        $this->setInteger($integer);
        $this->setBinary(str_pad(decbin($integer), 9, '0', STR_PAD_LEFT));
    }

    /**
     * Bitboard constructor with binary string instead an integer
     *
     * @param  string $binary
     * @return App\Utils\Bitboard $bitboard
     */
    public static function binary(string $binary) {
        $integer = bindec($binary);
        return new self($integer);
    }

    /**
     * Bitboard constructor with index instead integer
     * Bitboard with index will only have one piece (at index)
     * Index will be:
     *    8  7  6
     *    5  4  3
     *    2  1  0
     *
     * @param  integer $index [description]
     * @return [type] [description]
     * @author Toni Paricio <toniparicio@gmail.com>
     * @since  2018-06-01
     */
    public static function index(int $index)
    {
      return new self(pow(2, $index));
    }

    /**
     * Get the value of Bitboard as integer
     *
     * @return integer
     */
    public function getInteger()
    {
        return $this->integer;
    }

    /**
     * Set the value of Bitboard as integer
     *
     * @param integer integer
     *
     * @return self
     */
    public function setInteger($integer)
    {
        $this->integer = $integer;

        return $this;
    }

    /**
     * Get the value of Bitboard as binary representation
     *
     * @return string
     */
    public function getBinary()
    {
        return $this->binary;
    }

    /**
     * Set the value of Bitboard as binary representation
     *
     * @param string binary
     *
     * @return self
     */
    public function setBinary($binary)
    {
        $this->binary = $binary;

        return $this;
    }

    /**
     * check if bitboard had any line
     * if line then return bitboard with line otherwise return false
     *
     * @return boolean|int
     */
    public function isLine()
    {
        // iterate on all lines to check if user get line
        foreach (self::LINES as $name => $line) {
            // make an AND bit operation to check line
            if ( ($line & $this->integer) == $line) {
                $this->line = $name;
                // bitboard line found
                return $line;
            }
        }

        // no line so return false
        return false;
    }

   /**
   * check if bitboard has not any piece
   *
   * @return boolean true for empty
   */
   public function isEmpty()
   {
      return $this->integer == 0;
   }

   /**
   * check if bitboard has not empty squares
   *
   * @return boolean true for full
   */
   public function isFull()
   {
      return $this->integer == self::BOARD;
   }

   /**
   * check if bitboard has center square is taked
   *
   * @return boolean true for center square taked
   */
   public function hasCenter()
   {
      return ($this->integer & self::CENTER) != 0;
   }

   /**
    * make and bitwise operation and return another bitboard
    *
    * @param  Bitboard $bitboard
    * @return Bitboard $bitboard intersection of pieces in both bitboards
    */
   public function and(Bitboard $bitboard)
   {
      return new Bitboard($this->integer & $bitboard->getInteger());
   }

   /**
    * make or bitwise operation and return another bitboard
    *
    * @param  Bitboard $bitboard
    * @return Bitboard $bitboard union of pieces in both bitboards
    */
   public function or(Bitboard $bitboard)
   {
      return new Bitboard($this->integer | $bitboard->getInteger());
   }

   /**
    * make xor bitwise operation and return another bitboard
    *
    * @param  Bitboard $bitboard
    * @return Bitboard $bitboard pieces in any boards but no in two
    */
   public function xor(Bitboard $bitboard)
   {
      return new Bitboard($this->integer ^ $bitboard->getInteger());
   }

   /**
   * simple method for represent bitboard in HTML format for debug
   */
   public function draw()
   {
      echo "<p>Bitboard ".$this->integer." ".$this->binary."</p>";

      // represent bitboard in 3 HTML rows and 3 columns
      for ($i = 0 ; $i < 9 ; $i++) {
           if ($i % 3 == 0)
               echo "<br/>";

           echo " <span> ".$this->binary[$i]." <span> ";
      }
   }

   /**
    * return line name
    *
    * @return null|string
    */
   public function getLine()
   {
     return $this->line;
   }

   /**
    * return bitboard as integer when used as string
    *
    * @return string
    */
   public function __toString()
   {
      return $this->integer;
   }
}
