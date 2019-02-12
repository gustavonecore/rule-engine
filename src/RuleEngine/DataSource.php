<?php

namespace GCore\RuleEngine;

use GCore\RuleEngine\Contract\DataSourceInterface;
use GCore\RuleEngine\Contract\Exception\KeyNotFoundException;

/**
 * Class to wrap any kind of data
 */
class DataSource implements DataSourceInterface
{
    protected $data;

    /**
     * Constructs the class
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Helper factory method
     *
     * @param array $data
     * @return \GCore\RuleEngine\Contract\DataSourceInterface
     */
    public static function fromArray(array $data = []) : DataSourceInterface
    {
        return new self($data);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key)
    {
        if (!array_key_exists($key, $this->data))
        {
            throw new KeyNotFoundException($key);
        }

        return $this->data[$key];
    }

    public function has(string $key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __toString() : string
    {
        return json_encode($this->data);
    }

    /**
     * Get data basis array
     *
     * @return array
     */
    public function toArray() : array
    {
        return $this->data;
    }
}