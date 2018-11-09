<?php

namespace MyQ;

class CleaningRobot
{
    /** @var string Source file. */
    protected $source;

    /**
     * CleaningRobot constructor.
     *
     * @param string $source
     */
    public function __construct(string $source)
    {
        $this->source = $source;
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
        // @TODO
    }

    /**
     * Turn right from given position.
     *
     * @return self
     */
    public function turnRight() : self
    {
        // @TODO
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
