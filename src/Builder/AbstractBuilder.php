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

use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array<string>
     */
    protected $options;

    public function getName(): string
    {
        return \preg_replace('/(\s)+/', '_', \trim($this->name)) ?? $this->name;
    }

    /**
     * @return array<string>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return FormBuilderInterface<string>
     */
    abstract protected function factory(): FormBuilderInterface;

    /**
     * @return \Twig\Environment
     */
    protected function twig()
    {
        $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDirectory = \dirname((string)$appVariableReflection->getFileName());

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
