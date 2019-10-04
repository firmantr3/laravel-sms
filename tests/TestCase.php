<?php

namespace Firmantr3\Sms\Test;

use Firmantr3\Sms\SmsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{

    public function setUp(): void
    {
        parent::setUp();
        //
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        //
        parent::tearDown();
    }
    
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SmsServiceProvider::class,
        ];
    }
}
