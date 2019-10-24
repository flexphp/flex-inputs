<?php

namespace FlexPHP\Inputs\Tests\Unit\Builder;

use FlexPHP\Inputs\Builder\AbstractBuilder;
use FlexPHP\Inputs\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AbstractBuilderTest extends TestCase
{
    private function getMock(string $name, string $type, array $properties): MockObject
    {
        $mock = $this->getMockForAbstractClass(
            AbstractBuilder::class, [$name, $properties, []], '', true, true, true, ['getType']
        );

        $mock->method('getType')->will($this->returnValue($type));

        return $mock;
    }

    public function testItDefault(): void
    {
        $render = $this->getMock('foo', TextType::class, [])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="form-control" /></div>
T, $render);
    }

    public function testItDefaultSlug(): void
    {
        $render = $this->getMock('foo_bar', TextType::class, [])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo_bar">Foo bar</label><input type="text" id="form_foo_bar" name="form[foo_bar]" class="form-control" /></div>
T, $render);
    }

    public function testItDefaultSpace(): void
    {
        $render = $this->getMock('foo bar', TextType::class, [])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo_bar">Foo bar</label><input type="text" id="form_foo_bar" name="form[foo_bar]" class="form-control" /></div>
T, $render);
    }

    public function testItSetLabel(): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'Label' => 'My Label',
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">My Label</label><input type="text" id="form_foo" name="form[foo]" class="form-control" /></div>
T, $render);
    }

    public function testItSetDefault(): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'Default' => 'fuz',
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="form-control" value="fuz" /></div>
T, $render);
    }

    /**
     * @dataProvider getRequiredOptions
     *
     * @param string|array $required
     * @return void
     */
    public function testItSetRequiredConstraint($required): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'Constraints' => $required,
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo" class="required">Foo</label><input type="text" id="form_foo" name="form[foo]" required="required" class="form-control" /></div>
T, $render);
    }

    public function testItSetInputHelp(): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'InputHelp' => 'A help block',
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" aria-describedby="form_foo_help" class="form-control" /><small id="form_foo_help" class="form-text text-muted">A help block</small></div>
T, $render);
    }

    public function getRequiredOptions(): array
    {
        return [
            [\json_encode(['required'])],
            [\json_encode(['required' => true])],
            [\json_encode(['required' => 'true'])],
            [\json_encode(['required' => 'required'])],
            ['required'],
        ];
    }
}
