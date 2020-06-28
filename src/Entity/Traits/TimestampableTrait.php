<?php
declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Trait TimestampableTrait
 * @package App\Entity\Traits
 *
 * @ORM\HasLifecycleCallbacks()
 */
trait TimestampableTrait
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     * @Groups({"game:read","game:list","board:read","player:read"})
     */
    protected $createdAt;


    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function createdUpdate()
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(date_create('now'));
        }
    }
}