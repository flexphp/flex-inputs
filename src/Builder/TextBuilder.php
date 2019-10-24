<?php

namespace FlexPHP\Inputs\Builder;

use FlexPHP\Inputs\Builder\AbstractBuilder;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TextBuilder extends AbstractBuilder
{
    protected function getType(): string
    {
        return TextType::class;
    }
}
