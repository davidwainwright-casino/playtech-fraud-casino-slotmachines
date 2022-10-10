<?php

namespace Laravel\Nova;

use JsonSerializable;

class Badge implements JsonSerializable
{
    use Makeable;

    /**
     * The value for the badge.
     *
     * @var string
     */
    public $value;

    /**
     * The type for the badge.
     *
     * @var string
     */
    public $type;

    public const SUCCESS_TYPE = 'success';

    public const WARNING_TYPE = 'warning';

    public const DANGER_TYPE = 'danger';

    public const INFO_TYPE = 'info';

    /**
     * The built-in badge types and their corresponding CSS classes.
     *
     * @var array<string, string>
     */
    public static $types = [
        'success' => 'bg-green-100 text-green-600 dark:bg-green-500 dark:text-green-900',
        'info' => 'bg-sky-100 text-sky-600 dark:bg-sky-600 dark:text-sky-900',
        'danger' => 'bg-red-100 text-red-600 dark:bg-red-400 dark:text-red-900',
        'warning' => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-300 dark:text-yellow-800',
    ];

    /**
     * Create a new badge instance.
     *
     * @param  string  $value
     * @param  string  $type
     */
    public function __construct($value, $type = 'info')
    {
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * Set the type to be used for the badge.
     *
     * @param  string  $type
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'typeClass' => static::$types[$this->type],
        ];
    }
}
