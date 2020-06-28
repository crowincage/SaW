<?php
declare(strict_types=1);

namespace App\SaW;

use App\Entity\Board;
use App\Entity\Ship;
use App\SaW\Grid\Coordinate;
use App\SaW\Grid\Direction;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class GridFactory
 *
 * @package App\SaW
 * @author Christian Ruppel < post@christianruppel.de >
 */
final class GridFactory
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * GridFactory constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Board $board
     *
     * @return Grid
     */
    public function __invoke(Board $board = null): Grid
    {
        $size = !is_null($board) ? $board->getBoardSize() : Board::BOARD_DEFAULT_SIZE;

        // create empty grid - no ships are placed
        if (is_null($board)) {
            $grid = new Grid($size);
        }
        // create grid from board layout
        else {
            $grid = new Grid($board->getBoardSize());
            $size = $board->getBoardSize();
            $layout = $board->getLayout();
            $rows = str_split($layout, $size);

            $placedShips = [];

            // echo "\nOriginal layout\n";
            // print_r($rows);
            // echo "\n---------\n";
            foreach ($rows as $rowIndex => $row) {
                $columns = str_split($row);

                foreach ($columns as $columnIndex => $column) {
                    try {
                        $coordinate = new Coordinate(Coordinate::numberToLetter($columnIndex), $rowIndex);

                        if ($column === Grid::TYPE_SHOT) {
                            $grid->markShot($coordinate);
                            continue;
                        }

                        $columnValue = (int)$column;
                        // skip already placed ships or water fields
                        if (
                            $columnValue === 0
                            || !is_numeric($columnValue)
                            || array_key_exists($columnValue, $placedShips)
                        ) {
                            continue;
                        }

                        $ship = $this->findShip($columnValue);
                        $direction = (isset($columns[$columnIndex + 1]) && $columns[$columnIndex + 1] === $columns[$columnIndex])
                            ? Direction::horizontal()
                            : Direction::vertical();

                        if (!is_null($ship)) {
                            $grid->placeShip($ship, $coordinate, $direction);
                            $placedShips[$ship->getId()] = true;
                        }
                    } catch (\Exception $e) {
                        throw new \Exception(sprintf(
                            'Error placing ship: ' . $e->getMessage()
                        ));
                    }
                }
            }

            // echo "\nLoaded layout\n";
            // print_r($grid->toArray());
            // echo "\n---------\n";

            // compare saved with loaded grid layout
            if ($grid->toString() !== $layout) {
                throw new \InvalidArgumentException('Layout format not valid!');
            }
        }

        return $grid;
    }

    /**
     * @param int $id
     *
     * @return Ship|object|null
     */
    private function findShip(int $id)
    {
        return $this->entityManager->getRepository(Ship::class)->find($id);
    }
}