<?php

namespace App\Http\Controllers;

use App\Models\Weather;
use App\Http\Requests\StoreWeatherRequest;
use App\Http\Requests\UpdateWeatherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Weather::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            "location" => "required",
            "temperature" => "required",
            "humidity" => "required",
            "timestamp" => "required",
        ]);

        $weather = Weather::create($fields);

        return [
            "weather" => $weather
        ];

    }

    /**
     * Display the specified resource.
     */
    public function show(String $city)
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Weather Api',
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q' => $city,
            'format' => 'json',
            'limit' => 1
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }

        $data = $response->json();

        if (empty($data)) {
            return response()->json(['error' => 'City not found'], 404);
        }

        $weather = Http::withHeaders([
            'User-Agent' => 'Weather Api',
        ])->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $data[0]['lat'],
            'longitude' => $data[0]['lon'],
            'current' => 'temperature_2m,relative_humidity_2m',
        ]);

        if ($weather->failed()) {
            return response()->json(['error' => 'Failed to fetch weather data'], 500);
        }

        $weatherData = $weather->json();

        if (empty($weatherData)) {
            return response()->json(['error' => 'Weather not found'], 404);
        }

        return [
            "location" => $city,
            "temperature" => $weatherData['current']['temperature_2m'] . "°C",
            "humidity" => $weatherData['current']['relative_humidity_2m'] . '%',
            "time" => $weatherData['current']['time'] . " UTC",
        ];
    }

    public function update(Request $request, Weather $weather)
    {
        $fields = $request->validate([
            "location" => "required",
            "temperature" => "required",
            "humidity" => "required",
            "timestamp" => "required",
        ]);

        $weather->update($fields);

        return [
            "weather" => $weather
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Weather $weather)
    {
        Weather::destroy($weather->id);

        return "Weather record deleted";
    }
}
