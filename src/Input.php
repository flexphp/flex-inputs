<?php

namespace FlexPHP\Inputs;

use FlexPHP\Inputs\Builder\AbstractBuilder;
use InvalidArgumentException;

/**
 * @method string text()
 */
class Input implements InputInterface
{
    /** @codeCoverageIgnore */
    private function __construct()
    {
    }

    public static function create(string $type, string $name, array $options = []): string
    {
        $type = preg_replace('/type$/i', '', trim($type)) ?? $type;
        $classType = \sprintf('\Symfony\Component\Form\Extension\Core\Type\%1$sType', $type);

        if (!\class_exists($classType)) {
            throw new InvalidArgumentException(\sprintf('Type [%1$s] is not supported', $type));
        }

        return (new class($name, $options, $classType) extends AbstractBuilder {
            private $type;

            public function __construct($name, $options, $type)
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
