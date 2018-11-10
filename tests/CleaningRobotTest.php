<?php

namespace MyQ\Test;

use MyQ\CleaningRobot;
use MyQ\SourceFileReader;
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
        $this->robot = new CleaningRobot(new SourceFileReader(__DIR__ . '/Fixtures/source.json'));

        parent::setUp();
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::init
     *
     * @expectedException \MyQ\Exceptions\FileException
     * @expectedExceptionMessage Invalid source file.
     */
    public function it_throws_file_exception_for_invalid_source_file()
    {
        new CleaningRobot(new SourceFileReader(__DIR__ . '/Fixtures/invalid/file'));
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::init
     *
     * @expectedException \MyQ\Exceptions\FileException
     * @expectedExceptionMessage Invalid source json.
     */
    public function it_throws_file_exception_for_invalid_json_in_source_file()
    {
        new CleaningRobot(new SourceFileReader(__DIR__ . '/Fixtures/invalid.json'));
    }

    /**
     * @test
     *
     * @covers ::getBattery
     * @covers ::setBattery
     * @covers ::init
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
     * @covers ::init
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
     * @covers ::init
     */
    public function it_gets_position()
    {
        $this->assertEquals(['X' => 3, 'Y' => 0], $this->robot->getPosition());
    }

    /**
     * @test
     *
     * @covers ::getVisited
     */
    public function it_gets_visited_cells()
    {
        $this->assertEquals([['X' => 3, 'Y' => 0]], $this->robot->getVisited());
    }

    /**
     * @test
     *
     * @covers ::getCleaned
     */
    public function it_gets_cleaned_cells()
    {
        $this->assertEquals([], $this->robot->getCleaned());
    }

    /**
     * @test
     *
     * @covers ::getCommands
     */
    public function it_gets_commands()
    {
        $this->assertEquals(['TL', 'A', 'C', 'A', 'C', 'TR', 'A', 'C'], $this->robot->getCommands());
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
     * @expectedException \MyQ\Exceptions\OutOfBatteryException
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
     * @expectedException \MyQ\Exceptions\OutOfBatteryException
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
     * @expectedException \MyQ\Exceptions\ObstacleException
     * @expectedExceptionMessage Obstacle on the way.
     */
    public function it_hits_the_obstacle_during_advance()
    {
        $this->robot->turnRight()->advance();
    }

    /**
     * @test
     *
     * @covers ::advance
     *
     * @expectedException \MyQ\Exceptions\OutOfBatteryException
     * @expectedExceptionMessage Out of battery.
     */
    public function it_runs_out_of_battery_during_advance()
    {
        $this->robot->setBattery(0);
        $this->robot->advance();
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
     * @covers ::run
     * @covers ::backOff
     */
    public function it_run_commands()
    {
        $this->robot->run();

        $this->assertEquals($this->robot->getPosition(), ['X' => 1, 'Y' => 1]);
        $this->assertEquals($this->robot->getVisited(), [
            ['X' => 3, 'Y' => 0],
            ['X' => 2, 'Y' => 0],
            ['X' => 1, 'Y' => 0],
            ['X' => 1, 'Y' => 1],
        ]);
        $this->assertEquals($this->robot->getCleaned(), [
            ['X' => 2, 'Y' => 0],
            ['X' => 1, 'Y' => 0],
            ['X' => 1, 'Y' => 1],
        ]);
        $this->assertEquals(54, $this->robot->getBattery());
        $this->assertEquals('E', $this->robot->getDirection());
    }

    /**
     * @test
     *
     * @covers ::run
     * @covers ::backOff
     */
    public function it_backs_off_in_obstacle()
    {
        $robot = new CleaningRobot(new SourceFileReader(__DIR__ . '/Fixtures/obstacle.json'));

        $robot->run();

        $this->assertEquals($robot->getPosition(), ['X' => 2, 'Y' => 3]);
        $this->assertEquals($robot->getVisited(), [
            ['X' => 3, 'Y' => 2],
            ['X' => 3, 'Y' => 3],
            ['X' => 2, 'Y' => 3],
        ]);
        $this->assertEquals($robot->getCleaned(), [['X' => 2, 'Y' => 3]]);
        $this->assertEquals(83, $robot->getBattery());
        $this->assertEquals('N', $robot->getDirection());
    }

    /**
     * @test
     *
     * @covers ::run
     * @covers ::backOff
     */
    public function it_backs_off_in_consecutive_obstacles()
    {
        $robot = new CleaningRobot(new SourceFileReader(__DIR__ . '/Fixtures/obstacle2.json'));

        $robot->run();

        $this->assertEquals($robot->getPosition(), ['X' => 1, 'Y' => 0]);
        $this->assertEquals($robot->getVisited(), [
            ['X' => 0, 'Y' => 1],
            ['X' => 1, 'Y' => 1],
            ['X' => 1, 'Y' => 0],
        ]);
        $this->assertEquals($robot->getCleaned(), [['X' => 1, 'Y' => 0]]);
        $this->assertEquals(79, $robot->getBattery());
        $this->assertEquals('W', $robot->getDirection());
    }

    /**
     * @test
     *
     * @covers ::run
     * @covers ::backOff
     */
    public function it_backs_off_in_multiple_consecutive_obstacles()
    {
        $robot = new CleaningRobot(new SourceFileReader(__DIR__ . '/Fixtures/obstacle3.json'));

        $robot->run();

        $this->assertEquals($robot->getPosition(), ['X' => 2, 'Y' => 2]);
        $this->assertEquals($robot->getVisited(), [
            ['X' => 0, 'Y' => 1],
            ['X' => 1, 'Y' => 1],
            ['X' => 2, 'Y' => 1],
            ['X' => 2, 'Y' => 2],
        ]);
        $this->assertEquals($robot->getCleaned(), [['X' => 2, 'Y' => 2]]);
        $this->assertEquals(72, $robot->getBattery());
        $this->assertEquals('E', $robot->getDirection());
    }

    /**
     * @test
     *
     * @covers ::run
     * @covers ::backOff
     */
    public function it_runs_all_back_off_strategies()
    {
        $robot = new CleaningRobot(new SourceFileReader(__DIR__ . '/Fixtures/obstacle4.json'));

        $robot->run();

        $this->assertEquals($robot->getPosition(), ['X' => 2, 'Y' => 0]);
        $this->assertEquals($robot->getVisited(), [
            ['X' => 0, 'Y' => 1],
            ['X' => 1, 'Y' => 1],
            ['X' => 2, 'Y' => 1],
            ['X' => 2, 'Y' => 0],
        ]);
        $this->assertEquals($robot->getCleaned(), [['X' => 2, 'Y' => 0]]);
        $this->assertEquals(68, $robot->getBattery());
        $this->assertEquals('W', $robot->getDirection());
    }

    /**
     * @test
     *
     * @covers ::run
     * @covers ::backOff
     *
     * @expectedException \MyQ\Exceptions\BackOffException
     * @expectedExceptionMessage Cannot back off.
     */
    public function it_throws_exception_if_all_back_off_strategy_fails()
    {
        $robot = new CleaningRobot(new SourceFileReader(__DIR__ . '/Fixtures/backoff.json'));

        $robot->run();

        $this->assertEquals($robot->getPosition(), ['X' => 1, 'Y' => 1]);
        $this->assertEquals($robot->getVisited(), [['X' => 1, 'Y' => 1]]);
        $this->assertEquals($robot->getCleaned(), []);
        $this->assertEquals(79, $robot->getBattery());
        $this->assertEquals('E', $robot->getDirection());
    }

    /**
     * @test
     *
     * @covers ::clean
     */
    public function it_cleans_a_cell()
    {
        $this->robot->clean();

        $this->assertEquals(75, $this->robot->getBattery());
        $this->assertEquals([['X' => 3, 'Y' => 0]], $this->robot->getCleaned());
    }

    /**
     * @test
     *
     * @covers ::clean
     *
     * @expectedException \MyQ\Exceptions\OutOfBatteryException
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
    public function it_goes_back_from_east_to_west()
    {
        $this->robot->advance()->turnRight()->advance()->back();

        $this->assertEquals(['X' => 2, 'Y' => 0], $this->robot->getPosition());
        $this->assertEquals(72, $this->robot->getBattery());
    }

    /**
     * @test
     *
     * @covers ::back
     */
    public function it_goes_back_from_west_to_east()
    {
        $this->robot->advance()->turnRight()->advance()->turnLeft()->turnLeft()->back();

        $this->assertEquals(['X' => 2, 'Y' => 2], $this->robot->getPosition());
        $this->assertEquals(70, $this->robot->getBattery());
    }

    /**
     * @test
     *
     * @covers ::back
     */
    public function it_goes_back_from_south_to_north()
    {
        $this->robot->advance()->advance()->advance()->turnRight()->advance()->turnRight()->advance()->back();

        $this->assertEquals(['X' => 0, 'Y' => 1], $this->robot->getPosition());
        $this->assertEquals(65, $this->robot->getBattery());
    }

    /**
     * @test
     *
     * @covers ::back
     */
    public function it_goes_back_from_north_to_south()
    {
        $this->robot->advance()->back();

        $this->assertEquals(['X' => 3, 'Y' => 0], $this->robot->getPosition());
        $this->assertEquals(75, $this->robot->getBattery());
    }

    /**
     * @test
     *
     * @covers ::back
     *
     * @expectedException \MyQ\Exceptions\ObstacleException
     * @expectedExceptionMessage Cannot go back.
     */
    public function it_hits_the_obstacle_while_going_back()
    {
        $this->robot->back();
    }

    /**
     * @test
     *
     * @covers ::back
     *
     * @expectedException \MyQ\Exceptions\OutOfBatteryException
     * @expectedExceptionMessage Out of battery.
     */
    public function it_runs_out_of_battery_when_going_back()
    {
        $this->robot->setBattery(2);
        $this->robot->back();
    }
}
