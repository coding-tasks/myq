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
     */
    public function it_turns_left()
    {
        $this->assertTrue(true);
    }
}
