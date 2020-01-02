<?php

namespace Laradic\Generators\DocBlock\Tags;

use Illuminate\Support\Str;
use Barryvdh\Reflection\DocBlock\Tag;

class TagUtil
{
    public static function resolveTagInnerName(Tag $tag)
    {
        if (method_exists($tag, 'getMethodName')) {
            return $tag->getMethodName();
        }
        if (method_exists($tag, 'getVariableName')) {
            return $tag->getVariableName();
        }
        if (method_exists($tag, 'getReference')) {
            return $tag->getReference();
        }
        return null;
    }
    public static function resolveTagName($tag)
    {
        // removes the @, returns the name
        return substr(static::resolveTagLine($tag),1);
    }

    /**
     * @param string|Tag $tag
     * @return string
     */
    public static function resolveTagLine($tag): string
    {
        if ($tag instanceof Tag) {
            $tag = $tag->getName();
        }
        if (Str::startsWith($tag, '\\')) {
            $tag = substr($tag, 1);
        }
        if (Str::is(Tag::class . '\\*Tag', $tag)) {
            $tag = Str::removeLeft(Tag::class . '\\', '', $tag);
            $tag = Str::removeRight('Tag', '', $tag);
            $tag = Str::lowerCaseFirst($tag);
            $tag = Str::kebab($tag);
        }
        if ( ! Str::startsWith($tag, '@')) {
            $tag = '@' . $tag;
        }
        return (string)$tag;
    }

    /**
     * @param string|Tag $tag
     * @return string
     */
    public static function resolveTagType($tag): string
    {
        // support {$tag} like '\Barryvdh\Reflection\DocBlock\Tag::createInstance('method', 'someMethod()')'
        if ($tag instanceof Tag) {
            return get_class($tag);
        }
        // support {$tag} like '\Barryvdh\Reflection\DocBlock\Tag\MethodTag'
        if (Str::startsWith($tag, '\\')) {
            $tag = substr($tag, 1);
        }
        // support {$tag} like 'Barryvdh\Reflection\DocBlock\Tag\MethodTag'
        if (Str::startsWith($tag, Tag::class) && Str::endsWith($tag, 'Tag')) {
            return $tag;
        }
        // support '{$tag}' like '@method'
        if (Str::startsWith($tag, '@')) {
            $tag = substr($tag, 1);
        }
        // support '{$tag}' like 'method'
        return Tag::class . '\\' . Str::upperCamelize($tag . '-tag');
    }
}