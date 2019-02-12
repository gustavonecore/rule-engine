<?php

namespace GCore\RuleEngineException;

use InvalidArgumentException;

/**
 * Class to handle not found keys
 */
class KeyNotFoundException extends InvalidArgumentException
{
    /**
     * Construct the exception.
     *
     */
    public function __construct(string $key)
    {
        parent::__construct('Not found item for key ' . $key);
    }
}
