<?php


namespace Laradic\Generators\Core\Elements;


use Laradic\Generators\Core\Traits\ValueTrait;
use Laradic\Generators\Core\Traits\DocBlockTrait;
use Laradic\Generators\Core\Traits\AccessModifierTrait;
use Laradic\Generators\Core\Traits\StaticModifierTrait;
use Laradic\Generators\Core\Interfaces\NameableInterface;

class PropertyElement extends AbstractPropertyElement implements NameableInterface
{
    use AccessModifierTrait;
    use DocBlockTrait;
    use StaticModifierTrait;
    use ValueTrait;

    public function __construct($name, $access = 'public', $value = null)
    {
        $this
            ->setName($name)
            ->setAccess($access)
            ->setValue($value);
    }

    public function toLines()
    {
        $lines = [];
        if ($this->docBlock !== null) {
            $lines = array_merge($lines, $this->docBlock->toLines());
        }

        $property = $this->access . ' ';
        if ($this->static) {
            $property .= 'static ';
        }
        $property .= '$' . $this->name;

        if ($this->value !== null) {
            $value = $this->renderValue();
            if ($value !== null) {
                $property .= sprintf(' = %s', $this->renderValue());
            }
        }
        $property .= ';';
        $lines[] = $property;

        return $lines;
    }
}
