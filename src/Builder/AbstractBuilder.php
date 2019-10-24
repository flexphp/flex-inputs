<?php

namespace FlexPHP\Inputs\Builder;

use Exception;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormInterface;

abstract class AbstractBuilder implements BuilderInterface
{
    private $name;
    private $properties;
    private $options;
    protected $build = null;

    public function __construct(string $name, array $properties, array $options)
    {
        $this->name = $name;
        $this->properties = $properties;
        $this->options = $options;
    }

    abstract protected function getType(): string;

    public function getName(): string
    {
        return str_replace(' ', '_', $this->name);
    }

    public function getProperties(): array
    {
        return $this->properties;
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
                array_merge_recursive($this->parseProperties($this->getProperties()), $this->getDefaultOptions($this->getOptions()))
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
        return [
            'mapped' => false,
            'required' => false,
            'trim' => false,
        ] + $options;
    }

    private function parseProperties(array $properties): array
    {
        $options = [];

        $properties = array_filter($properties, function($var) {
            return !is_null($var);
        });

        foreach ($properties as $property => $value) {
            switch ($property) {
                case 'Label';
                    $options['label'] = $value;
                    break;
                case 'Default';
                    $options['data'] = $value;
                    break;
                case 'Constraints';
                    $attributes = \json_decode($value, true);

                    if ((\json_last_error() !== JSON_ERROR_NONE)) {
                        $attributes = [$value];
                    }

                    foreach ($attributes as $attribute => $_value) {
                        if ($attribute == 'required' || $_value == 'required') {
                            $options['required'] = true;
                        } else {
                            $options['attr'][$attribute] = $_value;
                        }
                    }
                    break;
                case 'InputHelp';
                    $options['help'] = $value;
                    break;
            }
        }

        return $options;
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
