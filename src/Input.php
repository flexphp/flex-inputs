<?php

namespace FlexPHP\Inputs;

use FlexPHP\Inputs\Builder\FormBuilder;
use FlexPHP\Inputs\Builder\InputBuilder;

/**
 * @method static string text(string $name, array $options = [])
 */
class Input implements InputInterface
{
    /** @codeCoverageIgnore */
    private function __construct()
    {
    }

    public static function form(array $inputs, $data = null, array $options = [], string $template = null): string
    {
        return (new FormBuilder($inputs, $data, $options, $template))->render();
    }

    public static function create(string $type, string $name, array $options = []): string
    {
        return (new class($type, $name, $options) extends InputBuilder {
            public function __construct($type, $name, $options)
            {
                $this->type = $type;

                parent::__construct($name, $options);
            }

            protected function getType(): string
            {
                return $this->type;
            }
        })->render();
    }

    public static function __callStatic($name, $arguments)
    {
        return self::create($name, ...$arguments);
    }
}
