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

use InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;

class InputBuilder extends AbstractBuilder
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(string $name, array $options)
    {
        $this->name = $name;
        $this->options = $this->parseOptions($this->getDefaultOptions($options));
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function build(): FormInterface
    {
        $options = $this->getOptions();
        $classType = \trim($this->getType());

        if (\strpos($classType, 'Symfony') === false) {
            // Not symfony type
            $type = \preg_replace('/type$/i', '', $classType) ?? $classType;
            $classType = \sprintf('\Symfony\Component\Form\Extension\Core\Type\%1$sType', \ucwords($type));
        } elseif (!empty($options['type']) && \stripos($classType, $options['type']) === false) {
            // Symfony type, but its diff in options type contraint
            $type = $options['type'];
            $classType = \sprintf('\Symfony\Component\Form\Extension\Core\Type\%1$sType', \ucwords($type));
        }

        unset($options['type']);

        if (!\class_exists($classType)) {
            throw new InvalidArgumentException(\sprintf('Type [%1$s] is not supported', $classType));
        }

        return $this
            ->factory()
            ->add($this->getName(), $classType, $options)
            ->getForm();
    }

    public function render(): string
    {
        return $this->twig()->createTemplate(\sprintf('{{ form_row(form.%1$s) }}', $this->getName()))->render([
            'form' => $this->build()->createView(),
        ]);
    }

    /**
     * @return FormBuilderInterface<string>
     */
    protected function factory(): FormBuilderInterface
    {
        return Forms::createFormFactory()->createBuilder(FormType::class, null);
    }

    protected function getType(): string
    {
        return TextType::class;
    }

    /**
     * @param array<string> $options
     *
     * @return array<mixed>
     */
    private function getDefaultOptions(array $options = []): array
    {
        return (array)\array_merge([
            'required' => false,
        ], $options);
    }

    /**
     * @param array<mixed> $options
     *
     * @return array<string>
     */
    private function parseOptions(array $options): array
    {
        $_options = [];

        if (!empty($options['attr']['type'])) {
            $options['type'] = $options['attr']['type'];
            unset($options['attr']['type']);
        }

        foreach ($options as $option => $value) {
            switch ($option) {
                case 'default':
                    $_options['data'] = $value;

                    break;
                case 'constraints':
                    $_options = $this->parseOptionConstraints($_options, $value);

                    break;
                case 'empty_data':
                    $_options = $this->parseOptionEmptyData($_options, $option, $value);

                    break;
                default:
                    $_options = $this->parseOption($_options, $option, $value);

                    break;
            }

            unset($options[$option]);
        }

        return $this->parseOptionTypeSpecial($_options);
    }

    private function parseOptionEmptyData(array $_options, string $option, string $value): array
    {
        $_options[$option] = $value;

        if (empty($_options['attr'])) {
            $_options['attr'] = [];
        }

        $_options['attr']['placeholder'] = $value;

        return $_options;
    }

    private function parseOptionConstraints(array $_options, array $value): array
    {
        foreach ($value as $attribute => $_value) {
            if (\is_int($attribute)) {
                $attribute = $_value;
                $_value = true;
            }

            if ($attribute === 'required') {
                $_options['required'] = $_value && !\preg_match('/^false$/i', (string)$_value);
            } elseif (\in_array($attribute, ['length', 'mincheck', 'maxcheck', 'check', 'equalto'])) {
                $_options['attr']['data-parsley-' . $attribute] = $_value;
            } elseif ($attribute === 'range') {
                $_options['type'] = $attribute;
                [$min, $max] = \explode(',', $_value);
                $_options['attr'] = \compact('min', 'max');
            } elseif ($attribute === 'type') {
                $_options['type'] = $_value;
            } else {
                $_options['attr'][$attribute] = $_value;
            }
        }

        return $_options;
    }

    private function parseOptionTypeSpecial(array $_options): array
    {
        if (!empty($_options['type'])) {
            $_type = \strtolower($_options['type']);

            if (\in_array($_type, ['digits', 'alphanum'])) {
                $_options['attr']['data-parsley-type'] = $_type;
                $_options['type'] = 'text';
            }
        }

        return $_options;
    }

    /**
     * @param mixed $value
     */
    private function parseOption(array $_options, string $option, $value): array
    {
        if (\is_array($value) && !empty($_options[$option])) {
            $_options[$option] = \array_merge_recursive($_options[$option], $value);
        } else {
            $_options[$option] = $value;
        }

        return $_options;
    }
}
