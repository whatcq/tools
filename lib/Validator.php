<?php

class Validator
{
    protected $data;
    protected $rules;
    protected $messages;
    protected $errors;

    public function __construct(array $data, array $rules, array $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
        $this->errors = [];
    }

    public function passes(): bool
    {
        foreach ($this->rules as $field => $ruleSet) {
            $rules = is_string($ruleSet) ? explode('|', $ruleSet) : $ruleSet;

            foreach ($rules as $rule) {
                $this->validateRule($field, $rule);
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    protected function validateRule(string $field, string $rule)
    {
        $params = [];

        if (str_contains($rule, ':')) {
            [$ruleName, $params] = explode(':', $rule, 2);
            $params = explode(',', $params);
        } else {
            $ruleName = $rule;
        }

        $value = $this->data[$field] ?? null;
        if (!$this->judge($value, $ruleName, $params)) {
            $this->addError($field, $ruleName, $value, $params);
        }
    }

    protected function addError(string $field, string $rule, $value, array $params)
    {
        $message = $this->messages["{$field}.{$rule}"]
            ?? $this->messages[$rule]
            ?? $this->defaultMessages[$rule]
            ?? "Validation failed for {$rule} on field {$field}.";

        $message = str_replace([':attribute', ':value'], [$field, var_export($value, true)], $message);
        foreach ($params as $i => $param) {
            $message = str_replace(":param" . ($i + 1), $param, $message);
        }

        $this->errors[$field][] = $message;
    }

    protected $defaultMessages = [
        'required' => 'The :attribute field is required.',
        'string' => 'The :attribute field must be a string.',
        'int' => 'The :attribute field must be an integer.',
        'array' => 'The :attribute field must be an array.',
        'in' => 'The :attribute field must be one of the following values: :param1.',
        'array_value' => 'The :attribute field must be an array with at least one element.',
        'min' => 'The :attribute field must be at least :param2 characters long.',
        'max' => 'The :attribute field must be at most :param1 characters long.',
        'email' => 'The :attribute field must be a valid email address.',
    ];

    protected function judge($value, string $rule, $params): bool
    {
        _log($value, $rule, $params);
        return match ($rule) {
            'required' => !empty($value),
            'string' => is_string($value),
            'int' => is_int($value),
            'array' => is_array($value),
            'in' => in_array($value, $params),
            'array_value' => is_array($value) && !empty($value),
            'min' => (is_string($value) && strlen($value) >= $params[0]) || (is_numeric($value) && $value >= $params[0]),
            'max' => (is_string($value) && strlen($value) <= $params[0]) || (is_numeric($value) && $value <= $params[0]),
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL),
            default => false,
        };
    }
}

include '../php-analysis.php';

// 正向测试用例
$data = [
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 'securePassword123',
    'colors' => ['red', 'green', 'blue', 11],
];

$rules = [
    'name' => 'required|string',
    'email' => 'required|email',
    'password' => 'required|min:6|max:20',
    'colors' => 'array:string',
];

$customMessages = [
    // 'required' => 'The :param1 field is required.',
    'email.email' => 'The email must be a valid email address.',
    'password.min' => 'The password must be at least :param1 characters.',
    'password.max' => 'The password must be at most :param1 characters.',
];

$validator = new Validator($data, $rules, $customMessages);

if ($validator->passes()) {
    echo "Positive Test Passed!\n";
} else {
    echo "Positive Test Failed:\n";
    print_r($validator->errors());
}

// 反向测试用例
$data = [
    'name' => '',
    'email' => 'invalid-email',
    'password' => 'short',
];

$rules = [
    'name' => 'required|string',
    'email' => 'required|email',
    'password' => 'required|min:6|max:20',
];

$customMessages = [
    // 'required' => 'The :param1 field is required.',
    'email.email' => 'The email must be a valid email address.',
    'password.min' => 'The password must be at least :param1 characters.',
    'password.max' => 'The password must be at most :param1 characters.',
];

$validator = new Validator($data, $rules, $customMessages);

if (!$validator->passes()) {
    echo "<pre>Negative Test Passed!\n";
    print_r($validator->errors());
} else {
    echo "Negative Test Failed:\n";
}
