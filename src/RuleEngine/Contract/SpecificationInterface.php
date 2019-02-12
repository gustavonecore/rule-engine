<?php

namespace GCore\RuleEngine\Contract;

use GCore\RuleEngine\Contract\DataSourceInterface;

interface SpecificationInterface
{
    /**
     * @param \GCore\RuleEngine\Contract\DataSourceInterface  Data source
     *
     * @return bool
     */
    public function isSatisfiedBy(DataSourceInterface $candidate) : bool;
}