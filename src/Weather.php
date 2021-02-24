<?php


namespace Hanc\Weather;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Hanc\Weather\Exception\Exception;
use Hanc\Weather\Exception\HttpException;
use Hanc\Weather\Exception\InvalidArgumentException;

class Weather
{
    protected $key;
    protected $guzzleOption = [];


    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getHttpClient(): Client
    {
        return new Client($this->guzzleOption);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOption = $options;
    }

    public function getWeather($city, string $type = 'base', string $format = 'json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        if (!\in_array(\strtolower($format), ['xml', 'json'])) {
            throw new InvalidArgumentException('Invalid response format: ' . $format);
        }

        if (!\in_array(\strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): ' . $format);
        }

        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => $format,
            'extensions' => $type
        ]);

        try {

            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();

            return $response;

            return 'json' === $format ? \json_encode($response, true) : $response;

        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        } catch (GuzzleException $e) {
        }
    }
}