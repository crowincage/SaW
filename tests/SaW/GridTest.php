<?php

namespace App\Tests\SaW;

use App\Entity\Board;
use App\SaW\Grid;
use App\SaW\Grid\Coordinate;
use App\Tests\ATest;

/**
 * Class DirectionTest
 *
 * @package App\Tests\SaW
 * @author Christian Ruppel < post@christianruppel.de >
 */
class GridTest extends ATest
{
    /**
     * @covers \App\SaW\Grid::__construct
     * @test
     */
    public function initializeTest ()
    {
        $size = 10;
        $grid = new Grid($size);

        $this->assertSame($size, count($grid->toArray()));
    }

    /**
     * @covers \App\SaW\Grid::fromBoard
     * @test
     */
    public function fromBoardTest ()
    {
        $size = 10;
        $board = new Board();
        $board->setBoardSize($size);

        $grid = Grid::fromBoard($board);

        $this->assertSame($size, count($grid->toArray()));
    }

    /**
     * @covers \App\SaW\Grid::initGrid
     * @test
     */
    public function initGridTest ()
    {
        $size = 10;
        $grid = new Grid($size);
        $result = $this->invokeMethod($grid, 'initGrid', [$size]);

        $this->assertIsArray($result);
        $this->assertSame($size, count($result));
    }

    /**
     * @covers \App\SaW\Grid::isCoordinateInGridBounds
     * @test
     */
    public function isCoordinateInGridBoundsTest ()
    {
        $size = 10;
        $grid = new Grid($size);

        $coordinate = Coordinate::fromCoordinate('B4');
        $this->assertTrue($grid->isCoordinateInGridBounds($coordinate));

        $coordinate = Coordinate::fromCoordinate('X23');
        $this->assertFalse($grid->isCoordinateInGridBounds($coordinate));

        $size = 22;
        $grid = new Grid($size);

        $coordinate = Coordinate::fromCoordinate('B4');
        $this->assertTrue($grid->isCoordinateInGridBounds($coordinate));

        $coordinate = Coordinate::fromCoordinate('N21');
        $this->assertTrue($grid->isCoordinateInGridBounds($coordinate));

        $coordinate = Coordinate::fromCoordinate('Z77');
        $this->assertFalse($grid->isCoordinateInGridBounds($coordinate));
    }
}