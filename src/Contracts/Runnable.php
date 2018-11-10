<?php

namespace MyQ\Contracts;

interface Runnable
{
    /**
     * Get commands.
     *
     * @return array
     */
    public function getCommands() : array;

    /**
     * Run commands in given order.
     *
     * @return array
     */
    public function run() : array;
}
