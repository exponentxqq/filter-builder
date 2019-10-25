<?php

namespace Exper\FilterBuilder\src\Commands;

use Exper\FilterBuilder\Helpers;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputOption;

class MakeCriterion extends GeneratorCommand
{
    protected $name = 'make:criterion';

    protected $description = 'Create a new criterion class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/Criterion.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return trim(config('builder.criterion_namespace', $rootNamespace."\\Criteria"), '\\');
    }

    protected function getOptions()
    {
        return [
            ['table', 't', InputOption::VALUE_REQUIRED, 'db table name'],
            ['field', 'f', InputOption::VALUE_REQUIRED, 'field name']
        ];
    }

    protected function getNameInput()
    {
        if (!$this->option('field')) {
            throw new InvalidOptionException("option field must be required");
        }
        if (!$this->option('table')) {
            throw new InvalidOptionException("option table must be required");
        }
        return Helpers::formatField(
            trim($this->option('field')),
            trim($this->option('table'))
        );
    }

    protected function getArguments()
    {
        return [];
    }
}
