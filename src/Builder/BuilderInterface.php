<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
