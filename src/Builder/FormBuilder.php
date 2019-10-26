<?php

namespace FlexPHP\Inputs\Builder;

use FlexPHP\Inputs\Input;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;

class FormBuilder extends AbstractBuilder
{
    private $data;
    private $inputs;
    private $template;

    public function __construct(array $inputs, array $data = null, array $options = [], string $template = null)
    {
        $this->inputs = $this->parseInputs($inputs);
        $this->data = $data;
        $this->options = $options;
        $this->template = $template;
    }

    private function getInputs(): array
    {
        return $this->inputs;
    }

    private function getData(): ?array
    {
        return $this->data;
    }

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

    protected function factory(): FormBuilderInterface
    {
        return Forms::createFormFactory()->createBuilder(FormType::class, $this->getData(), $this->getOptions());
    }

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
        $_template = <<<'T'
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

        return $_template;
    }
}
