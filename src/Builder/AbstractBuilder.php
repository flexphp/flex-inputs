<?php

namespace FlexPHP\Inputs\Builder;

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

    abstract protected function getFileTemplate(): string;

    public function getName(): string
    {
        return $this->name;
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
            ->add($this->getName(), $this->getType(), $this->getDefaultOptions($this->getOptions()))
            ->getForm();
    }

    public function render(): string
    {
        $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDirectory = dirname((string)$appVariableReflection->getFileName());

        $loader = new \Twig\Loader\FilesystemLoader([
            $this->getPathTemplate(),
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

        return $twig->render($this->getFileTemplate(), [
            'form' => $this->build()->createView(),
            'field' => $this->getName(),
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
        ] + $options;
    }

    protected function getPathTemplate(): string
    {
        return \sprintf('%1$s/../Template', __DIR__);
    }
}
