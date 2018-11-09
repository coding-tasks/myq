<?php

namespace MyQ;

use MyQ\Exception\FileException;
use MyQ\Exception\BackOffException;
use MyQ\Exception\ObstacleException;
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

    /** @var array Cleaned cells. */
    protected $cleaned = [];

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
        $json = @file_get_contents($this->source);

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
     * Get battery.
     *
     * @param string $direction
     *
     * @return self
     */
    public function setDirection(string $direction) : self
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Get direction.
     *
     * @return array
     */
    public function getPosition() : array
    {
        return $this->position;
    }

    /**
     * Get visited.
     *
     * @return array
     */
    public function getVisited() : array
    {
        return $this->visited;
    }

    /**
     * Get cleaned.
     *
     * @return array
     */
    public function getCleaned() : array
    {
        return $this->cleaned;
    }

    /**
     * Run commands in given order.
     *
     * @return array
     */
    public function run() : array
    {
        foreach ($this->commands as $command) {
            switch ($command) {
                case 'TL':
                    $this->turnLeft();
                    break;

                case 'TR':
                    $this->turnRight();
                    break;

                case 'A':
                    try {
                        $this->advance();
                    } catch (ObstacleException $e) {
                        $attempt = 0;

                        do {
                            try {
                                $this->backOff($attempt);
                                break;
                            } catch (ObstacleException $e) {
                                ++$attempt;
                            }
                        } while ($attempt <= 5);
                    }

                    break;

                case 'C':
                    $this->clean();
                    break;
            }
        }

        return [
            'visited' => $this->visited,
            'cleaned' => $this->cleaned,
            'final' => $this->position + ['facing' => $this->direction],
            'battery' => $this->battery,
        ];
    }

    /**
     * Turn left from given position.
     *
     * @throws OutOfBatteryException
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
     * @throws OutOfBatteryException
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
     * @throws OutOfBatteryException
     *
     * @return self
     */
    public function advance() : self
    {
        if ($this->battery < 2) {
            throw new OutOfBatteryException('Out of battery.');
        }

        $this->battery -= 2;

        $nextX = $this->position['X'];
        $nextY = $this->position['Y'];

        switch ($this->direction) {
            case self::DIRECTION_EAST:
                $nextY += 1;
                break;

            case self::DIRECTION_WEST:
                $nextY -= 1;
                break;

            case self::DIRECTION_NORTH:
                $nextX -= 1;
                break;

            case self::DIRECTION_SOUTH:
                $nextX += 1;
                break;
        }

        if ($this->isObstacle($nextX, $nextY)) {
            throw new ObstacleException('Obstacle on the way.');
        }

        $this->position = ['X' => $nextX, 'Y' => $nextY];

        $this->saveUnique($this->visited, $this->position);

        return $this;
    }

    /**
     * Go back without changing direction.
     *
     * @return self
     */
    public function back() : self
    {
        if ($this->battery < 3) {
            throw new OutOfBatteryException('Out of battery.');
        }

        $this->battery -= 3;

        $nextX = $this->position['X'];
        $nextY = $this->position['Y'];

        switch ($this->direction) {
            case self::DIRECTION_EAST:
                $nextY -= 1;
                break;

            case self::DIRECTION_WEST:
                $nextY += 1;
                break;

            case self::DIRECTION_NORTH:
                $nextX += 1;
                break;

            case self::DIRECTION_SOUTH:
                $nextX -= 1;
                break;
        }

        if ($this->isObstacle($nextX, $nextY)) {
            throw new ObstacleException('Cannot go back.');
        }

        $this->position = ['X' => $nextX, 'Y' => $nextY];

        $this->saveUnique($this->visited, $this->position);

        return $this;
    }

    /**
     * Clean current cell.
     *
     * @return self
     */
    public function clean() : self
    {
        if ($this->battery < 5) {
            throw new OutOfBatteryException('Out of battery.');
        }

        $this->battery -= 5;

        $this->saveUnique($this->cleaned, $this->position);

        return $this;
    }

    /**
     * Back off in case of obstacle.
     *
     * @param int $attempt
     *
     * @return null
     */
    protected function backOff(int $attempt)
    {
        switch ($attempt) {
            case 0:
                $this->turnRight()->advance();
                break;

            case 1:
                $this->turnLeft()->back()->turnRight()->advance();
                break;

            case 2:
                $this->turnLeft()->turnLeft()->advance();
                break;

            case 3:
                $this->turnRight()->back()->turnRight()->advance();
                break;

            case 4:
                $this->turnLeft()->turnLeft()->advance();
                break;

            default:
                throw new BackOffException('Cannot back off.');
        }
    }

    /**
     * Check if there is obstacle at given position.
     *
     * @param int $x
     * @param int $y
     *
     * @return bool
     */
    public function isObstacle(int $x, int $y) : bool
    {
        $length = count($this->map[0]);

        if ($x < 0 || $y < 0 || $x >= $length || $y >= $length) {
            return true;
        }

        $context = $this->map[$x][$y];

        if (is_null($context) || 'C' === $context) {
            return true;
        }

        return false;
    }

    /**
     * Helper method to save unique value in array.
     *
     * @param array $source
     * @param array $value
     *
     * @return void
     */
    protected function saveUnique(array &$source, array $value) : void
    {
        foreach ($source as $item) {
            if ($item['X'] === $value['X'] && $item['Y'] === $value['Y']) {
                return;
            }
        }

        $source[] = $value;
    }
}
