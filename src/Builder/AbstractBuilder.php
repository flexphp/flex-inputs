<?php

namespace FlexPHP\Inputs\Builder;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormInterface;

abstract class AbstractBuilder implements BuilderInterface
{
    private $name;
    private $options;
    protected $build = null;

    public function __construct(string $name, array $options)
    {
        $this->name = $name;
        $this->options = $options;
    }

    abstract protected function getType(): string;

    public function getName(): string
    {
        return preg_replace('/(\s)+/', '_', trim($this->name)) ?? $this->name;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function build(): FormInterface
    {
        return $this->factory()
            ->add(
                $this->getName(),
                $this->getType(),
                $this->parseOptions($this->getDefaultOptions($this->getOptions())),
            )
            ->getForm();
    }

    public function render(): string
    {
        return $this->twig()->createTemplate(\sprintf('{{ form_row(form.%1$s) }}', $this->getName()))->render([
            'form' => $this->build()->createView(),
        ]);
    }

    protected function factory(): FormBuilderInterface
    {
        return Forms::createFormFactory()->createBuilder(FormType::class, null);
    }

    protected function getDefaultOptions(array $options = []): array
    {
        return array_merge([
            'mapped' => false,
            'required' => false,
            'trim' => false,
        ], $options);
    }

    private function parseOptions(array $options): array
    {
        $_options = [];

        $options = array_filter($options, function ($var) {
            return !is_null($var);
        });

        foreach ($options as $option => $value) {
            switch ($option) {
                case 'Label':
                    $_options['label'] = $value;
                    break;
                case 'Default':
                    $_options['data'] = $value;
                    break;
                case 'Constraints':
                    $attributes = \json_decode($value, true);

                    if ((\json_last_error() !== JSON_ERROR_NONE)) {
                        $attributes = [$value];
                    }

                    foreach ($attributes as $attribute => $_value) {
                        if ($attribute == 'required' || $_value == 'required') {
                            $_options['required'] = true;
                        } else {
                            $_options['attr'][$attribute] = $_value;
                        }
                    }
                    break;
                case 'Help':
                    $_options['help'] = $value;
                    break;
                case 'empty_data':
                    $_options[$option] = $value;

                    if (!empty($_options['attr'])) {
                        $_options['attr'] = array_merge_recursive($_options['attr'], ['placeholder' => $value]);
                    } else {
                        $_options['attr']['placeholder'] = $value;
                    }

                    break;
                default:
                    if (is_array($value) && !empty($_options[$option])) {
                        $_options[$option] = array_merge_recursive($_options[$option], $value);
                    } else {
                        $_options[$option] = $value;
                    }
                    break;
            }

            unset($options[$option]);
        }

        return $_options;
    }

    private function twig()
    {
        $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDirectory = dirname((string)$appVariableReflection->getFileName());

        $loader = new \Twig\Loader\FilesystemLoader([
            $vendorTwigBridgeDirectory . '/Resources/views/Form',
        ]);

        $twig = new \Twig\Environment($loader);
        $twig->addExtension(new \Symfony\Bridge\Twig\Extension\FormExtension());
        $twig->addExtension(new \Symfony\Bridge\Twig\Extension\TranslationExtension(
            new \Symfony\Component\Translation\Translator('en')
        ));

        $formEngine = new \Symfony\Bridge\Twig\Form\TwigRendererEngine(['bootstrap_4_layout.html.twig'], $twig);
        $twig->addRuntimeLoader(new \Twig\RuntimeLoader\FactoryRuntimeLoader([
            \Symfony\Component\Form\FormRenderer::class => function () use ($formEngine) {
                return new \Symfony\Component\Form\FormRenderer($formEngine);
            },
        ]));

        return $twig;
    }
}
