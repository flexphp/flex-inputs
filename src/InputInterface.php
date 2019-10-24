<?php

namespace FlexPHP\Inputs;

interface InputInterface
{
    public static function create(string $type, string $name, array $options = []): string;
}
