<?php
declare(strict_types=1);

namespace App\SaW\Grid;

/**
 * Class Direction
 *
 * @package App\SaW\Grid
 * @author Christian Ruppel < post@christianruppel.de >
 */
final class Direction
{
    const VERTICAL = 'vertical';
    const HORIZONTAL = 'horizontal';

    /**
     * @var string
     */
    protected $direction;


    /**
     * @param string $direction
     */
    public function __construct($direction)
    {
        $this->direction = $direction;
    }

    /**
     * @return static
     */
    public static function vertical(): self
    {
        return new static(self::VERTICAL);
    }

    /**
     * @return static
     */
    public static function horizontal(): self
    {
        return new static(self::HORIZONTAL);
    }

    /**
     * @return string
     */
    public function getDirection (): string
    {
        return $this->direction;
    }

    /**
     * @param Direction $direction
     *
     * @return bool
     */
    public function isEqual(self $direction): bool
    {
        return $this->direction === $direction->getDirection();
    }
}