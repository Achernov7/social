<?php

namespace App\Components;
use GuzzleHttp\Client;

class ImportCitiesOrTowsOfRussiaClient {

    public $client; 

    public function __construct()
    {
        $this->client = new Client([           
            'base_uri' => 'https://api.hh.ru/areas/',     
            'timeout'  => 2.0,    
            'verify' => false,
        ]);
    }

}