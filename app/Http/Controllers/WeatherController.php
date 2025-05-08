<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    protected $weatherService;
    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }
    /**
     * Get current weather for a city.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function current(Request $request): JsonResponse
    {
        $city = $request->query('city', 'London');
        try {
            $data = $this->weatherService->getCurrentWeather($city);
            return response()->json([
                'city' => $data['name'],
                'temperature' => $data['main']['temp'],
                'description' => $data['weather'][0]['description'],
                'icon' => $data['weather'][0]['icon'],
                'humidity' => $data['main']['humidity'],
                'wind_speed' => $data['wind']['speed'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'City not found or API error'], 404);
        }
    }
    /**
     * Get 5-day forecast for a city.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function forecast(Request $request): JsonResponse
    {
        $city = $request->query('city', 'London');
        try {
            $data = $this->weatherService->getForecast($city);
            $forecast = array_map(function ($item) {
                return [
                    'date' => $item['dt_txt'],
                    'temperature' => $item['main']['temp'],
                    'description' => $item['weather'][0]['description'],
                    'icon' => $item['weather'][0]['icon'],
                ];
            }, array_slice($data['list'], 0, 5));
            return response()->json($forecast);
        } catch (\Exception $e) {
            return response()->json(['error' => 'City not found or API error'], 404);
        }
    }
}
