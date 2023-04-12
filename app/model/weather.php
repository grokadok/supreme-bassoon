<?php

namespace bopdev;

class Weather
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = getenv('OPENWEATHERMAP_API_KEY');
    }

    /**
     * Returns the weather for a city.
     * @param string $city City name.
     */
    public function getWeather(string $city)
    {
        $url = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=$this->apiKey&units=metric";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return $err;
        } else {
            return json_decode($response, true);
        }
    }
}
