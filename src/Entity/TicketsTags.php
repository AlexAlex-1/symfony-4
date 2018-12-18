<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TicketsTagsRepository")
 */
class TicketsTags
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $Ticket_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $Tag_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicketId(): ?int
    {
        return $this->Ticket_id;
    }

    public function setTicketId(int $Ticket_id): self
    {
        $this->Ticket_id = $Ticket_id;

        return $this;
    }

    public function getTagId(): ?int
    {
        return $this->Tag_id;
    }

    public function setTagId(int $Tag_id): self
    {
        $this->Tag_id = $Tag_id;

        return $this;
    }
}
