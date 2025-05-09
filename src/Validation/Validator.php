<?php
namespace App\Validation;

class Validator {
    private $errors = [];

    public function validate(array $data, array $rules): void {
        $this->errors = [];
        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $data[$field] ?? null, $rule);
            }
        }
        if (!empty($this->errors)) {
            throw new \Exception(json_encode($this->errors), 422);
        }
    }

    private function applyRule(string $field, $value, string $rule): void {
        if ($rule === 'required' && (is_null($value) || $value === '')) {
            $this->errors[$field][] = "$field zorunlu";
        }
        if (strpos($rule, 'min:') === 0) {
            $minLength = (int) substr($rule, 4);
            if (strlen($value) < $minLength) {
                $this->errors[$field][] = "$field en az $minLength karakter olmalı";
            }
        }
        if (strpos($rule, 'max:') === 0) {
            $maxLength = (int) substr($rule, 4);
            if (strlen($value) > $maxLength) {
                $this->errors[$field][] = "$field en fazla $maxLength karakter olmalı";
            }
        }
        if ($rule === 'string' && !is_string($value)) {
            $this->errors[$field][] = "$field bir metin olmalı";
        }
        if (strpos($rule, 'in:') === 0) {
            $allowedValues = explode(',', substr($rule, 3));
            if (!in_array($value, $allowedValues)) {
                $this->errors[$field][] = "$field şu değerlerden biri olmalı: " . implode(',', $allowedValues);
            }
        }
        if ($rule === 'date' && !strtotime($value)) {
            $this->errors[$field][] = "$field geçerli bir tarih olmalı";
        }
        if ($rule === 'after:now' && strtotime($value) <= time()) {
            $this->errors[$field][] = "$field gelecek bir tarih olmalı";
        }
        if ($rule === 'array' && !is_array($value)) {
            $this->errors[$field][] = "$field bir dizi olmalı";
        }
        if ($rule === 'array_of_integers' && is_array($value)) {
            foreach ($value as $item) {
                if (!is_numeric($item) || (int)$item != $item) {
                    $this->errors[$field][] = "$field yalnızca tam sayılar içermeli";
                    break;
                }
            }
        }
        if ($rule === 'nullable' && is_null($value)) {
            return; // Nullable alanlar için diğer kuralları atla
        }
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
?>