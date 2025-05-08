<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    protected $client;
    protected $apiKey;
    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.openweathermap.org/data/2.5/']);
        $this->apiKey = config('services.openweathermap.key');
    }
    /**
     * Fetch current weather data for a city.
     *
     * @param string $city
     * @return array
     */
    public function getCurrentWeather(string $city): array
    {
        $cacheKey = "weather_current_{$city}";
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($city) {
            $response = $this->client->get('weather', [
                'query' => [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                ],
            ]);
            return json_decode($response->getBody()->getContents(), true);
        });
    }
    /**
     * Fetch 5-day weather forecast for a city.
     *
     * @param string $city
     * @return array
     */
    public function getForecast(string $city): array
    {
        $cacheKey = "weather_forecast_{$city}";
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($city) {
            $response = $this->client->get('forecast', [
                'query' => [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                ],
            ]);
            return json_decode($response->getBody()->getContents(), true);
        });
    }
}
