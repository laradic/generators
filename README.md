Laravel IntelliJ IDEA / PHPStorm package
========================================

[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

Aimed at developers using Laravel 5 in IntelliJ IDEA / PHPStorm, the package adds (configurable, optional)
helpers, improvements and automation to the IDE. A small grasp of features: 

- Advanced Metadata generators (autocompletion)
- Fixes settings
- Improves code completion
- Improved package development
- Much more...


The package follows the FIG standards PSR-1, PSR-2, and PSR-4 to ensure a high level of interoperability between shared PHP code.

Quick Installation
------------------
Begin by installing the package through Composer.

```bash
composer require laradic/idea=~1.0
```

Documentation
-------------

Go to the [Laradic documentation](https://la.radic.nl)

Quick overview
--------------

#### Advanced Metadata Generator
<small>[View topic on PHPStorm documentation](https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata)</small>
Similar to [barryvdh/laravel-ide-helper](#), `php artisan laradic:idea:meta` generates the **.phpstorm.meta.php** file inside your project root.
This will autocomplete the bindings when calling `app('<binding>')` or `App::make('<binding>')` and will spawn the code-completion for the binding.

SCREENSHOT

The `laradic/idea` version also includes **config**, **routes** and **language** autocompletion. 
It also provides an easy way to add your own completions. A good example would be "config":
```php
class ConfigMeta extends Laradic\Idea\Metadata\Metas\BaseMeta {
    protected $methods = [
        '\\config(\'\')',
        '\\Config::get(\'\')',
        'new \\Illuminate\\Contracts\\Config\\Repository',
        '\\Illuminate\\Contracts\\Config\\Repository::get(\'\')'
    ];

    public function getData(){
        return array_dot($this->app['config']->all());
    }     
}
```

