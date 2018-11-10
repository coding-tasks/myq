<?php

namespace MyQ\Contracts;

interface Walkable
{
    /**
     * Turn left from given position.
     *
     * @return self
     */
    public function turnLeft() : self;

    /**
     * Turn right from given position.
     *
     * @return self
     */
    public function turnRight() : self;

    /**
     * Advance one step from given position.
     *
     * @return self
     */
    public function advance() : self;

    /**
     * Go back without changing direction.
     *
     * @return self
     */
    public function back() : self;

    /**
     * Back off in case of obstacle.
     *
     * @param int $attempt
     *
     * @return void
     */
    public function backOff(int $attempt);
}
