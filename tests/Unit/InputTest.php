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
<input type="text" id="form_foo" name="form[foo]" required="required" class="form-control" />
T, $render);
    }
}
