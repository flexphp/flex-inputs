<?php

namespace FlexPHP\Inputs\Builder;

use Symfony\Component\Form\FormInterface;

interface BuilderInterface
{
    public function getName(): string;

    public function getOptions(): array;

    public function build(): FormInterface;

    public function render(): string;
}
