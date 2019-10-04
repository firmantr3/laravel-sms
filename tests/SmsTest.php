<?php 

namespace Firmantr3\Sms\Test;

use Firmantr3\Sms\Facade\Sms;
use GuzzleHttp\Psr7\Response;

class SmsTest extends TestCase {

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('sms.channels.nusasms.credentials.user', 'example_user');
        $app['config']->set('sms.channels.nusasms.credentials.password', 'secret');
    }

    public function testSendSmsNusa() {
        $sms = Sms::text('test text')->phone('081138702880');
        $response = $sms->send();

        $this->assertEquals([
            'user' => 'example_user',
            'GSM' => '081138702880',
            'SMSText' => 'test text',
            'password' => 'secret',
            'output' => 'json',
        ], $sms->payload());

        $this->assertIsObject($response);
        $this->assertEquals('-5', $response->results[0]->status);
        $this->assertEquals('6281138702880', $response->results[0]->destination);
        $this->assertInstanceOf(Response::class, $sms->getResponse());

        $sms = Sms::text('test text 2')->phone('081138702880');
        $response = $sms->send();

        $this->assertEquals([
            'user' => 'example_user',
            'GSM' => '081138702880',
            'SMSText' => 'test text 2',
            'password' => 'secret',
            'output' => 'json',
        ], $sms->payload());

        $this->assertIsObject($response);
        $this->assertEquals('-5', $response->results[0]->status);
        $this->assertEquals('6281138702880', $response->results[0]->destination);
        $this->assertInstanceOf(Response::class, $sms->getResponse());
    }
    
}
