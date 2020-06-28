<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Board
 * @package App\Entity
 * @author Christian Ruppel < post@christianruppel.de >
 *
 * @ApiResource(
 *     collectionOperations={},
 *     itemOperations={
 *         "get"={
 *             "normalization_context"={"groups"={"board:read"}}
 *         }
 *     },
 *     subresourceOperations={
 *         "api_games_boards_subresource"={
 *             "method"="GET",
 *             "normalization_context"={"groups"={"game:read"}}
 *         }
 *     }
 * )
 * @ORM\Entity()
 * @ORM\Table(name="board")
 */
class Board extends AEntity
{
    const BOARD_DEFAULT_SIZE = 10;
    const BOARD_TYPE_SHIP = 'ship';
    const BOARD_TYPE_SHOT = 'shot';
    const BOARD_USER_BOT = 'bot';
    const BOARD_USER_PLAYER = 'player';

    /**
     * @var Game
     *
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="boards")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     * @Groups({"board:read"})
     */
    protected $game;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @Groups({"game:read","board:read"})
     */
    protected $boardSize;

    /**
     * @var string
     *
     * @ORM\Column(type="string", options={"default": "ship"})
     * @Groups({"game:read","board:read"})
     */
    protected $boardType;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"board:read"})
     */
    protected $boardUser;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"game:read","board:read"})
     */
    protected $layout;


    /**
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }

    /**
     * @param Game $game
     */
    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    /**
     * @return int
     */
    public function getBoardSize(): int
    {
        return $this->boardSize;
    }

    /**
     * @param int $boardSize
     */
    public function setBoardSize(int $boardSize): void
    {
        $this->boardSize = $boardSize;
    }

    /**
     * @return string
     */
    public function getBoardType(): string
    {
        return $this->boardType;
    }

    /**
     * @param string $boardType
     *
     * @throws \InvalidArgumentException
     */
    public function setBoardType(string $boardType): void
    {
        if (!in_array($boardType, [self::BOARD_TYPE_SHIP, self::BOARD_TYPE_SHOT])) {
            throw new \InvalidArgumentException('Invalid boardType');
        }

        $this->boardType = $boardType;
    }

    /**
     * @return string|null
     */
    public function getBoardUser()
    {
        return $this->boardUser;
    }

    /**
     * @param string $boardUser
     */
    public function setBoardUser(string $boardUser = null): void
    {
        if (!in_array($boardUser, [self::BOARD_USER_BOT, self::BOARD_USER_PLAYER, null])) {
            throw new \InvalidArgumentException('Invalid boardUser');
        }

        $this->boardUser = $boardUser;
    }

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     */
    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

}