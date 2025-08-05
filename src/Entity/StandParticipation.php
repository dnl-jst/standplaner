<?php

namespace App\Entity;

use App\Repository\StandParticipationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StandParticipationRepository::class)]
class StandParticipation
{
    public const STATUS_ATTENDING = 'attending';
    public const STATUS_MAYBE = 'maybe';
    public const STATUS_NOT_ATTENDING = 'not_attending';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CampaignStand $campaignStand = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $participant = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = self::STATUS_MAYBE; // Default status
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCampaignStand(): ?CampaignStand
    {
        return $this->campaignStand;
    }

    public function setCampaignStand(?CampaignStand $campaignStand): static
    {
        $this->campaignStand = $campaignStand;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(?Participant $participant): static
    {
        $this->participant = $participant;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        // Validate status
        if (!in_array($status, [self::STATUS_ATTENDING, self::STATUS_MAYBE, self::STATUS_NOT_ATTENDING])) {
            throw new \InvalidArgumentException('Invalid status: ' . $status);
        }
        
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get human-readable status text in German
     */
    public function getStatusText(): string
    {
        return match($this->status) {
            self::STATUS_ATTENDING => 'Nehme teil',
            self::STATUS_MAYBE => 'Nehme vielleicht teil',
            self::STATUS_NOT_ATTENDING => 'Nehme nicht teil',
            default => 'Unbekannt'
        };
    }

    /**
     * Check if participant is attending
     */
    public function isAttending(): bool
    {
        return $this->status === self::STATUS_ATTENDING;
    }

    /**
     * Check if participant might attend
     */
    public function isMaybe(): bool
    {
        return $this->status === self::STATUS_MAYBE;
    }

    /**
     * Check if participant is not attending
     */
    public function isNotAttending(): bool
    {
        return $this->status === self::STATUS_NOT_ATTENDING;
    }

    /**
     * Get all available statuses
     */
    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_ATTENDING => 'Nehme teil',
            self::STATUS_MAYBE => 'Nehme vielleicht teil',
            self::STATUS_NOT_ATTENDING => 'Nehme nicht teil'
        ];
    }
}
