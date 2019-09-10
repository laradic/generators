<?php

namespace Laradic\Tests\Generators;

use Laradic\Support\Commands\AddMixins;

class TestCase extends \Laradic\Testing\Native\AbstractTestCase
{

    public function setUp(): void
    {
        (new AddMixins())->withDefaultMixins()->handle();
    }
}
