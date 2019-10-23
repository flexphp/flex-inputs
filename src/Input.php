<?php

namespace FlexPHP\Inputs;

use FlexPHP\Inputs\Builder\TextBuilder;

class Input implements InputInterface
{
    private function __construct()
    {
    }

    public static function text(string $name, array $properties = [], array $options = []): string
    {
        return (new TextBuilder($name, $properties, $options))->render();
    }
}
