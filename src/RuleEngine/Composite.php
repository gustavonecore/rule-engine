<?php

namespace GCore\RuleEngine;

use GCore\RuleEngine\AndSpecification;
use GCore\RuleEngine\Contract\DataSourceInterface;
use GCore\RuleEngine\Contract\SpecificationInterface;

abstract class Composite implements SpecificationInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function isSatisfiedBy(DataSourceInterface $candidate) : bool;

    /**
     * @param Specification $specification
     *
     * @return \GCore\RuleEngine\Contract\SpecificationInterface
     */
    public function append(SpecificationInterface $specification) : SpecificationInterface
    {
        return new AndSpecification($this, $specification);
    }
}