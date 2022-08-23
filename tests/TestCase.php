<?php

namespace Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, InteractsWithSession;

    protected string $baseUrl;

    protected function setup(): void
    {
        parent::setUp();
        $this->baseUrl = config('app.url') . '/api/v1';
        $this->faker = \Faker\Factory::create();
    }
}
