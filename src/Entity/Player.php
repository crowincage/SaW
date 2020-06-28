<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Player
 * @package App\Entity
 * @author Christian Ruppel < post@christianruppel.de >
 *
 * @ApiResource(
 *     collectionOperations={},
 *     itemOperations={
 *         "get"={
 *             "normalization_context"={"groups"={"player:read"}}
 *         }
 *     },
 *     subresourceOperations={
 *         "api_games_players_subresource"={
 *             "method"="GET",
 *             "path"="/saw/{id}/player",
 *             "normalization_context"={"groups"={"game:read"}}
 *         }
 *     }
 * )
 * @ORM\Entity()
 * @ORM\Table(
 *     name="player",
 *     indexes={
 *          @ORM\Index(
 *              name="player_name_idx",
 *              columns={"name"}
 *          )
 *     }
 * )
 */
class Player extends AEntity
{
    use TimestampableTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @Groups({"game:read","game:list","player:read"})
     */
    protected $name;

    /**
     * @var Game
     *
     * @ORM\OneToOne(targetEntity="Game", mappedBy="player")
     * @Groups({"player:read"})
     */
    protected $game;


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

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
}