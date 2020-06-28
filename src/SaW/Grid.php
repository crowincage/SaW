<?php
declare(strict_types=1);

namespace App\SaW;

use App\Entity\Board;
use App\Entity\Ship;
use App\SaW\Grid\Coordinate;
use App\SaW\Grid\Direction;

/**
 * Class Grid
 *
 * @package App\SaW
 * @author Christian Ruppel < post@christianruppel.de >
 */
class Grid
{
    const TYPE_WATER = 0;
    const TYPE_HIT = 1;
    const TYPE_SUNK = 2;
    const TYPE_SHOT = '.';

    /**
     * @var array
     */
    protected $grid;

    /**
     * @var array
     */
    protected $ships;


    /**
     * Board constructor.
     *
     * @param int $size
     */
    public function __construct(int $size = Board::BOARD_DEFAULT_SIZE)
    {
        // for now limit size to number of letters in alphabet
        if ($size > 10 && $size <= 26) {
            throw new \OutOfBoundsException('Grid size is out of bounce! Minimum = 10 maximum = 26');
        }

        $this->grid = $this->initGrid($size);
    }

    /**
     * @param Board $board
     *
     * @return static
     */
    public static function fromBoard(Board $board): self
    {
        $static = new static($board->getBoardSize());

        return $static;
    }

    /**
     * @param Ship $ship
     * @param Coordinate $coordinate
     * @param Direction $direction
     *
     * @throws \Exception
     */
    public function placeShip(Ship $ship, Coordinate $coordinate, Direction $direction)
    {
        if (isset($this->ships[$ship->getId()])) {
            throw new \Exception('Ship is already placed!');
        }

        for ($i=0; $i < $ship->getSize(); $i++) {
            $row = $coordinate->getRow() + ($direction->isEqual(Direction::vertical()) ? $i : 0);
            $column = $coordinate->getColumn() + ($direction->isEqual(Direction::horizontal()) ? $i : 0);

            if ($this->grid[$row][$column] === $ship->getId()) {
                throw new \Exception('Ship is already placed!');
            }

            // check if ship is completely on board
            if (!isset($this->grid[$row][$column])) {
                throw new \OutOfBoundsException('Ship placement ist out of board bounds!');
            }

            // check if overlapping
            if ($this->grid[$row][$column] > 0) {
                throw new \InvalidArgumentException(sprintf(
                    'Ship overlaps with another one at %s, direction: %s. Please choose another space.',
                    $coordinate->get(),
                    $direction->getDirection()
                ));
            }

            $this->grid[$row][$column] = $ship->getId();
            $this->ships[$ship->getId()] = $ship;
        }
    }

    /**
     * @param Coordinate $coordinate
     *
     * @return int
     */
    public function shot(Coordinate $coordinate)
    {
        $y = $coordinate->getColumn();
        $x = $coordinate->getRow();
        $shipId = $this->grid[$y][$x];

        if ($shipId !== 0 && is_numeric($shipId)) {
            $this->grid[$y][$x] = -abs($this->grid[$y][$x]);

            if ($this->isShipSunk($this->ships[abs($shipId)])) {
                return self::TYPE_SUNK;
            }

            return self::TYPE_HIT;
        }

        return self::TYPE_WATER;
    }

    /**
     * @param Coordinate $coordinate
     */
    public function markShot(Coordinate $coordinate)
    {
        $this->grid[$coordinate->getRow()][$coordinate->getColumn()] = self::TYPE_SHOT;
    }

    /**
     * @param Ship $ship
     *
     * @return bool
     */
    public function isShipSunk (Ship $ship)
    {
        $size = $ship->getSize();

        $count = 0;
        foreach ($this->grid as $y => $letter) {
            foreach ($letter as $x => $number) {
                if ($this->grid[$y][$x] === -$ship->getId()) {
                    ++$count;
                }
            }
        }

        return $count === $size;
    }

    public function allShipsSunk ()
    {
        //
    }

    /**
     * @param int $numShipsToPlace
     *
     * @return bool
     */
    public function isReadyToPlay (int $numShipsToPlace): bool
    {
        return $numShipsToPlace === count(array_unique(
                array_filter(str_split($this->toString()), function ($e) {
                    return $e > 0;
                })
            ));
    }

    /**
     * @param Coordinate $coordinate
     * 
     * @return bool
     */
    public function isCoordinateInGridBounds(Coordinate $coordinate): bool
    {
        if (!isset($this->grid[$coordinate->getRow()])) {
            return false;
        }

        if (!isset($this->grid[$coordinate->getRow()][$coordinate->getColumn()])) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function toString (): string
    {
        $string = '';
        foreach ($this->grid as $x => $columns) {
            foreach ($columns as $column => $value) {
                $string .= $value;
            }
        }

        return $string;
    }

    /**
     * @return array
     */
    public function toArray (): array
    {
        $rows = [];
        foreach ($this->grid as $x => $columns) {
            $rows[$x] = '';

            foreach ($columns as $column => $value) {
                $rows[$x] .= $value;
            }
        }

        return $rows;
    }

    /**
     * @param int $size
     *
     * @return array
     */
    protected function initGrid (int $size = Board::BOARD_DEFAULT_SIZE): array
    {
        $letters = $this->letters($size);

        $grid = [];
        for ($row=0; $row < $size; $row++) {
            for ($column=0; $column < sizeof($letters); $column++) {
                $grid[$row][$column] = static::TYPE_WATER;
            }
        }

        return $grid;
    }

    /**
     * @param int $size
     *
     * @return array
     */
    protected function letters (int $size): array
    {
        $letters = range('A', 'Z');

        return array_slice($letters, 0, $size);
    }

}