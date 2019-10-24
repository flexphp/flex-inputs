<?php

namespace FlexPHP\Inputs;

use FlexPHP\Inputs\Builder\TextBuilder;

class Input implements InputInterface
{
    /** @codeCoverageIgnore */
    private function __construct()
    {
    }

    public static function text(string $name, array $options = []): string
    {
        return (new TextBuilder($name, $options))->render();
    }
}
