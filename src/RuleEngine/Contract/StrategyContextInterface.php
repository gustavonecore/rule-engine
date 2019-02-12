<?php

namespace GCore\RuleEngine\Contract;

use GCore\RuleEngine\Contract\StrategyInterface;

interface StrategyContextInterface
{
    /**
     * Get an strategy based on the given name
     *
     * @param string $context  Context to be used as selector of strategy
     * @return \GCore\RuleEngine\Contract\StrategyInterface
     */
    public function getStrategy(string $context) : StrategyInterface;
}