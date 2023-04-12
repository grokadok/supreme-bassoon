<?php

namespace bopdev;

class Maps
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = getenv('GOOGLE_MAPS_API_KEY');
    }

    /**
     * Returns autocomplete suggestions for a city input.
     * @param string $city City input.
     */
    public function autoCompleteCity(string $city)
    {
        $url = "https://maps.googleapis.com/maps/api/place/autocomplete/json?input=$city&types=(cities)&key=$this->apiKey";
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
