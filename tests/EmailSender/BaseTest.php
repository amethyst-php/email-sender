<?php

namespace Railken\LaraOre\Tests\EmailSender;

use Illuminate\Support\Facades\File;

abstract class BaseTest extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $dotenv = new \Dotenv\Dotenv(__DIR__.'/../..', '.env');
        $dotenv->load();

        parent::setUp();

        File::cleanDirectory(database_path('migrations/'));
        $this->artisan('migrate:fresh');
        $this->artisan('vendor:publish', [
            '--provider' => 'Spatie\MediaLibrary\MediaLibraryServiceProvider',
        ]);
        // $this->artisan('vendor:publish', ['--provider' => 'Railken\LaraOre\EmailSenderServiceProvider', '--force' => true]);
        $this->artisan('migrate');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Railken\LaraOre\EmailSenderServiceProvider::class,
        ];
    }
}
