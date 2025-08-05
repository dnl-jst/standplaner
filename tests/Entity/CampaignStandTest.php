<?php

namespace App\Tests\Entity;

use App\Entity\CampaignStand;
use App\Entity\Participant;
use App\Entity\StandParticipation;
use PHPUnit\Framework\TestCase;

class CampaignStandTest extends TestCase
{
    private CampaignStand $campaignStand;

    protected function setUp(): void
    {
        $this->campaignStand = new CampaignStand();
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->campaignStand->getCreatedAt());
        $this->assertCount(0, $this->campaignStand->getParticipations());
    }

    public function testSettersAndGetters(): void
    {
        $startTime = new \DateTimeImmutable('2025-08-10 10:00:00');
        $endTime = new \DateTimeImmutable('2025-08-10 14:00:00');
        $district = 'Mitte';
        $address = 'Alexanderplatz 1, 10178 Berlin';

        $this->campaignStand->setStartTime($startTime);
        $this->campaignStand->setEndTime($endTime);
        $this->campaignStand->setDistrict($district);
        $this->campaignStand->setAddress($address);

        $this->assertEquals($startTime, $this->campaignStand->getStartTime());
        $this->assertEquals($endTime, $this->campaignStand->getEndTime());
        $this->assertEquals($district, $this->campaignStand->getDistrict());
        $this->assertEquals($address, $this->campaignStand->getAddress());
    }

    public function testAddParticipation(): void
    {
        $participant = new Participant();
        $participant->setName('Max Mustermann');

        $participation = new StandParticipation();
        $participation->setParticipant($participant);
        $participation->setStatus(StandParticipation::STATUS_ATTENDING);

        $this->campaignStand->addParticipation($participation);

        $this->assertCount(1, $this->campaignStand->getParticipations());
        $this->assertTrue($this->campaignStand->getParticipations()->contains($participation));
        $this->assertEquals($this->campaignStand, $participation->getCampaignStand());
    }

    public function testRemoveParticipation(): void
    {
        $participant = new Participant();
        $participant->setName('Max Mustermann');

        $participation = new StandParticipation();
        $participation->setParticipant($participant);
        $participation->setStatus(StandParticipation::STATUS_ATTENDING);

        $this->campaignStand->addParticipation($participation);
        $this->assertCount(1, $this->campaignStand->getParticipations());

        $this->campaignStand->removeParticipation($participation);
        $this->assertCount(0, $this->campaignStand->getParticipations());
    }

    public function testToString(): void
    {
        $startTime = new \DateTimeImmutable('2025-08-10 10:00:00');
        $endTime = new \DateTimeImmutable('2025-08-10 14:00:00');
        $district = 'Mitte';

        $this->campaignStand->setStartTime($startTime);
        $this->campaignStand->setEndTime($endTime);
        $this->campaignStand->setDistrict($district);

        $expected = 'Mitte (10.08.2025 10:00 - 14:00)';
        $this->assertEquals($expected, (string) $this->campaignStand);
    }

    public function testOptionalAddress(): void
    {
        $this->assertNull($this->campaignStand->getAddress());

        $address = 'Potsdamer Platz 1';
        $this->campaignStand->setAddress($address);
        $this->assertEquals($address, $this->campaignStand->getAddress());

        $this->campaignStand->setAddress(null);
        $this->assertNull($this->campaignStand->getAddress());
    }
}
