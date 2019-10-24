<?php

namespace FlexPHP\Inputs\Tests\Unit;

use FlexPHP\Inputs\Input;
use FlexPHP\Inputs\Tests\TestCase;

class InputTest extends TestCase
{
    public function testItRenderText(): void
    {
        $render = Input::text('foo');

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="form-control" /></div>
T, $render);
    }
}
