<?php

namespace GCore\RuleEngine;

use GCore\RuleEngine\Contract\DataSourceInterface;
use GCore\RuleEngine\Contract\SpecificationInterface;

/**
 * Specification class to handle and operator
 */
class AndSpecification implements SpecificationInterface
{
    /**
     * @var \GCore\SpecificationInterface  Left specification
     */
    protected $specifications;

    /**
     * Constructs the spec
     *
     * @param \GCore\SpecificationInterface $specifications  Specifications
     */
    public function __construct(SpecificationInterface ...$specifications)
    {
        $this->specifications = $specifications;
    }

    /**
     * {@inheritDoc}
     */
    public function isSatisfiedBy(DataSourceInterface $candidate) : bool
    {
        foreach ($this->specifications as $specification)
        {
            if (!$specification->isSatisfiedBy($candidate))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Specification $specification
     *
     * @return \GCore\RuleEngine\Contract\SpecificationInterface
     */
    public function append(SpecificationInterface $specification) : SpecificationInterface
    {
        return new self($this, $specification);
    }
}