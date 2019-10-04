<?php

namespace Firmantr3\Sms;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use Firmantr3\Sms\Exceptions\SmsException;
use GuzzleHttp\Exception\RequestException;

class Sms
{
    /**
     * Selected sms channel
     *
     * @var string
     */
    protected $channel;

    /**
     * Bulk sms payloads data
     *
     * @var array
     */
    protected $bulkPayloads;

    /** @var array */
    protected $config;

    /** @var array */
    protected $payloads;

    /** @var bool */
    protected $isBulk;

    /**
     * Initialize
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->channel(config('sms.default'));
    }

    /** @return string */
    protected function getChannelConfigKey() {
        return "sms.channels.{$this->channel}";
    }

    /**
     * Get channel configurations
     *
     * @return array
     */
    protected function config($key = null)
    {
        $config = config($this->getChannelConfigKey());

        if($key !== null) {
            $key = ".{$key}";
        }

        if ($config) {
            return config($this->getChannelConfigKey() . $key);
        }

        throw new SmsException('missing channel config!');
    }

    protected function mergeConfigWithDefaults() {
        config([
            $this->getChannelConfigKey() => array_merge(
                [
                    'request_method' => 'POST',
                    'payload' => [],
                    'payload_type' => 'form_params',
                    'headers' => [],
                    'credentials' => [],
                    'keys' => [
                        'message' => 'message',
                        'phone' => 'phone',
                    ],
                    'api_url' => null,
                    'bulk' => false,
                ],
                $this->config()
            ),
        ]);
    }

    /**
     * Change sms channel
     *
     * @param string $name
     * @return self
     */
    public function channel($name)
    {
        $this->channel = $name;

        $this->mergeConfigWithDefaults();

        $this->payloads = $this->config('payloads');

        return $this;
    }

    /**
     * Get sms api url
     *
     * @return string
     */
    public function apiUrl()
    {
        return $this->config('api_url');
    }

    /**
     * Get payloads data
     *
     * @return array
     */
    public function payload()
    {
        if ($this->config('bulk')) {
            if (! isset($this->bulkPayloads)) {
                $this->bulk([
                    $this->config('payloads')
                ]);
            }
            
            $self = $this;

            return array_map(function ($data) use ($self) {
                return $self->mapPayload($data);
            }, $this->bulkPayloads);
        }

        return $this->mapPayload(
            $this->payloads
        );
    }

    /**
     * Get API request method
     *
     * @return string
     */
    public function requestMethod()
    {
        return $this->config('request_method');
    }

    /**
     * get API headers config
     *
     * @return array
     */
    public function headers()
    {
        return $this->config('headers');
    }

    /**
     * Get message key for payload
     *
     * @return string
     */
    public function messageKey()
    {
        return $this->config('keys.message');
    }

    /**
     * Get phone key for payload
     *
     * @return string
     */
    public function phoneKey()
    {
        return $this->config('keys.phone');
    }

    /**
     * Get API credentials
     *
     * @return array
     */
    public function credentials()
    {
        return $this->config('credentials');
    }

    /**
     * Merge credentials data to payload data
     *
     * @return array
     */
    public function credentialsToPayload($payload)
    {
        $credentials = $this->credentials();

        if (count($credentials)) {
            return array_merge($payload, $credentials);
        }

        return $payload;
    }

    /**
     * Set message text
     *
     * @param string $text
     * @return self
     */
    public function text($text)
    {
        $this->payloads['message'] = $text;
        return $this;
    }

    /**
     * Set phone number to send
     *
     * @param string $number
     * @return self
     */
    public function phone($number)
    {
        $this->payloads['phone'] = $number;
        return $this;
    }

    /**
     * Prepare bulk message send
     *
     * @param array $param
     * @return self
     */
    public function bulk($params)
    {
        $this->setOption('bulk', true);

        $this->bulkPayloads = [];
        foreach ($params as $key => $value) {
            array_push($this->bulkPayloads, array_merge($this->config('payloads'), $value));
        }

        return $this;
    }

    /**
     * Map / formats payload keys according to config
     *
     * @param array $payload
     * @return array
     */
    protected function mapPayload($payload)
    {
        if (! $this->check($payload)) {
            return null;
        }

        $formattedPayload = $this->credentialsToPayload($payload);

        foreach ($payload as $key => $value) {
            switch ($key) {
                case 'message':
                    $key = $this->messageKey();
                    unset($formattedPayload['message']);
                break;

                case 'phone':
                    $key = $this->phoneKey();
                    unset($formattedPayload['phone']);
                break;

                default:

                break;
            }

            $formattedPayload[$key] = $value;
        }

        return $formattedPayload;
    }

    /**
     * Check all requirements before sending
     *
     * @param array $payload
     * @return bool
     */
    protected function check($payload)
    {
        if (!isset($payload['phone'])) {
            throw new SmsException('missing message text');

            return false;
        }

        if (!isset($payload['message'])) {
            throw new SmsException('missing phone number');

            return false;
        }

        return true;
    }

    /**
     * Send the message
     *
     * @return object|null
     */
    public function send()
    {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->apiUrl(),
            // You can set any number of default request options.
            'timeout'  => 5.0,
            'headers' => $this->headers(),
        ]);

        try {
            switch ($this->requestMethod()) {
                case 'POST':
                    $response = $client->request('POST', '', [
                        $this->config('payload_type') => $this->payload(),
                    ]);
                break;

                case 'GET':
                    $response = $client->request('GET', '', [
                        'query' => $this->payload()
                    ]);
                break;

                default:
                break;
            }
        } catch (RequestException $e) {
            throw new SmsException(json_encode([
                'request' => Psr7\str($e->getRequest()),
                'response' => $e->hasResponse() ? Psr7\str($e->getResponse()) : null,
            ]));

            return null;
        }

        if (isset($response) && $response->getStatusCode() != 200) {
            return null;
        }

        return json_decode($response->getBody()->getContents());
    }
}
