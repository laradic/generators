<?php

namespace Laradic\Generators\Doc;

use Barryvdh\Reflection\DocBlock\Serializer;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Arr;
use Laradic\Generators\Doc\Doc\Doc;
use Laradic\Support\Spl\FileSearchAction;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;
use SplFileObject;

class DocSerializer
{
    /**
     * @var \Barryvdh\Reflection\DocBlock\Serializer
     */
    protected $serializer;

    protected $events;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
        $this->events = new Dispatcher();
    }

    public function transform($classes)
    {
        $classes = Arr::wrap($classes);
        foreach ($classes as $classDoc) {
            $classDoc->getTemporaryFile();
            $children = array_merge($classDoc->getProperties(), $classDoc->getMethods());
            foreach ($children as $child) {
                $this->transformDoc($child);
            }
            $this->transformDoc($classDoc);
            $tempFile = $classDoc->getTemporaryFile();
//            $tempFile->fread($tempFile->fstat()[ 'size' ]);
        }
    }

    public function writeToSourceFiles($classes)
    {
        /** @var \Laradic\Generators\Doc\Doc\ClassDoc[] $classes */
        $classes = Arr::wrap($classes);
        foreach ($classes as $classDoc) {
            $pathName = $classDoc->getReflectionFileName();
            $classDoc->writeTemporaryFileTo($pathName);
            $this->events->dispatch('write', [$classDoc]);
        }
    }

    protected function transformDoc(Doc $doc)
    {
        $tempFile        = $doc->getClassDoc()->getTemporaryFile();
        $originalContent = $tempFile->fread($tempFile->fstat()[ 'size' ]);
        $content         = $originalContent;

        $originalDocComment = $doc->getReflection()->getDocComment();
        $hasComments        = $originalDocComment !== false;
        if ( ! $hasComments) {
            $line = $this->getStartLine($doc->getReflection(), $tempFile);
            $tempFile->seek($line);
            $originalLine = $tempFile->current();
            $this->serializer->setIndent(strspn($originalLine, ' '));
            $processedDocComment = $this->serializer->getDocComment($doc->getDocblock());
            $replacementLine     = "{$processedDocComment}\n{$originalLine}";
            $content             = str_replace($originalLine, $replacementLine, $content);
        } else {
            $this->serializer->setIndent(strspn($originalDocComment, ' '));
            $processedDocComment = $this->serializer->getDocComment($doc->getDocblock());
            $content             = str_replace($originalDocComment, $processedDocComment, $content);
        }

        $tempFile->ftruncate(0);
        $tempFile->fwrite($content);
        $tempFile->rewind();
    }

    protected function getStartLine(Reflector $reflection, SplFileObject $file = null)
    {
        $file->openFile('r');
        if ($reflection instanceof ReflectionProperty) {
            $line = FileSearchAction::make($file)
                ->startAt($reflection->getDeclaringClass()->getStartLine())
                ->downwards()
                ->returnFirstMatch()
                ->matchesExpression('/.*(public|protected|private).*\\$' . $reflection->getName() . '/')
                ->getResult();
        } elseif ($reflection instanceof ReflectionMethod) {
            $line = FileSearchAction::make($file)
                ->startAt($reflection->getStartLine() + 1)
                ->downwards()
                ->returnFirstMatch()
                ->matchesExpression('/.*function.*' . $reflection->getShortName() . '/')
                ->getResult();
        } elseif ($reflection instanceof ReflectionClass) {
            $line = FileSearchAction::make($file)
                ->startAt($reflection->getStartLine() + 1)
                ->upwards()
                ->returnFirstMatch()
                ->matchesExpression('/.*(class|interface|trait).*' . $reflection->getShortName() . '/')
                ->getResult();
        }
        $file->rewind();
        return $line;
    }
}
