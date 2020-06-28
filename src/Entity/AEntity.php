<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class AEntity
 *
 * @package App\Entity
 * @copyright 2017 (c) MCS GmbH
 *
 * @author Christian Ruppel < christian.ruppel@mcs.de >
 */
abstract class AEntity
{

    /**
     * @var integer|
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"game:read","game:list","player:read","board:read"})
     */
    protected $id;


    /**
     * AEntity constructor
     *
     * @return void
     */
    public function __construct()
    {
        if (property_exists($this, 'createdAt')) {
            if ($this->createdAt === null) {
                $this->createdAt = date_create();
            }
        }
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}