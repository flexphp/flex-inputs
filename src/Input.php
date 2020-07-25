<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Inputs;

use FlexPHP\Inputs\Builder\FormBuilder;
use FlexPHP\Inputs\Builder\InputBuilder;

/**
 * @method static string text(string $name, array $options = [])
 */
final class Input implements InputInterface
{
    /**
     * @param array<string> $inputs
     * @param array<string> $data
     * @param array<string> $options
     */
    public static function form(array $inputs, array $data = [], array $options = [], string $template = ''): string
    {
        return (new FormBuilder($inputs, $data, $options, $template))->render();
    }

    public static function create(string $type, string $name, array $options = []): string
    {
        return (new class($type, $name, $options) extends InputBuilder {
            public function __construct(string $type, string $name, array $options)
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

    /** @codeCoverageIgnore */
    private function __construct()
    {
    }

    /**
     * @param string $type
     * @param array<int, string> $arguments
     *
     * @return string
     */
    public static function __callStatic($type, $arguments)
    {
        return self::create($type, ...$arguments);
    }
}
