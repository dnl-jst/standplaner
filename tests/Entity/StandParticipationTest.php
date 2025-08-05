<?php

namespace App\Tests\Entity;

use App\Entity\StandParticipation;
use App\Entity\CampaignStand;
use App\Entity\Participant;
use PHPUnit\Framework\TestCase;

class StandParticipationTest extends TestCase
{
    private StandParticipation $participation;

    protected function setUp(): void
    {
        $this->participation = new StandParticipation();
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->participation->getCreatedAt());
        $this->assertEquals(StandParticipation::STATUS_MAYBE, $this->participation->getStatus());
        $this->assertNull($this->participation->getUpdatedAt());
    }

    public function testSettersAndGetters(): void
    {
        $campaignStand = new CampaignStand();
        $campaignStand->setDistrict('Friedrichshain');
        $campaignStand->setStartTime(new \DateTimeImmutable('2025-08-25 11:00:00'));
        $campaignStand->setEndTime(new \DateTimeImmutable('2025-08-25 16:00:00'));

        $participant = new Participant();
        $participant->setName('Test Person');

        $this->participation->setCampaignStand($campaignStand);
        $this->participation->setParticipant($participant);

        $this->assertEquals($campaignStand, $this->participation->getCampaignStand());
        $this->assertEquals($participant, $this->participation->getParticipant());
    }

    public function testStatusValidation(): void
    {
        // Valid statuses should work
        $this->participation->setStatus(StandParticipation::STATUS_ATTENDING);
        $this->assertEquals(StandParticipation::STATUS_ATTENDING, $this->participation->getStatus());

        $this->participation->setStatus(StandParticipation::STATUS_MAYBE);
        $this->assertEquals(StandParticipation::STATUS_MAYBE, $this->participation->getStatus());

        $this->participation->setStatus(StandParticipation::STATUS_NOT_ATTENDING);
        $this->assertEquals(StandParticipation::STATUS_NOT_ATTENDING, $this->participation->getStatus());
    }

    public function testInvalidStatusThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid status: invalid_status');

        $this->participation->setStatus('invalid_status');
    }

    public function testStatusUpdatesTimestamp(): void
    {
        $originalUpdatedAt = $this->participation->getUpdatedAt();
        $this->assertNull($originalUpdatedAt);

        $this->participation->setStatus(StandParticipation::STATUS_ATTENDING);

        $this->assertInstanceOf(\DateTimeImmutable::class, $this->participation->getUpdatedAt());
    }

    public function testStatusTextGerman(): void
    {
        $this->participation->setStatus(StandParticipation::STATUS_ATTENDING);
        $this->assertEquals('Nehme teil', $this->participation->getStatusText());

        $this->participation->setStatus(StandParticipation::STATUS_MAYBE);
        $this->assertEquals('Nehme vielleicht teil', $this->participation->getStatusText());

        $this->participation->setStatus(StandParticipation::STATUS_NOT_ATTENDING);
        $this->assertEquals('Nehme nicht teil', $this->participation->getStatusText());
    }

    public function testStatusBooleanMethods(): void
    {
        $this->participation->setStatus(StandParticipation::STATUS_ATTENDING);
        $this->assertTrue($this->participation->isAttending());
        $this->assertFalse($this->participation->isMaybe());
        $this->assertFalse($this->participation->isNotAttending());

        $this->participation->setStatus(StandParticipation::STATUS_MAYBE);
        $this->assertFalse($this->participation->isAttending());
        $this->assertTrue($this->participation->isMaybe());
        $this->assertFalse($this->participation->isNotAttending());

        $this->participation->setStatus(StandParticipation::STATUS_NOT_ATTENDING);
        $this->assertFalse($this->participation->isAttending());
        $this->assertFalse($this->participation->isMaybe());
        $this->assertTrue($this->participation->isNotAttending());
    }

    public function testGetAvailableStatuses(): void
    {
        $expectedStatuses = [
            StandParticipation::STATUS_ATTENDING => 'Nehme teil',
            StandParticipation::STATUS_MAYBE => 'Nehme vielleicht teil',
            StandParticipation::STATUS_NOT_ATTENDING => 'Nehme nicht teil'
        ];

        $this->assertEquals($expectedStatuses, StandParticipation::getAvailableStatuses());
    }

    public function testStatusConstants(): void
    {
        $this->assertEquals('attending', StandParticipation::STATUS_ATTENDING);
        $this->assertEquals('maybe', StandParticipation::STATUS_MAYBE);
        $this->assertEquals('not_attending', StandParticipation::STATUS_NOT_ATTENDING);
    }

    public function testDefaultStatus(): void
    {
        $newParticipation = new StandParticipation();
        $this->assertEquals(StandParticipation::STATUS_MAYBE, $newParticipation->getStatus());
        $this->assertTrue($newParticipation->isMaybe());
    }

    public function testCreatedAtSetInConstructor(): void
    {
        $now = new \DateTimeImmutable();
        $participation = new StandParticipation();

        $this->assertInstanceOf(\DateTimeImmutable::class, $participation->getCreatedAt());
        // Allow for small timing differences
        $this->assertLessThanOrEqual(1, $now->diff($participation->getCreatedAt())->s);
    }

    public function testUpdateAtSetWhenStatusChanges(): void
    {
        $originalUpdatedAt = $this->participation->getUpdatedAt();
        $this->assertNull($originalUpdatedAt);

        $this->participation->setStatus(StandParticipation::STATUS_ATTENDING);
        $firstUpdate = $this->participation->getUpdatedAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $firstUpdate);

        // Sleep to ensure different timestamp
        sleep(1);
        $this->participation->setStatus(StandParticipation::STATUS_NOT_ATTENDING);
        $secondUpdate = $this->participation->getUpdatedAt();

        $this->assertInstanceOf(\DateTimeImmutable::class, $secondUpdate);
        $this->assertGreaterThanOrEqual($firstUpdate->getTimestamp(), $secondUpdate->getTimestamp());
    }
}
