<?php

namespace MyQ\Test;

use MyQ\CleaningRobot;
use MyQ\Exception\ObstacleException;
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
     * @covers ::setDirection
     */
    public function it_gets_and_sets_direction()
    {
        $this->assertEquals('N', $this->robot->getDirection());
        $this->assertInstanceOf(CleaningRobot::class, $this->robot->setDirection('E'));
        $this->assertEquals('E', $this->robot->getDirection());
    }

    /**
     * @test
     *
     * @covers ::getPosition
     */
    public function it_gets_position()
    {
        $this->assertEquals(['X' => 3, 'Y' => 0], $this->robot->getPosition());
    }

    /**
     * @test
     *
     * @covers ::getPosition
     */
    public function it_gets_visited_cells()
    {
        $this->assertEquals([['X' => 3, 'Y' => 0]], $this->robot->getVisited());
    }

    /**
     * @test
     *
     * @covers ::getPosition
     */
    public function it_gets_cleaned_cells()
    {
        $this->assertEquals([], $this->robot->getCleaned());
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
        $this->assertInstanceOf(CleaningRobot::class, $this->robot->turnLeft());
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
        $this->assertInstanceOf(CleaningRobot::class, $this->robot->turnRight());
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

    /**
     * @test
     *
     * @covers ::advance
     */
    public function it_advances_north() : CleaningRobot
    {
        $this->robot->advance();

        $this->assertEquals($this->robot->getPosition(), ['X' => 2, 'Y' => 0]);
        $this->assertEquals($this->robot->getVisited(), [
            ['X' => 3, 'Y' => 0],
            ['X' => 2, 'Y' => 0],
        ]);
        $this->assertEquals(78, $this->robot->getBattery());

        return $this->robot;
    }

    /**
     * @test
     *
     * @covers ::advance
     *
     * @depends it_advances_north
     */
    public function it_advances_east(CleaningRobot $robot) : CleaningRobot
    {
        $robot->turnRight()->advance();

        $this->assertEquals($robot->getPosition(), ['X' => 2, 'Y' => 1]);
        $this->assertEquals($robot->getVisited(), [
            ['X' => 3, 'Y' => 0],
            ['X' => 2, 'Y' => 0],
            ['X' => 2, 'Y' => 1],
        ]);
        $this->assertEquals('E', $robot->getDirection());
        $this->assertEquals(75, $robot->getBattery());

        return $robot;
    }

    /**
     * @test
     *
     * @covers ::advance
     *
     * @depends it_advances_east
     */
    public function it_advances_west(CleaningRobot $robot) : CleaningRobot
    {
        $robot->turnLeft()->turnLeft()->advance();

        $this->assertEquals($robot->getPosition(), ['X' => 2, 'Y' => 0]);
        $this->assertEquals($robot->getVisited(), [
            ['X' => 3, 'Y' => 0],
            ['X' => 2, 'Y' => 0],
            ['X' => 2, 'Y' => 1],
        ]);
        $this->assertEquals('W', $robot->getDirection());
        $this->assertEquals(71, $robot->getBattery());

        return $robot;
    }

    /**
     * @test
     *
     * @covers ::advance
     *
     * @depends it_advances_west
     */
    public function it_advances_south(CleaningRobot $robot)
    {
        $robot->turnLeft()->advance();

        $this->assertEquals($robot->getPosition(), ['X' => 3, 'Y' => 0]);
        $this->assertEquals($robot->getVisited(), [
            ['X' => 3, 'Y' => 0],
            ['X' => 2, 'Y' => 0],
            ['X' => 2, 'Y' => 1],
        ]);
        $this->assertEquals('S', $robot->getDirection());
        $this->assertEquals(68, $robot->getBattery());
    }

    /**
     * @test
     *
     * @covers ::advance
     *
     * @expectedException \MyQ\Exception\ObstacleException
     * @expectedExceptionMessage Obstacle on the way.
     */
    public function it_hits_the_obstacle_during_advance()
    {
        $this->robot->turnRight()->advance();
    }

    /**
     */
    public function it_hits_the_obstacle_second_time_during_advance()
    {
        $robotMock = $this->getMockBuilder(CleaningRobot::class)
                          ->setMethods(['backOff'])
                          ->setConstructorArgs([__DIR__ . '/Fixtures/source.json'])
                          ->getMock();

        $robotMock
            ->expects($this->exactly(2))
            ->method('backOff')
            ->willReturnOnConsecutiveCalls(null, $this->throwException(new ObstacleException()), null);

        $robotMock->turnRight()->advance();

        $this->assertEquals(1, $robotMock->getBackOffAttempts());
    }

    /**
     * @test
     *
     * @covers ::isObstacle
     */
    public function it_checks_for_obstacle()
    {
        $this->assertFalse($this->robot->isObstacle(2, 0));
        $this->assertFalse($this->robot->isObstacle(1, 1));
        $this->assertTrue($this->robot->isObstacle(3, 1));
        $this->assertTrue($this->robot->isObstacle(1, 2));
        $this->assertTrue($this->robot->isObstacle(-1, 2));
        $this->assertTrue($this->robot->isObstacle(2, 4));
        $this->assertTrue($this->robot->isObstacle(-1, 4));
    }

    /**
     * @test
     *
     * @covers ::clean
     */
    public function it_cleans_a_cell()
    {
        $this->assertInstanceOf(CleaningRobot::class, $this->robot->clean());
        $this->assertEquals(75, $this->robot->getBattery());
        $this->assertEquals([['X' => 3, 'Y' => 0]], $this->robot->getCleaned());
    }

    /**
     * @test
     *
     * @covers ::turnLeft
     *
     * @expectedException \MyQ\Exception\OutOfBatteryException
     * @expectedExceptionMessage Out of battery.
     */
    public function it_throws_out_of_battery_exception_when_cleaning_cell()
    {
        $this->robot->setBattery(0);
        $this->robot->clean();
    }

    /**
     * @test
     *
     * @covers ::back
     */
    public function it_goes_back()
    {
        $this->robot->advance()->turnRight()->advance()->back();

        $this->assertEquals(['X' => 2, 'Y' => 0], $this->robot->getPosition());
        $this->assertEquals(72, $this->robot->getBattery());
    }

    /**
     * @test
     *
     * @covers ::back
     *
     * @expectedException \MyQ\Exception\ObstacleException
     * @expectedExceptionMessage Cannot go back.
     */
    public function it_hits_the_obstacle_while_going_back()
    {
        $this->robot->back();
    }
}
