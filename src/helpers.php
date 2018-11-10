<?php

namespace MyQ;

/**
 * Helper method to save unique value in array.
 *
 * @param array $source
 * @param array $value
 *
 * @return array
 */
function saveUnique(array $source, array $value) : array
{
    foreach ($source as $item) {
        if ($item['X'] === $value['X'] && $item['Y'] === $value['Y']) {
            return $source;
        }
    }

    $source[] = $value;

    return $source;
}
