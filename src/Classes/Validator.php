<?php

namespace Reich\Classes;

use Reich\Types\Rule;

use Reich\Interfaces\Input;
use Reich\Interfaces\Validator as ValidatorInterface;
use Prophecy\Exception\InvalidArgumentException;

class Validator implements ValidatorInterface
{
    /**
     * Stores the input.
     * 
     * @var \Reich\Interfaces\Input
     */
    private $input;

    /**
     * Stores the rules.
     * 
     * @var array
     */
    private $rules;

    /**
     * Stores the failed rules.
     * 
     * @var array
     */
    private $failedRules;

    /**
     * Initialize:
     * - the input
     * - the rules
     */
    public function __construct(Input $input, array $rules = [])
    {
        $this->input = $input;
        $this->rules = $rules;
        $this->failedRules = [];
    }

    /**
     * Indicate if the validator rules passes.
     * 
     * @return bool
     */
    public function passes(): bool
    {
        foreach ($this->rules as $ruleName => $validRules) {
            foreach ($this->input->getFiles() as $file) {
                switch ($ruleName) {
                    case Rule::Size:
                        $maxSize = $validRules;

                        if ($file->getSize() > $maxSize) {
                            $this->failedRules[$ruleName][] = $file; 
                        }

                        break;
                    case Rule::Extensions:
                        $allowedExtensions = $validRules;
                      
                        $extension = pathinfo($file->getName(), PATHINFO_EXTENSION);

                        if (! in_array($extension, $allowedExtensions)) {
                            $this->failedRules[$ruleName][] = $file;
                        }
                   
                        break;
                    case Rule::MimeTypes:
                        $allowedMimeTypes = $validRules;
                        $mimeType = mime_content_type($file->getTmpName());

                        if (! in_array($mimeType, $allowedMimeTypes)) {
                            $this->failedRules[$ruleName][] = $file;
                        }

                        break;
                }
            }
        }

        return empty($this->failedRules);
    }

    /**
     * Indicate if the validator rules failes.
     * 
     * @return bool
     */
    public function fails(): bool
    {
        return $this->passes() === false;
    }

    /**
     * Setter for a validation rule.
     * 
     * @param string $rule
     * @param array $values
     * @return \Reich\Interfaces\Validator
     */
    public function setRule(string $rule, array $values): ValidatorInterface
    {
        switch ($rule) {
            case Rule::Size:
                $this->maxSize = @intval($values);
                break;
            case Rule::MimeTypes:
                if (is_array($values) === false) {
                    throw new InvalidRuleException('MimeTypes rule expect an array of Mime-Types.');
                }

                $this->allowedMimeTypes = $values;
                break;
            case Rule::Extensions:
                if (is_array($values) === false) {
                    throw new InvalidRuleException('Extensions rule expect an array of extensions.');
                }

                $this->allowedExtensions = $values;
                break;
            default:
                throw new InvalidRuleException('Given rule does not exist!');
                break;
        }

        return $this;
    }

    /**
     * Setter for validation rules.
     * 
     * @param array $rules
     * @return \Reich\Interfaces\Validator
     */
    public function setRules(array $rules): ValidatorInterface
    {
        foreach ($rules as $rule => $values) {
			$this->setRule($rule, $values);
		}

		return $this;
    }

    /**
     * Retrieve all the errors.
     * 
     * @return array
     */
    public function errors(): array
    {
        return $this->failedRules;
    }
}
