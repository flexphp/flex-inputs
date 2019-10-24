<?php

namespace FlexPHP\Inputs;

interface InputInterface
{
    public static function text(string $name, array $options = []): string;

    // public function number(string $name, array $options = []): string;

    // public function textarea(string $name, array $options = []): string;

    // public function datetime(string $name, array $options = []): string;

    // public function checkbox(string $name, array $options = []): string;
}
