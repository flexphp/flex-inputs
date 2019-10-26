<?php

namespace FlexPHP\Inputs\Tests\Unit;

use FlexPHP\Inputs\Input;
use FlexPHP\Inputs\Tests\TestCase;
use InvalidArgumentException;

class InputTest extends TestCase
{
    public function testItCreateTypeNotValidThrownException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('supported');

        Input::create('unknow', 'foo');
    }

    public function testItMethodTypeNotValidThrownException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('supported');

        Input::unknow('foo');
    }

    /**
     * @dataProvider getTypeNameOptions
     */
    public function testItRenderCreateText($type): void
    {
        $render = Input::create($type, 'foo');

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="form-control" /></div>
T, $render);
    }

    public function testItRenderMethodType(): void
    {
        $render = Input::text('foo');

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="form-control" /></div>
T, $render);
    }

    public function testItRenderMethodForm(): void
    {
        $render = Input::form([]);

        $this->assertEquals(<<<'T'
<form name="form" method="post">
</form>
T, $render);
    }

    public function getTypeNameOptions(): array
    {
        return [
            ['text'],
            [' text '],
            [' text'],
            ['text '],
            ['TEXT '],
            ['textType'],
            [' textType '],
            [' textType'],
            ['textType '],
            ['TextType '],
            ['Texttype '],
            ['texttype '],
            ['TEXTTYPE '],
        ];
    }
}
