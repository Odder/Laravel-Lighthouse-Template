<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class CreateTypeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'create:type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new GraphQL type';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'GraphQL Type';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (parent::handle() === false && ! $this->option('force')) {
            return false;
        }

        if ($this->option('all')) {
            $this->input->setOption('model', true);
            $this->input->setOption('factory', true);
            $this->input->setOption('seed', true);
            $this->input->setOption('migration', true);
        }

        if ($this->option('model')) {
            $this->createModel();
        }

        if ($this->option('factory')) {
            $this->createFactory();
        }

        if ($this->option('seed')) {
            $this->createSeeder();
        }

        if ($this->option('migration')) {
            $this->createMigration();
        }
    }

    /**
     * Create a model for the type.
     *
     * @return void
     */
    protected function createModel()
    {
        $factory = Str::studly($this->argument('name'));

        $this->call('make:model', [
            'name' => "{$factory}",
        ]);
    }

    /**
     * Create a model factory for the type.
     *
     * @return void
     */
    protected function createFactory()
    {
        $factory = Str::studly($this->argument('name'));

        $this->call('make:factory', [
            'name' => "{$factory}Factory",
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }

    /**
     * Create a migration file for the type.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

        $this->call('make:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    /**
     * Create a seeder file for the type.
     *
     * @return void
     */
    protected function createSeeder()
    {
        $seeder = Str::studly(class_basename($this->argument('name')));

        $this->call('make:seeder', [
            'name' => "{$seeder}Seeder",
        ]);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return app_path('Console/Commands/Stubs/graphql_type.stub');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return is_dir(app_path('Type')) ? $rootNamespace.'\\Type' : $rootNamespace;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/../graphql/schemas/'.str_replace('\\', '/', $name).'.graphql';
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceName($stub, $name)
    {
        $searches = [
            ['DummyType', 'DummyLowerType'],
            ['{{ type }}', '{{ lowerType }}'],
            ['{{type}}', '{{lowerType}}'],
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$name, Str::camel($name)],
                $stub
            );
        }

        return $stub;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceName($stub, $name);
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        return $name;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['all', 'a', InputOption::VALUE_NONE, 'Generate a migration, seeder, factory, and resource controller for the type'],
            ['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the type'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the type'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the type already exists'],
            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the type'],
            ['seed', 's', InputOption::VALUE_NONE, 'Create a new seeder file for the type'],
            ['model', 'p', InputOption::VALUE_NONE, 'Create a new model file for the type'],
        ];
    }
}
