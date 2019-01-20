<?php

namespace Reich\Interfaces;

interface Validator
{
    /**
     * Indicate if the validator rules passes.
     * 
     * @return bool
     */
    public function passes(): bool;

    /**
     * Indicate if the validator rules failes.
     * 
     * @return bool
     */
    public function fails(): bool;

    /**
     * Setter for a validation rule.
     * 
     * @param string $rule
     * @param array $values
     * @return \Reich\Interfaces\Validator
     */
    public function setRule(string $rule, array $values): Validator;

    /**
     * Setter for validation rules.
     * 
     * @param array $rules
     * @return \Reich\Interfaces\Validator
     */
    public function setRules(array $rules): Validator;

    /**
     * Retrieve all the errors.
     * 
     * @return array
     */
    public function errors(): array;
}
