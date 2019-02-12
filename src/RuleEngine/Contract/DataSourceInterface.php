<?php

namespace GCore\RuleEngine\Contract;

interface DataSourceInterface
{
    /**
     * Get the store value
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * Set the store value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value);
}