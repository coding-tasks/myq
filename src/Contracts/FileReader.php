<?php

namespace MyQ\Contracts;

interface FileReader
{
    /**
     * Read data from source file.
     *
     * @return array
     */
    public function read() : array;

    /**
     * Validate data based on given rules.
     *
     * @param array $contents
     * @param array $rules
     *
     * @return void
     */
    public function validate(array $contents, array $rules);
}
