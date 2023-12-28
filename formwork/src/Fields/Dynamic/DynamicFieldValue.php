<?php

namespace Formwork\Fields\Dynamic;

use Formwork\Exceptions\RecursionException;
use Formwork\Fields\Field;
use Formwork\Interpolator\Interpolator;

class DynamicFieldValue
{
    /**
     * @var array<string, mixed>
     */
    public static array $vars = [];

    /**
     * Dynamic value computation status
     */
    protected bool $computed = false;

    /**
     * Whether the dynamic value is being computed
     */
    protected bool $computing = false;

    /**
     * Computed value
     */
    protected mixed $value;

    /**
     * @param string $key             Dynamic value key
     * @param string $uncomputedValue Uncomputed value
     * @param Field  $field           Field to which the value belongs
     */
    public function __construct(protected string $key, protected string $uncomputedValue, protected Field $field)
    {
    }

    /**
     * Create an instance with an already computed value
     * (used by field validation)
     *
     * @internal
     */
    public static function withComputed(string $value, self $dynamic): self
    {
        $instance = clone $dynamic;
        $instance->computed = true;
        $instance->value = $value;
        return $instance;
    }

    /**
     * Compute dynamic field value
     */
    public function compute(): void
    {
        if ($this->computed) {
            return;
        }

        if ($this->computing) {
            throw new RecursionException(sprintf('Recursion in the computation of dynamic property "%s" of field "%s". Trying to compute "%s"', $this->key, $this->field->name(), $this->uncomputedValue));
        }

        $this->computing = true;

        $this->value = Interpolator::interpolate($this->uncomputedValue, [...self::$vars, 'this' => $this->field]);

        $this->computed = true;

        $this->computing = false;
    }

    /**
     * Get the key associated to the dynamic value
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * Get the computed value
     */
    public function value(): mixed
    {
        if (!$this->computed) {
            $this->compute();
        }

        return $this->value;
    }

    /**
     * Return whether the dynamic value has been computed
     */
    public function isComputed(): bool
    {
        return $this->computed;
    }

    /**
     * Return the field to which the dynamic value belongs
     */
    public function field(): Field
    {
        return $this->field;
    }
}
