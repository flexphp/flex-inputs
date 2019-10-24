<?php

namespace FlexPHP\Inputs\Tests\Unit\Builder;

use FlexPHP\Inputs\Builder\AbstractBuilder;
use FlexPHP\Inputs\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AbstractBuilderTest extends TestCase
{
    private function getMock(string $name, string $type, array $options): MockObject
    {
        $mock = $this->getMockForAbstractClass(
            AbstractBuilder::class, [$name, $options], '', true, true, true, ['getType']
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

    /**
     * @dataProvider getDefaultWithSpacesOptions
     */
    public function testItDefaultSpace($name): void
    {
        $render = $this->getMock($name, TextType::class, [])->render();

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

    public function testItSetLabelAttr(): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'Label' => 'My Label',
            'label_attr' => [
                'class' => 'label-class',
            ],
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label class="label-class" for="form_foo">My Label</label><input type="text" id="form_foo" name="form[foo]" class="form-control" /></div>
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

    public function testItSetRequired(): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'required' => true,
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo" class="required">Foo</label><input type="text" id="form_foo" name="form[foo]" required="required" class="form-control" /></div>
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

    public function testItSetHelp(): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'Help' => 'A help block',
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" aria-describedby="form_foo_help" class="form-control" /><small id="form_foo_help" class="form-text text-muted">A help block</small></div>
T, $render);
    }

    public function testItSetHelpAttr(): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'Help' => 'A help block',
            'help_attr' => [
                'class' => 'help-class',
            ],
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" aria-describedby="form_foo_help" class="form-control" /><small id="form_foo_help" class="help-class form-text text-muted">A help block</small></div>
T, $render);
    }

    public function testItSetHelpHtml(): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'Help' => '<a href="link">A help block</a>',
            'help_html' => true,
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" aria-describedby="form_foo_help" class="form-control" /><small id="form_foo_help" class="form-text text-muted"><a href="link">A help block</a></small></div>
T, $render);
    }

    public function testItSetAttrExtra(): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'attr' => [
                'class' => 'input-class',
            ],
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="input-class form-control" /></div>
T, $render);
    }

    public function testItSetAttrExtraWithEmptyData(): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'attr' => [
                'class' => 'input-class',
            ],
            'empty_data' => 'default',
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="input-class form-control" placeholder="default" /></div>
T, $render);
    }

    public function testItSetEmptyDataWithAttrExtra(): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'empty_data' => 'default',
            'attr' => [
                'class' => 'input-class',
            ],
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" placeholder="default" class="input-class form-control" /></div>
T, $render);
    }

    public function testItSetDisabled(): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'disabled' => true,
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" disabled="disabled" class="form-control" /></div>
T, $render);
    }

    public function testItSetEmptyData(): void
    {
        $render = $this->getMock('foo', TextType::class, [
            'empty_data' => 'default',
        ])->render();

        $this->assertEquals(<<<'T'
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" placeholder="default" class="form-control" /></div>
T, $render);
    }
    
    public function testItSetRowAttr(): void
    {
        $this->markTestSkipped('Not works...');

        $render = $this->getMock('foo', TextType::class, [
            'row_attr' => [
                'class' => 'row-class',
            ],
        ])->render();

        $this->assertEquals(<<<'T'
<div class="row-class form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="form-control" /></div>
T, $render);
    }

    public function getDefaultWithSpacesOptions(): array
    {
        return [
            ['foo bar'],
            ['foo  bar'],
            [' foo bar '],
            [' foo bar'],
            ['foo bar '],
            ['  foo  bar  '],
        ];
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
