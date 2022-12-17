<?php

namespace DeLoachTech\Request;

trait ValidateTrait
{


    /**
     * Validates the callback function bool result.
     * @param string $elementName
     * @param callable $callback Must return a bool.
     * @param string $errorMessage
     * @return $this
     */
    public function validate(string $elementName, callable $callback, string $errorMessage = 'This value is invalid.'): self
    {
        if (call_user_func($callback) !== true) {
            $this->errors[$elementName][] = $errorMessage;
        }
        return $this;
    }


    public function validateStringLength(string $elementName, $min, $max = null): self
    {
        $len = strlen($this->values[$elementName]);

        if ($len < $min) {
            if ($max) {
                $this->errors[$elementName][] = "Value is too short. Minimum {$min} characters (maximum {$max}).";
            }
            else {
                $this->errors[$elementName][] = "Value needs to be {$min} or more characters.";
            }
        }
        elseif ($max && $len > $max) {
            $this->errors[$elementName][] = "Value is too long. Maximum {$max} characters (minimum {$min}).";
        }
        return $this;
    }


    public function validateEmail(string $elementName = 'email'): self
    {
        if(!empty($this->values[$elementName])) {
            if (!filter_var($this->values[$elementName], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$elementName][] = "Invalid email address.";
            }
        }
        return $this;
    }


    public function validatePhoneNumber(string $elementName): self
    {
        $phoneRegex = '/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/';
        if (!preg_match($phoneRegex, $this->values[$elementName])) {
            $this->errors[$elementName][] = "Invalid phone number format.";
        }
        return $this;
    }


    public function validateValuesMatch(array $elementNames, string $message): self
    {
        if (!empty($this->values[$elementNames[0]]) && !empty($this->values[$elementNames[1]])) {
            if ($this->values[$elementNames[0]] != $this->values[$elementNames[1]]) {
                $this->errors[$elementNames[0]][] = $message;
            }
        }
        return $this;
    }


    public function validateValuesDoNotMatch(array $elementNames, string $message): self
    {
        if (!empty($this->values[$elementNames[0]]) && !empty($this->values[$elementNames[1]])) {
            if ($this->values[$elementNames[0]] == $this->values[$elementNames[1]]) {
                $this->errors[$elementNames[0]][] = $message;
            }
        }
        return $this;
    }


    public function validateSelected(string $elementName, int $minimumSelected = 1): self
    {
        if (empty($this->values[$elementName]) || count($this->values[$elementName]) < $minimumSelected) {
            $this->errors[$elementName][] = $minimumSelected > 0 ? "Minimum of {$minimumSelected} required." : "This value is required";
        }
        return $this;
    }


    public function validateCreditCardExpirationDate(string $monthElementName, string $yearElementName): self
    {
        $month = preg_replace('/\D/', '', $this->values[$monthElementName]);
        $year = preg_replace('/\D/', '', $this->values[$yearElementName]);

        // https://www.php.net/manual/en/function.getdate.php
        $date = getdate();

        if ($date['year'] > (int)$year) {
            $this->errors[$yearElementName][] = "The credit card is expired.";
        }
        else {
            if ($date['year'] == $year) {
                if ($date['mon'] >= (int)$month) {
                    $this->errors[$monthElementName][] = "The credit card is expired.";
                }
            }
        }
        return $this;
    }


    public function validateCreditCardSecurityCode(string $elementName): self
    {
        $code = preg_replace('/\D/', '', $this->values[$elementName]);
        $len = strlen($code);
        if ($len < 3 || $len > 4) {
            $this->errors[$elementName][] = "Security codes must be 3 or 4 digits.";
        }
        return $this;
    }


    public function validateCreditCardNumber(string $elementName): self
    {
        $number = $this->values[$elementName];

        // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
        $number = preg_replace('/\D/', '', $number);

        // Set the string length and parity
        $number_length = strlen($number);

        if ($number_length != 16) {
            $this->errors[$elementName][] = "Credit card number mus be 16 digits.";
            return $this;
        }

        $parity = $number_length % 2;

        // Loop through each digit and do the maths
        $total = 0;
        for ($i = 0; $i < $number_length; $i++) {
            $digit = $number[$i];
            // Multiply alternate digits by two
            if ($i % 2 == $parity) {
                $digit *= 2;
                // If the sum is two digits, add them together (in effect)
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            // Total up the digits
            $total += $digit;
        }

        // If the total mod 10 equals 0, the number is valid
        if ($total % 10 == 0) {
            // ok
        }
        else {
            $this->errors[$elementName][] = "Invalid credit card number.";
        }
        return $this;

    }


    public function validateNotEmpty(array $elementNames): self
    {
        foreach ($elementNames ?? [] as $name) {
            if (empty($this->values[$name])) {
                $this->errors[$name][] = "This value is required.";
            }
        }
        return $this;
    }
}