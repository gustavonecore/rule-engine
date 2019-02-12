<?php

namespace GCore\RuleEngine\Contract;

use GCore\RuleEngine\Contract\DataSourceInterface;

interface StrategyInterface
{
    /**
     * Execute the given strategy
     *
     * @param \GCore\RuleEngine\Contract\DataSourceInterface  $payload   Payload required by the strategy
     * @param \GCore\RuleEngine\Contract\DataSourceInterface  $meta      Optional datasource metadata
     * @return void|mixed
     */
    public function execute(DataSourceInterface $payload, DataSourceInterface $meta = null);
}