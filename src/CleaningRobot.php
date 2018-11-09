<?php

namespace MyQ;

use MyQ\Exception\FileException;
use MyQ\Exception\OutOfBatteryException;

class CleaningRobot
{
    /** @const string */
    const DIRECTION_EAST = 'E';

    /** @const string */
    const DIRECTION_WEST = 'W';

    /** @const string */
    const DIRECTION_NORTH = 'N';

    /** @const string */
    const DIRECTION_SOUTH = 'S';

    /** @const string */
    const MOVEMENT_LEFT = 'L';

    /** @const string */
    const MOVEMENT_RIGHT = 'R';

    /** @const array */
    const DIRECTION_MAP = [
        self::DIRECTION_EAST => [
            self::MOVEMENT_LEFT => self::DIRECTION_NORTH,
            self::MOVEMENT_RIGHT => self::DIRECTION_SOUTH,
        ],
        self::DIRECTION_WEST => [
            self::MOVEMENT_LEFT => self::DIRECTION_SOUTH,
            self::MOVEMENT_RIGHT => self::DIRECTION_NORTH,
        ],
        self::DIRECTION_NORTH => [
            self::MOVEMENT_LEFT => self::DIRECTION_WEST,
            self::MOVEMENT_RIGHT => self::DIRECTION_EAST,
        ],
        self::DIRECTION_SOUTH => [
            self::MOVEMENT_LEFT => self::DIRECTION_EAST,
            self::MOVEMENT_RIGHT => self::DIRECTION_WEST,
        ],
    ];

    /** @var string Source file. */
    protected $source;

    /** @var array Floor structure. */
    protected $map;

    /** @var array Commands to execute */
    protected $commands;

    /** @var int Battery size. */
    protected $battery;

    /** @var string Robot direction. */
    protected $direction;

    /** @var array Robot position. */
    protected $position;

    /** @var array Visited cells. */
    protected $visited = [];

    /**
     * CleaningRobot constructor.
     *
     * @param string $source
     */
    public function __construct(string $source)
    {
        $this->source = $source;

        $this->init();
    }

    /**
     * Initialize method.
     *
     * @return void
     */
    private function init()
    {
        $json = file_get_contents($this->source);

        if ( ! $json) {
            throw new FileException('Invalid source file.');
        }

        $input = json_decode($json);

        if ( ! $input) {
            throw new FileException('Invalid source json.');
        }

        $this->map       = $input->map;
        $this->battery   = $input->battery;
        $this->commands  = $input->commands;
        $this->direction = $input->start->facing;
        $this->position  = ['X' => $input->start->X, 'Y' => $input->start->Y];
        $this->visited[] = $this->position;
    }

    /**
     * Get battery.
     *
     * @return int
     */
    public function getBattery() : int
    {
        return $this->battery;
    }

    /**
     * Get battery.
     *
     * @param int $battery
     *
     * @return self
     */
    public function setBattery(int $battery) : self
    {
        $this->battery = $battery;

        return $this;
    }

    /**
     * Get direction.
     *
     * @return string
     */
    public function getDirection() : string
    {
        return $this->direction;
    }

    /**
     * Run commands in given order.
     *
     * @return array
     */
    public function run() : array
    {
        // @TODO
    }

    /**
     * Turn left from given position.
     *
     * @return self
     */
    public function turnLeft() : self
    {
        if ($this->battery < 1) {
            throw new OutOfBatteryException('Out of battery.');
        }

        $this->battery   -= 1;
        $this->direction = self::DIRECTION_MAP[$this->direction][self::MOVEMENT_LEFT];

        return $this;
    }

    /**
     * Turn right from given position.
     *
     * @return self
     */
    public function turnRight() : self
    {
        if ($this->battery < 1) {
            throw new OutOfBatteryException('Out of battery.');
        }

        $this->battery   -= 1;
        $this->direction = self::DIRECTION_MAP[$this->direction][self::MOVEMENT_RIGHT];

        return $this;
    }

    /**
     * Advance one step from given position.
     *
     * @return null
     */
    public function advance()
    {
        // @TODO
    }

    /**
     * Go back without changing direction.
     *
     * @return self
     */
    public function back() : self
    {
        // @TODO
    }

    /**
     * Clean current cell.
     */
    public function clean()
    {
        // @TODO
    }

    /**
     * Back off in case of obstacle.
     */
    protected function backOff()
    {
        // @TODO
    }
}
