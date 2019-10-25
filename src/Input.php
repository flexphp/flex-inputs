<?php

namespace FlexPHP\Inputs;

use FlexPHP\Inputs\Builder\AbstractBuilder;
use FlexPHP\Inputs\Builder\InputBuilder;

/**
 * @method static string text()
 */
class Input implements InputInterface
{
    /** @codeCoverageIgnore */
    private function __construct()
    {
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
