<?php


namespace Laradic\Generators\Core\Elements;

use Laradic\Generators\Core\Traits\DocBlockTrait;
use Laradic\Generators\Core\Traits\InitsCollections;
use Laradic\Generators\Core\Traits\FinalModifierTrait;
use Laradic\Generators\Core\Traits\StaticModifierTrait;
use Laradic\Generators\Core\Traits\AccessModifierTrait;
use Laradic\Generators\Core\Interfaces\NameableInterface;
use Laradic\Generators\Core\Traits\AbstractModifierTrait;
use Laradic\Generators\Core\Collections\ArgumentElementCollection;

class MethodElement extends Element implements NameableInterface
{
    use AbstractModifierTrait;
    use AccessModifierTrait;
    use DocBlockTrait;
    use FinalModifierTrait;
    use StaticModifierTrait;
    use InitsCollections;

    /** @var string */
    protected $name;

    /** @var ArgumentElement[]|ArgumentElementCollection */
    protected $arguments;

    /** @var string */
    protected $body;

    protected $collections = [
        'arguments' => ArgumentElementCollection::class,
    ];

    /**
     * MethodModel constructor.
     *
     * @param string $name
     * @param string $access
     */
    public function __construct($name, $access = 'public')
    {
        $this
            ->setName($name)
            ->setAccess($access)
            ->initCollections();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function addArgument($argument, $type = null, $default = null)
    {
        if ( ! $argument instanceof ArgumentElement) {
            $argument = new ArgumentElement($argument, $type, $default);
        }
        $this->arguments[] = $argument;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toLines()
    {
        $lines = [];
        if ($this->docBlock !== null) {
            $lines = array_merge($lines, $this->docBlock->toLines());
        }

        $function = '';
        if ($this->final) {
            $function .= 'final ';
        }
        if ($this->abstract) {
            $function .= 'abstract ';
        }
        $function .= $this->access . ' ';
        if ($this->static) {
            $function .= 'static ';
        }

        $function .= 'function ' . $this->name . '(' . $this->renderArguments() . ')';

        if ($this->abstract) {
            $function .= ';';
        }

        $lines[] = $function;
        if ( ! $this->abstract) {
            if ($this->body) {
                $lines[] = '{' . PHP_EOL . $this->body;
                $lines[] = '}';
            } else {
                $lines[] = '{';
                $lines[] = '}';
            }
        }

        return $lines;
    }

    protected function renderArguments()
    {
        $result = '';
        if ($this->arguments) {
            $arguments = [];
            foreach ($this->arguments as $argument) {
                $arguments[] = $argument->render();
            }

            $result .= implode(', ', $arguments);
        }

        return $result;
    }

    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }


}
