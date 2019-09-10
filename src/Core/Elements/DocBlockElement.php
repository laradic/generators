<?php


namespace Laradic\Generators\Core\Elements;


class DocBlockElement extends Element
{
    /** @var string[] */
    protected $content = [];

    public function __construct(...$contents)
    {
        foreach ($contents as $content) {
            $this->addContent($content);
        }
    }

    public function toLines()
    {
        $lines = [];
        $lines[] = '/**';
        if ($this->content) {
            foreach ($this->content as $item) {
                $lines[] = sprintf(' * %s', $item);
            }
        } else {
            $lines[] = ' *';
        }
        $lines[] = ' */';

        return $lines;
    }

    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string[]|string $content
     *
     * @return $this
     */
    public function addContent($content)
    {
        if (is_array($content)) {
            foreach ($content as $item) {
                $this->addContent($item);
            }
        } else {
            $this->content[] = $content;
        }

        return $this;
    }
}
