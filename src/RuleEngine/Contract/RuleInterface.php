<?php

namespace GCore\RuleEngine\Contract;

use GCore\RuleEngine\Contract\DataSourceInterface;
use GCore\RuleEngine\Contract\SpecificationInterface;
use GCore\RuleEngine\Contract\StrategyInterface;

/**
 * Interface to define a mix of an specification and a custom output related to it
 */
interface RuleInterface
{
    /**
     * Get the specification
     *
     * @return \GCore\RuleEngine\Contract\SpecificationInterface
     */
    public function getSpecification() : SpecificationInterface;

    /**
     * Get the rule strategy
     *
     * @return \GCore\RuleEngine\Contract\StrategyInterface
     */
    public function getStrategy() : StrategyInterface;

    /**
     * Get the specification output
     *
     * @return mixed null|\GCore\RuleEngine\Contract\DataSourceInterface
     */
    public function getOptions();
}