<?php

namespace App\Tests\SaW\Grid;

use App\SaW\Grid\Direction;
use App\Tests\ATest;

/**
 * Class DirectionTest
 *
 * @package App\Tests\SaW\Grid
 * @author Christian Ruppel < post@christianruppel.de >
 */
class DirectionTest extends ATest
{
    /**
     * @covers \App\SaW\Grid\Direction::__construct
     * @covers \App\SaW\Grid\Direction::getDirection
     * @test
     */
    public function initializeTest ()
    {
        $direction = new Direction('vertical');

        $this->assertSame($direction->getDirection(), 'vertical');
    }

    /**
     * @covers \App\SaW\Grid\Direction::__construct
     * @test
     */
    public function initializeWithWrongDirectionTest ()
    {
        $this->expectException(\InvalidArgumentException::class);

        new Direction('foobar');
    }

    /**
     * @covers \App\SaW\Grid\Direction::isEqual
     * @test
     */
    public function isEqualTest ()
    {
        $directionA = new Direction('vertical');
        $directionB = new Direction('horizontal');
        $directionC = new Direction('horizontal');

        $this->assertFalse($directionA->isEqual($directionB));
        $this->assertTrue($directionB->isEqual($directionC));
    }

    /**
     * @covers \App\SaW\Grid\Direction::vertical
     * @test
     */
    public function verticalTest ()
    {
        $vertical = Direction::vertical();

        $this->assertSame('vertical', $vertical->getDirection());
    }

    /**
     * @covers \App\SaW\Grid\Direction::horizontal
     * @test
     */
    public function horizontalTest ()
    {
        $vertical = Direction::horizontal();

        $this->assertSame('horizontal', $vertical->getDirection());
    }
}