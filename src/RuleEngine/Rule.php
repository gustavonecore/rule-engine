<?php

namespace GCore\RuleEngine;

use GCore\RuleEngine\Contract\DataSourceInterface;
use GCore\RuleEngine\Contract\SpecificationInterface;
use GCore\RuleEngine\Contract\StrategyInterface;
use GCore\RuleEngine\Contract\RuleInterface;

/**
 * Concrete implementation of interface
 */
class Rule implements RuleInterface
{
    /**
     * @var \GCore\RuleEngine\Contract\SpecificationInterface
     */
    protected $specification;

    /**
     * @var \GCore\RuleEngine\Contract\StrategyInterface
     */
    protected $strategy;

    /**
     * @var \GCore\RuleEngine\Contract\DataSourceInterface
     */
    protected $options;

    /**
     * Constructs the class
     *
     * @param \GCore\RuleEngine\Contract\SpecificationInterface   $specification
     * @param \GCore\RuleEngine\Contract\StrategyInterface        $strategy
     * @param \GCore\RuleEngine\Contract\DataSourceInterface      $options
     */
    public function __construct(SpecificationInterface $specification, StrategyInterface $strategy, DataSourceInterface $options = null)
    {
        $this->specification = $specification;
        $this->strategy = $strategy;
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecification() : SpecificationInterface
    {
        return $this->specification;
    }

    /**
     * {@inheritDoc}
     */
    public function getStrategy() : StrategyInterface
    {
        return $this->strategy;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->options;
    }
}