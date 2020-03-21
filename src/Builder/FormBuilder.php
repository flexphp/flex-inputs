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

use FlexPHP\Inputs\Input;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;

class FormBuilder extends AbstractBuilder
{
    /**
     * @var null|array<string>
     */
    private $data;

    /**
     * @var array<string>
     */
    private $inputs;

    /**
     * @var null|string
     */
    private $template;

    /**
     * @param array<string, mixed> $inputs
     * @param array<string> $data
     * @param array<string> $options
     */
    public function __construct(array $inputs, array $data = null, array $options = [], string $template = null)
    {
        $this->inputs = $this->parseInputs($inputs);
        $this->data = $data;
        $this->options = $options;
        $this->template = $template;
    }

    /**
     * @return FormInterface<string>
     */
    public function build(): FormInterface
    {
        return $this
            ->factory()
            ->getForm();
    }

    public function render(): string
    {
        return $this->twig()->createTemplate($this->getTemplate(), $this->getName())->render([
            'form' => $this->build()->createView(),
            'inputs' => $this->getInputs(),
        ]);
    }

    /**
     * @return FormBuilderInterface<string>
     */
    protected function factory(): FormBuilderInterface
    {
        return Forms::createFormFactory()->createBuilder(FormType::class, $this->getData(), $this->getOptions());
    }

    /**
     * @return array<string>
     */
    private function getInputs(): array
    {
        return $this->inputs;
    }

    /**
     * @return null|array<string>
     */
    private function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array<mixed> $inputs
     *
     * @return array<mixed>
     */
    private function parseInputs($inputs): array
    {
        foreach ($inputs as $name => $options) {
            if (\is_array($options)) {
                $inputs[$name] = Input::create($options['type'] ?? 'text', $name, $options);
            }
        }

        return $inputs;
    }

    private function getTemplate(): string
    {
        $_template = <<<T
{{ form_start(form) }}
{% for input in inputs %}
    {{ input|raw }}
{% endfor %}
{{ form_end(form) }}
T;

        if ($this->template) {
            if (\is_file($this->template)) {
                $_template = \file_get_contents($this->template);
            } else {
                $_template = $this->template;
            }
        }

        return (string)$_template;
    }
}
