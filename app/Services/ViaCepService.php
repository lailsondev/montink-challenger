<?php

namespace App\Services;

class ViaCepService
{
    const API_URL = "https://viacep.com.br/ws/";

    public function getAddressByCep($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        if (strlen($cep) !== 8) {
            return null;
        }

        $url = self::API_URL . $cep . "/json/";
        $response = file_get_contents($url);

        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);

        if (isset($data['erro'])) {
            return null;
        }

        return $data;
    }
}