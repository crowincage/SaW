<?php

namespace App\Tests\SaW\Grid;

use App\SaW\Grid\Coordinate;
use App\Tests\ATest;

/**
 * Class CoordinateTest
 *
 * @package App\Tests\SaW\Grid
 * @author Christian Ruppel < post@christianruppel.de >
 */
class CoordinateTest extends ATest
{
    /**
     * @covers \App\SaW\Grid\Coordinate::fromCoordinate
     * @covers \App\SaW\Grid\Coordinate::__construct
     * @test
     */
    public function fromCoordinateTest ()
    {
        $coordinate = Coordinate::fromCoordinate('A1');

        $this->assertSame(0, $coordinate->getRow());
        $this->assertSame(0, $coordinate->getColumn());
        $this->assertSame('A', $coordinate->getLetter());
    }

    /**
     * @covers \App\SaW\Grid\Coordinate::letterToNumber
     * @test
     */
    public function letterToNumberTest ()
    {
        $this->assertSame(0, Coordinate::letterToNumber('A'));
        $this->assertSame(5, Coordinate::letterToNumber('F'));
    }

    /**
     * @covers \App\SaW\Grid\Coordinate::numberToLetter
     * @test
     */
    public function numberToLetterTest ()
    {
        $this->assertSame('A', Coordinate::numberToLetter(0));
        $this->assertSame('F', Coordinate::numberToLetter(5));
    }

    /**
     * @covers \App\SaW\Grid\Coordinate::getLetter
     * @covers \App\SaW\Grid\Coordinate::getRow
     * @covers \App\SaW\Grid\Coordinate::getColumn
     * @covers \App\SaW\Grid\Coordinate::get
     * @test
     */
    public function coordinateGettersTest ()
    {
        $coordinate = Coordinate::fromCoordinate('A1');

        $this->assertSame('A', $coordinate->getLetter());
        $this->assertSame(0, $coordinate->getRow());
        $this->assertSame(0, $coordinate->getColumn());
        $this->assertSame('A1', $coordinate->get());
    }
}