<?php

namespace GCore\RuleEngine;

use GCore\RuleEngine\Contract\DataSourceInterface;
use GCore\RuleEngine\DataSource;

/**
 * Class to process rules
 */
class RuleEngine
{
    /**
     * Constructs the class
     *
     * @param array<\GCore\RuleEngine\Contract\RuleInterface>   $rules     List of rules
     */
    public function __construct(array $rules)
    {
        $this->rules  = $rules;
    }

    /**
     * Execute the rules for the given datasource
     *
     * @param DataSourceInterface $datasource        Data source of the related row
     * @param array               $externalContext   Context data useful for the strategy
     * @return mixed
     */
    public function run(DataSourceInterface $payload, array $externalContext = [])
    {
        $output = [];

        foreach ($this->rules as $rule)
        {
            if ($rule->getSpecification()->isSatisfiedBy($payload))
            {
                $meta = null;

                if ($rule->getOptions())
                {
                    $meta = DataSource::fromArray($rule->getOptions()->toArray() + $externalContext);
                }

                $output[] = $rule->getStrategy()->execute($payload, $meta);
            }
        }

        return $output;
    }
}