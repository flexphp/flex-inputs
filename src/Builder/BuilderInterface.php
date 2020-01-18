<?php

namespace FlexPHP\Inputs\Builder;

use Symfony\Component\Form\FormInterface;

interface BuilderInterface
{
    public function getName(): string;

    /**
     * @return array<string>
     */
    public function getOptions(): array;

    /**
     * @return FormInterface<string>
     */
    public function build(): FormInterface;

    public function render(): string;
}
