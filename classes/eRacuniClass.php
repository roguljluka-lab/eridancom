<?php

class eRacuniClass
{
    public function send_to_eracuni($data)
    {
        
        // API endpoint URL
        $url = 'https://e-racuni.com/WebServicesHR/API';

        // Convert data to JSON format
        $jsonData = json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            )
        ));

        // Execute cURL request
        $response = curl_exec($curl);

        // Check for errors
        if(curl_errno($curl)){
            echo 'Curl error: ' . curl_error($curl);
        }

        // Close cURL session
        curl_close($curl);

        //file_put_contents('debug.log', print_r($response, true) . "\n", FILE_APPEND);

        return json_decode($response);
        
    }
}