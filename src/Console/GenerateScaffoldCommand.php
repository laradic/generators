<?php
/**
 * Part of the Laradic packages.
 * MIT License and copyright information bundled with this package in the LICENSE file.
 *
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
namespace Laradic\Generators\Console;

use Composer\Factory as ComposerFactory;
use Composer\IO\BufferIO;
use Composer\Package\Package;
use Illuminate\Foundation\Providers\ComposerServiceProvider;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class GenerateScaffoldCommand
 *
 * @package     Laradic\Generators
 */
class GenerateScaffoldCommand extends GenerateCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
       # $name = $this->argument('name');
       # $path = $this->argument('path');

        $generator = $this->getGenerator();
      #  $generator->setDestinationDirPath($path);

        $io = new BufferIO();
        $composerFile = ComposerFactory::getComposerFile();
        $composer = ComposerFactory::create($io);
        $autoload = $composer->getPackage()->getAutoload();
        $config = $composer->getConfig()->all();
        #$package = new Package('laradic/dev', 'dev-master', 'dev-master');
        /** @var \Composer\Package\PackageInterface $package */
        $package = $composer->getRepositoryManager()->findPackage('laradic/dev', 'dev-master');
$requires = $package->getRequires();
        #$composer->getDownloadManager()->download($package, $composer->getPackage()->getTargetDir());

        VarDumper::dump(compact('composerFile', 'autoload', 'requires'));
        #$generator->create('database/stub')
        $this->info('scaffold generator');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
           # ['name', InputArgument::REQUIRED, 'Composer package name'],
           # ['path', InputArgument::REQUIRED, 'Path to the package'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            # ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
