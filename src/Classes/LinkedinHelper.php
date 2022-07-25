<?php

namespace Neurohub\Apilinkedin\Classes;

use GuzzleHttp\Client;

class LinkedinHelper
{
    public static function accessToken(string $code): string
    {
        $client = new Client(['base_uri' => 'https://www.linkedin.com']);
        $response = $client->request('POST', '/oauth/v2/accessToken', [
            'form_params' => [
                "grant_type" => "authorization_code",
                "code" => $code,
                "redirect_uri" =>  config('linkedinsharecontent.redirect_uri'),
                "client_id" => config('linkedinsharecontent.client_id'),
                "client_secret" => config('linkedinsharecontent.client_secret'),
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['access_token'];
    }

    public static function profileId(string $access_token): array
    {
        try {
            $client = new Client();
            $response = $client->request('GET', 'https://api.linkedin.com/v2/me', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token,
                    'Connection'    => 'Keep-Alive',
                ],
            ]);
            $object = json_decode($response->getBody()->getContents(), true);
            return ['id' => $object['id'], 'name' => $object['localizedFirstName'] . ' ' . $object['localizedLastName']];
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
