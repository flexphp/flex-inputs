<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Inputs\Tests\Unit\Builder;

use FlexPHP\Inputs\Builder\FormBuilder;
use FlexPHP\Inputs\Tests\TestCase;

class FormBuilderTest extends TestCase
{
    public function testItDefault(): void
    {
        $render = (new FormBuilder([]))->render();

        $this->assertEquals(<<<T
<form name="form" method="post">
</form>
T
, $render);
    }

    public function testItWithInputRender(): void
    {
        $render = (new FormBuilder([
            'foo' => '<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="form-control" /></div>',
        ]))->render();

        $this->assertEquals(<<<T
<form name="form" method="post">
    <div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="form-control" /></div>
</form>
T
, $render);
    }

    public function testItWithInputOptions(): void
    {
        $render = (new FormBuilder([
            'foo' => [
                'type' => 'email',
            ],
        ]))->render();

        $this->assertEquals(<<<T
<form name="form" method="post">
    <div class="form-group"><label for="form_foo">Foo</label><input type="email" id="form_foo" name="form[foo]" class="form-control" /></div>
</form>
T
, $render);
    }

    public function testItWithInputs(): void
    {
        $render = (new FormBuilder([
            'foo' => [
                'type' => 'email',
            ],
            'bar' => [
                'type' => 'textarea',
            ],
        ]))->render();

        $this->assertEquals(<<<T
<form name="form" method="post">
    <div class="form-group"><label for="form_foo">Foo</label><input type="email" id="form_foo" name="form[foo]" class="form-control" /></div>
    <div class="form-group"><label for="form_bar">Bar</label><textarea id="form_bar" name="form[bar]" class="form-control"></textarea></div>
</form>
T
, $render);
    }

    public function testItWithStringTemplate(): void
    {
        $render = (new FormBuilder([], [], [], '{{ form(form) }}'))->render();

        $this->assertEquals(<<<T
<form name="form" method="post"><div id="form"></div></form>
T
, $render);
    }

    public function testItWithFileTemplate(): void
    {
        $file = \sprintf('%1$s/../../Resources/Template.html.twig', __DIR__);

        $render = (new FormBuilder([], [], [], $file))->render();

        $this->assertEquals(<<<T
File: <form name="form" method="post"><div id="form"></div></form>
T
, $render);
    }
}
