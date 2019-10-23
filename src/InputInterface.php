<?php

namespace FlexPHP\Inputs;

interface InputInterface
{
    public static function text(string $name, array $properties = [], array $options = []): string;

    // public function number(string $name, array $properties = [], array $options = []): string;

    // public function textarea(string $name, array $properties = [], array $options = []): string;

    // public function datetime(string $name, array $properties = [], array $options = []): string;

    // public function checkbox(string $name, array $properties = [], array $options = []): string;
}
