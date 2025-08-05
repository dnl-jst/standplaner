<?php

namespace App\Tests\Entity;

use App\Entity\Participant;
use App\Entity\CampaignStand;
use App\Entity\StandParticipation;
use PHPUnit\Framework\TestCase;

class ParticipantTest extends TestCase
{
    private Participant $participant;

    protected function setUp(): void
    {
        $this->participant = new Participant();
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->participant->getCreatedAt());
        $this->assertCount(0, $this->participant->getParticipations());
    }

    public function testSettersAndGetters(): void
    {
        $name = 'Anna Beispiel';
        $this->participant->setName($name);

        $this->assertEquals($name, $this->participant->getName());
    }

    public function testAddParticipation(): void
    {
        $campaignStand = new CampaignStand();
        $campaignStand->setDistrict('Kreuzberg');
        $campaignStand->setStartTime(new \DateTimeImmutable('2025-08-15 09:00:00'));
        $campaignStand->setEndTime(new \DateTimeImmutable('2025-08-15 13:00:00'));

        $participation = new StandParticipation();
        $participation->setCampaignStand($campaignStand);
        $participation->setStatus(StandParticipation::STATUS_MAYBE);

        $this->participant->addParticipation($participation);

        $this->assertCount(1, $this->participant->getParticipations());
        $this->assertTrue($this->participant->getParticipations()->contains($participation));
        $this->assertEquals($this->participant, $participation->getParticipant());
    }

    public function testRemoveParticipation(): void
    {
        $campaignStand = new CampaignStand();
        $campaignStand->setDistrict('Prenzlauer Berg');
        $campaignStand->setStartTime(new \DateTimeImmutable('2025-08-20 10:00:00'));
        $campaignStand->setEndTime(new \DateTimeImmutable('2025-08-20 15:00:00'));

        $participation = new StandParticipation();
        $participation->setCampaignStand($campaignStand);
        $participation->setStatus(StandParticipation::STATUS_ATTENDING);

        $this->participant->addParticipation($participation);
        $this->assertCount(1, $this->participant->getParticipations());

        $this->participant->removeParticipation($participation);
        $this->assertCount(0, $this->participant->getParticipations());
    }

    public function testToString(): void
    {
        $name = 'Test Teilnehmer';
        $this->participant->setName($name);

        $this->assertEquals($name, (string) $this->participant);
    }

    public function testToStringWithEmptyName(): void
    {
        $this->assertEquals('', (string) $this->participant);
    }

    public function testCreatedAtIsImmutable(): void
    {
        $originalCreatedAt = $this->participant->getCreatedAt();
        
        // Wait a tiny bit to ensure different timestamp
        usleep(1000);
        
        $newCreatedAt = new \DateTimeImmutable();
        $this->participant->setCreatedAt($newCreatedAt);

        $this->assertEquals($newCreatedAt, $this->participant->getCreatedAt());
        $this->assertNotEquals($originalCreatedAt, $this->participant->getCreatedAt());
    }

    public function testNameCanBeChanged(): void
    {
        $firstName = 'Erster Name';
        $secondName = 'Zweiter Name';

        $this->participant->setName($firstName);
        $this->assertEquals($firstName, $this->participant->getName());

        $this->participant->setName($secondName);
        $this->assertEquals($secondName, $this->participant->getName());
    }
}
