<?php

namespace FlexPHP\Inputs;

interface InputInterface
{
    /**
     * @param string $type
     * @param string $name
     * @param array<string> $options
     * @return string
     */
    public static function create(string $type, string $name, array $options = []): string;
}
