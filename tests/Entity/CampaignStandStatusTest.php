<?php

namespace App\Tests\Entity;

use App\Entity\CampaignStand;
use PHPUnit\Framework\TestCase;

class CampaignStandTest extends TestCase
{
    public function testIsFuture(): void
    {
        $stand = new CampaignStand();

        // Test mit zukünftigem Datum
        $futureStart = new \DateTimeImmutable('+1 hour');
        $futureEnd = new \DateTimeImmutable('+2 hours');
        $stand->setStartTime($futureStart);
        $stand->setEndTime($futureEnd);

        $this->assertTrue($stand->isFuture());
        $this->assertFalse($stand->isRunning());
        $this->assertFalse($stand->isFinished());
        $this->assertEquals('future', $stand->getStatus());
        $this->assertTrue($stand->canRegister());
    }

    public function testIsRunning(): void
    {
        $stand = new CampaignStand();

        // Test mit laufendem Stand (Start in der Vergangenheit, Ende in der Zukunft)
        $pastStart = new \DateTimeImmutable('-1 hour');
        $futureEnd = new \DateTimeImmutable('+1 hour');
        $stand->setStartTime($pastStart);
        $stand->setEndTime($futureEnd);

        $this->assertFalse($stand->isFuture());
        $this->assertTrue($stand->isRunning());
        $this->assertFalse($stand->isFinished());
        $this->assertEquals('running', $stand->getStatus());
        $this->assertFalse($stand->canRegister());
    }

    public function testIsFinished(): void
    {
        $stand = new CampaignStand();

        // Test mit beendetem Stand
        $pastStart = new \DateTimeImmutable('-2 hours');
        $pastEnd = new \DateTimeImmutable('-1 hour');
        $stand->setStartTime($pastStart);
        $stand->setEndTime($pastEnd);

        $this->assertFalse($stand->isFuture());
        $this->assertFalse($stand->isRunning());
        $this->assertTrue($stand->isFinished());
        $this->assertEquals('finished', $stand->getStatus());
        $this->assertFalse($stand->canRegister());
    }

    public function testCanRegisterOnlyForFutureStands(): void
    {
        $stand = new CampaignStand();

        // Zukünftiger Stand - Anmeldung möglich
        $stand->setStartTime(new \DateTimeImmutable('+1 hour'));
        $stand->setEndTime(new \DateTimeImmutable('+2 hours'));
        $this->assertTrue($stand->canRegister());

        // Laufender Stand - keine Anmeldung möglich
        $stand->setStartTime(new \DateTimeImmutable('-1 hour'));
        $stand->setEndTime(new \DateTimeImmutable('+1 hour'));
        $this->assertFalse($stand->canRegister());

        // Beendeter Stand - keine Anmeldung möglich
        $stand->setStartTime(new \DateTimeImmutable('-2 hours'));
        $stand->setEndTime(new \DateTimeImmutable('-1 hour'));
        $this->assertFalse($stand->canRegister());
    }
}
