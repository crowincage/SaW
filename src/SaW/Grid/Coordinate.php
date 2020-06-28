<?php
declare(strict_types=1);

namespace App\SaW\Grid;

/**
 * Class Coordinate
 *
 * @package App\SaW\Grid
 * @author Christian Ruppel < post@christianruppel.de >
 */
class Coordinate
{
    /**
     * @var string
     */
    protected $letter;

    /**
     * @var int
     */
    protected $row;

    /**
     * @var int
     */
    protected $column;

    /**
     * @var string
     */
    protected $coordinate;

    /**
     * Coordinate constructor.
     *
     * @param string $letter
     * @param int $row
     */
    public function __construct(string $letter, int $row)
    {
        // @todo - validate letter / number
        $this->row = $row;
        $this->column = $this->letterToNumber($letter);
        $this->letter = strtoupper($letter);
        $this->coordinate = $letter . ($row + 1);
    }

    /**
     * Gets letter's number in alphabet
     *
     * @param $letter
     * 
     * @return int
     */
    public static function letterToNumber($letter)
    {
        return ord(strtoupper($letter)) - ord('A');
    }

    /**
     * Get a letter for a number in alphabet
     *
     * @param $number
     *
     * @return string
     */
    public static function numberToLetter($number)
    {
        return chr(ord('A') + $number);
    }

    /**
     * @param string $coordinate
     *
     * @return static
     */
    public static function fromCoordinate (string $coordinate): self
    {
        return new static($coordinate[0], (int)substr($coordinate,1, strlen($coordinate)) - 1);
    }

    /**
     * @return string
     */
    public function getLetter(): string
    {
        return $this->letter;
    }

    /**
     * @return int
     */
    public function getRow(): int
    {
        return $this->row;
    }

    /**
     * @return int
     */
    public function getColumn(): int
    {
        return $this->column;
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->coordinate;
    }


}