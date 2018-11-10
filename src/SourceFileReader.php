<?php

namespace MyQ;

use MyQ\Contracts\FileReader;
use MyQ\Exceptions\FileException;

class SourceFileReader implements FileReader
{
    /** @var string */
    protected $filePath;

    /**
     * SourceFileReader constructor.
     *
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Read json data from source file.
     *
     * @throws FileException
     *
     * @return array
     */
    public function read() : array
    {
        if ( ! file_exists($this->filePath)) {
            throw new FileException('Invalid source file.');
        }

        $data = file_get_contents($this->filePath);
        $json = json_decode($data, true);

        if ( ! $json) {
            throw new FileException('Invalid source json.');
        }

        return $json;
    }

    /**
     * Validate data based on given rules.
     *
     * @param array $contents
     * @param array $rules
     *
     * @throws FileException
     *
     * @return null
     */
    public function validate(array $contents, array $rules)
    {
        foreach ($rules as $requiredKey) {
            $keys = [$requiredKey];

            if (false !== strpos($requiredKey, '.')) {
                $keys = explode('.', $requiredKey);
            }

            $source = $contents;

            foreach ($keys as $key) {
                if ( ! isset($source[$key])) {
                    throw new FileException('Invalid source json.');
                }

                $source = $source[$key];
            }
        }
    }
}
