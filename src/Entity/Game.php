<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Game
 * @package App\Entity
 * @author Christian Ruppel < post@christianruppel.de >
 *
 * @ApiResource(
 *     collectionOperations={
 *         "get"={
 *             "normalization_context"={"groups"={"game:list"}}
 *         }
 *     },
 *     itemOperations={
 *         "get"={
 *             "normalization_context"={"groups"={"game:read"}}
 *         },
 *         "delete"={}
 *     },
 *     normalizationContext={"groups"={"game:read"}},
 *     denormalizationContext={"groups"={"write"}}
 * )
 * @ORM\Entity()
 * @ORM\Table(name="game")
 */
class Game extends AEntity
{
    use TimestampableTrait;

    /**
     * @var Board[]|ArrayCollection
     *
     * @ApiSubresource
     * @ORM\OneToMany(targetEntity="Board", mappedBy="game", cascade={"persist", "remove"})
     * @Groups({"game:read","game:list"})
     */
    protected $boards;

    /**
     * @var Player
     *
     * @ApiSubresource
     * @ORM\OneToOne(targetEntity="Player", inversedBy="game", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     * @Groups({"game:read","game:list","board:read"})
     */
    protected $player;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default": 0})
     * @Groups({"game:read","game:list","board:read"})
     */
    protected $roundCount;


    /**
     * Game constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->boards = new ArrayCollection();
        $this->roundCount = 0;
    }

    /**
     * @param bool $onlyUserBoards
     *
     * @return Board[]|ArrayCollection
     */
    public function getBoards($onlyUserBoards = true)
    {
        $collection = new ArrayCollection();

        if ($onlyUserBoards) {
            foreach ($this->boards as $board) {
                if (Board::BOARD_USER_PLAYER === $board->getBoardUser()) {
                    $collection->add($board);
                }
            }

            return $collection;
        }

        return $this->boards;
    }

    /**
     * @return Board[]|ArrayCollection
     */
    public function getPlayerBoards ($boardType = null)
    {
        return $this->boards->filter(function ($board) use ($boardType) {
            /* @var Board $board */
            if (!is_null($boardType)) {
                return $board->getBoardUser() === Board::BOARD_USER_PLAYER
                    && $board->getBoardType() === $boardType;
            }

            return $board->getBoardUser() === Board::BOARD_USER_PLAYER;
        });
    }

    /**
     * @return Board[]|ArrayCollection
     */
    public function getBotBoards ($boardType = null)
    {
        return $this->boards->filter(function ($board) use ($boardType)  {
            /* @var Board $board */
            if (!is_null($boardType)) {
                return $board->getBoardUser() === Board::BOARD_USER_BOT
                    && $board->getBoardType() === $boardType;
            }

            return $board->getBoardUser() === Board::BOARD_USER_BOT;
        });
    }

    /**
     * @param Board[]|ArrayCollection $boards
     */
    public function setBoards(ArrayCollection $boards): void
    {
        $this->boards = $boards;
    }

    /**
     * @param Board $board
     */
    public function addBoard (Board $board): void
    {
        $board->setGame($this);
        $this->boards->add($board);
    }

    /**
     * @param Board $board
     */
    public function removeBoard (Board $board): void
    {
        // $board->setGame(null);
        $this->boards->removeElement($board);
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @param Player $player
     */
    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    /**
     * @return int
     */
    public function getRoundCount(): int
    {
        return $this->roundCount;
    }

    /**
     * @param int $roundCount
     */
    public function setRoundCount(int $roundCount): void
    {
        $this->roundCount = $roundCount;
    }

}