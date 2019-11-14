<?php


namespace Laradic\Generators\DocBlock;


use Illuminate\Foundation\Bus\DispatchesJobs;
use Laradic\Generators\DocBlock\Command\GenerateTempFiles;
use Laradic\Generators\DocBlock\Definition\ClassDefinition;

class ProcessedClassDefinition
{
    use DispatchesJobs;

    /** @var ClassDefinition */
    protected $class;

    /** @var string */
    protected $doc;

    public function __construct(ClassDefinition $class, string $doc)
    {
        $this->class = $class;
        $this->doc   = $doc;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getDoc()
    {
        return $this->doc;
    }

    public function setDoc($doc)
    {
        $this->doc = $doc;
        return $this;
    }



    public function content($clear = false)
    {
        /** @var \Illuminate\Support\Collection|\SplTempFileObject[] $tempFiles */
        $tempFiles = $this->dispatchNow(new GenerateTempFiles($this->class->collect()));
        /** @var \SplTempFileObject $tempFile */
        $tempFile = $tempFiles->get($this->class->getFilePathname());
        $tempFile->rewind();
        $contents = $tempFile->fread($tempFile->fstat()['size']);
        $tempFile->rewind();
        return $contents;

        $tempFile = $tempFiles->first();
        $originalDocComment = $this->class->getDocComment();
        $classname          = $this->class->getShortName();
        $filename           = $this->class->getFileName();
        $contents           = $this->class->getContent();
        /** @noinspection ClassMemberExistenceCheckInspection */
        $type = method_exists($this->class, 'isInterface') && $this->class->isInterface() ? 'interface' : 'class';

        if ($originalDocComment && $clear) {
            $this->clearClassDoc($originalDocComment);
            $originalDocComment = null;
        }

        if ($originalDocComment) {
            $contents = str_replace($originalDocComment, $this->doc, $contents);
        } else {
            $needle  = "{$type} {$classname}";
            $replace = "{$this->doc}\n{$type} {$classname}";
            $pos     = strpos($contents, $needle);
            if ($pos !== false) {
                $contents = substr_replace($contents, $replace, $pos, strlen($needle));
            }
        }
        return $contents;
    }

    public function clearClassDoc(string $content)
    {
        $content= preg_replace('/\/\*\*[\w\W]*?\*\/[\s\t]*?\n[\s\t]*?(class|abstract|interface|trait)/','$1',$content);
        return $content;
    }

}