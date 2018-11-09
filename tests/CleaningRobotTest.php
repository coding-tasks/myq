<?php

namespace MyQ\Test;

use MyQ\CleaningRobot;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \MyQ\CleaningRobot
 */
class CleaningRobotTest extends TestCase
{
    /** @var CleaningRobot */
    protected $robot;

    /**
     * Prepare vars.
     *
     * @return void
     */
    public function setUp()
    {
        $this->robot = new CleaningRobot(__DIR__ . '/Fixtures/source.json');

        parent::setUp();
    }

    /**
     * @test
     *
     * @covers ::getBattery
     * @covers ::setBattery
     */
    public function it_gets_and_sets_battery()
    {
        $this->assertEquals(80, $this->robot->getBattery());
        $this->assertInstanceOf(CleaningRobot::class, $this->robot->setBattery(100));
        $this->assertEquals(100, $this->robot->getBattery());
    }

    /**
     * @test
     *
     * @covers ::getDirection
     */
    public function it_gets_direction()
    {
        $this->assertEquals('N', $this->robot->getDirection());
    }

    /**
     * @test
     *
     * @covers ::turnLeft
     * @covers ::getBattery
     * @covers ::getDirection
     */
    public function it_turns_left()
    {
        $this->robot->turnLeft();

        $this->assertEquals(79, $this->robot->getBattery());
        $this->assertEquals('W', $this->robot->getDirection()); // North to west
    }

    /**
     * @test
     *
     * @covers ::turnLeft
     *
     * @expectedException \MyQ\Exception\OutOfBatteryException
     * @expectedExceptionMessage Out of battery.
     */
    public function it_throws_out_of_battery_exception_when_turning_left()
    {
        $this->robot->setBattery(0);
        $this->robot->turnLeft();
    }

    /**
     * @test
     *
     * @covers ::turnRight
     * @covers ::getBattery
     * @covers ::getDirection
     */
    public function it_turns_right()
    {
        $this->robot->turnRight();

        $this->assertEquals(79, $this->robot->getBattery());
        $this->assertEquals('E', $this->robot->getDirection()); // North to east
    }

    /**
     * @test
     *
     * @covers ::turnRight
     *
     * @expectedException \MyQ\Exception\OutOfBatteryException
     * @expectedExceptionMessage Out of battery.
     */
    public function it_throws_out_of_battery_exception_when_turning_right()
    {
        $this->robot->setBattery(0);
        $this->robot->turnRight();
    }
}
