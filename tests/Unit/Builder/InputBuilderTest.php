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

use FlexPHP\Inputs\Builder\InputBuilder;
use FlexPHP\Inputs\Tests\TestCase;

class InputBuilderTest extends TestCase
{
    public function testItDefault(): void
    {
        $render = (new InputBuilder('foo', []))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="form-control" /></div>
T
, $render);
    }

    public function testItDefaultSlug(): void
    {
        $render = (new InputBuilder('foo_bar', []))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo_bar">Foo bar</label><input type="text" id="form_foo_bar" name="form[foo_bar]" class="form-control" /></div>
T
, $render);
    }

    /**
     * @dataProvider getDefaultWithSpacesOptions
     *
     * @param mixed $name
     */
    public function testItDefaultSpace($name): void
    {
        $render = (new InputBuilder($name, []))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo_bar">Foo bar</label><input type="text" id="form_foo_bar" name="form[foo_bar]" class="form-control" /></div>
T
, $render);
    }

    public function testItSetLabel(): void
    {
        $render = (new InputBuilder('foo', [
            'Label' => 'My Label',
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">My Label</label><input type="text" id="form_foo" name="form[foo]" class="form-control" /></div>
T
, $render);
    }

    public function testItSetLabelAttr(): void
    {
        $render = (new InputBuilder('foo', [
            'Label' => 'My Label',
            'label_attr' => [
                'class' => 'label-class',
            ],
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label class="label-class" for="form_foo">My Label</label><input type="text" id="form_foo" name="form[foo]" class="form-control" /></div>
T
, $render);
    }

    public function testItSetDefault(): void
    {
        $render = (new InputBuilder('foo', [
            'Default' => 'fuz',
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="form-control" value="fuz" /></div>
T
, $render);
    }

    public function testItSetType(): void
    {
        $render = (new InputBuilder('foo', [
            'Type' => 'email',
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="email" id="form_foo" name="form[foo]" class="form-control" /></div>
T
, $render);
    }

    public function testItSetRequired(): void
    {
        $render = (new InputBuilder('foo', [
            'Required' => true,
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo" class="required">Foo</label><input type="text" id="form_foo" name="form[foo]" required="required" class="form-control" /></div>
T
, $render);
    }

    /**
     * @dataProvider getRequiredOptions
     *
     * @param array|string $required
     */
    public function testItSetRequiredConstraint($required): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => $required,
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo" class="required">Foo</label><input type="text" id="form_foo" name="form[foo]" required="required" class="form-control" /></div>
T
, $render);
    }

    public function testItSetTypeConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'type' => 'email',
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="email" id="form_foo" name="form[foo]" class="form-control" /></div>
T
, $render);
    }

    public function testItSetTypeAttrConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'attr' => [
                'type' => 'email',
            ],
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="email" id="form_foo" name="form[foo]" class="form-control" /></div>
T
, $render);
    }

    public function testItSetDigitsConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'type' => 'digits',
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" data-parsley-type="digits" class="form-control" /></div>
T
, $render);
    }

    public function testItSetAlphanumConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'Type' => 'Alphanum',
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" data-parsley-type="alphanum" class="form-control" /></div>
T
, $render);
    }

    public function testItSetMinLengthConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'minlength' => 5,
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" minlength="5" class="form-control" /></div>
T
, $render);
    }

    public function testItSetMaxLengthConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'maxlength' => 666,
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" maxlength="666" class="form-control" /></div>
T
, $render);
    }

    public function testItSetLengthConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'length' => '[6,10]',
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" data-parsley-length="[6,10]" class="form-control" /></div>
T
, $render);
    }

    public function testItSetMinConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'min' => 3,
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" min="3" class="form-control" /></div>
T
, $render);
    }

    public function testItSetMaxConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'max' => 99,
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" max="99" class="form-control" /></div>
T
, $render);
    }

    public function testItSetRangeConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'range' => '6,10',
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label>        <input type="range" id="form_foo" name="form[foo]" min="6" max="10" class="form-control" /></div>
T
, $render);
    }

    public function testItSetPatternConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'pattern' => "\+d",
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" pattern="\+d" class="form-control" /></div>
T
, $render);
    }

    public function testItSetMinCheckConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'mincheck' => '3',
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" data-parsley-mincheck="3" class="form-control" /></div>
T
, $render);
    }

    public function testItSetMaxCheckConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'maxcheck' => 5,
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" data-parsley-maxcheck="5" class="form-control" /></div>
T
, $render);
    }

    public function testItSetCheckConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'check' => '[1,3]',
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" data-parsley-check="[1,3]" class="form-control" /></div>
T
, $render);
    }

    public function testItSetEqualToConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'equalto' => '#another',
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" data-parsley-equalto="#another" class="form-control" /></div>
T
, $render);
    }

    public function testItSetDataParsleyConstraint(): void
    {
        $render = (new InputBuilder('foo', [
            'Constraints' => \json_encode([
                'data-parsley-validator-foo' => '#bar',
            ]),
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" data-parsley-validator-foo="#bar" class="form-control" /></div>
T
, $render);
    }

    public function testItSetHelp(): void
    {
        $render = (new InputBuilder('foo', [
            'Help' => 'A help block',
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" aria-describedby="form_foo_help" class="form-control" /><small id="form_foo_help" class="form-text text-muted">A help block</small></div>
T
, $render);
    }

    public function testItSetHelpAttr(): void
    {
        $render = (new InputBuilder('foo', [
            'Help' => 'A help block',
            'help_attr' => [
                'class' => 'help-class',
            ],
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" aria-describedby="form_foo_help" class="form-control" /><small id="form_foo_help" class="help-class form-text text-muted">A help block</small></div>
T
, $render);
    }

    public function testItSetHelpHtml(): void
    {
        $render = (new InputBuilder('foo', [
            'Help' => '<a href="link">A help block</a>',
            'help_html' => true,
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" aria-describedby="form_foo_help" class="form-control" /><small id="form_foo_help" class="form-text text-muted"><a href="link">A help block</a></small></div>
T
, $render);
    }

    public function testItSetAttrExtra(): void
    {
        $render = (new InputBuilder('foo', [
            'attr' => [
                'class' => 'input-class',
            ],
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="input-class form-control" /></div>
T
, $render);
    }

    public function testItSetAttrExtraWithEmptyData(): void
    {
        $render = (new InputBuilder('foo', [
            'attr' => [
                'class' => 'input-class',
            ],
            'empty_data' => 'default',
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="input-class form-control" placeholder="default" /></div>
T
, $render);
    }

    public function testItSetEmptyDataWithAttrExtra(): void
    {
        $render = (new InputBuilder('foo', [
            'empty_data' => 'default',
            'attr' => [
                'class' => 'input-class',
            ],
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" placeholder="default" class="input-class form-control" /></div>
T
, $render);
    }

    public function testItSetDisabled(): void
    {
        $render = (new InputBuilder('foo', [
            'disabled' => true,
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" disabled="disabled" class="form-control" /></div>
T
, $render);
    }

    public function testItSetEmptyData(): void
    {
        $render = (new InputBuilder('foo', [
            'empty_data' => 'default',
        ]))->render();

        $this->assertEquals(<<<T
<div class="form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" placeholder="default" class="form-control" /></div>
T
, $render);
    }

    public function testItSetRowAttr(): void
    {
        $this->markTestSkipped('Not works...');

        $render = (new InputBuilder('foo', [
            'row_attr' => [
                'class' => 'row-class',
            ],
        ]))->render();

        $this->assertEquals(<<<T
<div class="row-class form-group"><label for="form_foo">Foo</label><input type="text" id="form_foo" name="form[foo]" class="form-control" /></div>
T
, $render);
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
