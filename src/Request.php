<?php

namespace DeLoachTech\Request;

class Request
{
    use ValidateTrait;

    private $values;
    private $errors = [];

    public function __construct()
    {
        $this->values = $this->isPost() ? $_POST : $_GET;
    }


    public function addError(string $name, string $value): self
    {
        $this->errors[$name][] = $value;
        return $this;

    }


    public function isset(string $value): bool
    {
        return !empty($this->values[$value]);
    }


    public function value(string $name)
    {
        return $this->values[$name]??null;
    }


    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }


    public function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }


    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }


    public function getErrors(): array
    {
        $errors = [];
        foreach ($this->errors as $k => $v) {
            $errors[$k] = implode(" ", $v);
        }
        return $errors;
    }



}