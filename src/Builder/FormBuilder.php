<?php

namespace FlexPHP\Inputs\Builder;

use FlexPHP\Inputs\Input;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;

class FormBuilder extends AbstractBuilder
{
    /**
     * @var array<string>|null
     */
    private $data;

    /**
     * @var array<string>
     */
    private $inputs;

    /**
     * @var string|null
     */
    private $template;

    /**
     * @param array<string> $inputs
     * @param array<string> $data
     * @param array<string> $options
     * @param string|null $template
     */
    public function __construct(array $inputs, array $data = null, array $options = [], string $template = null)
    {
        $this->inputs = $this->parseInputs($inputs);
        $this->data = $data;
        $this->options = $options;
        $this->template = $template;
    }

    /**
     * @return array<string>
     */
    private function getInputs(): array
    {
        return $this->inputs;
    }

    /**
     * @return array<string>|null
     */
    private function getData(): ?array
    {
        return $this->data;
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
     * @param array<mixed> $inputs
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
