<?php

namespace App\Http\Controllers;

use App\Models\Weather;
use App\Http\Requests\StoreWeatherRequest;
use App\Http\Requests\UpdateWeatherRequest;
use Illuminate\Http\Request;

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
    public function show(Weather $weather)
    {
        return $weather;
    }

    /**
     * Update the specified resource in storage.
     */
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
