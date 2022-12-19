<?php
/*
 * This file is part of the deloachtech/request package.
 *
 * Copyright (c) DeLoach Tech
 * https://deloachtech.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DeLoachTech\Request;

class Request
{
    use ValidateTrait;

    private $values;
    private $errors = [];
    private $time;


    public function __construct()
    {
        $this->values = $this->isPost() ? $_POST : $_GET;
        $this->time = $_SERVER['REQUEST_TIME'] ?? time();
    }


    public function getTime(): int
    {
        return $this->time;
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
        return $this->values[$name] ?? null;
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