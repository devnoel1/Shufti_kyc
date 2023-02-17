<?php

namespace App\Helpers;


class Kyc
{
    private static $url = 'https://api.shuftipro.com/';

    //Your Shufti Pro account Client ID
    private static $client_id;
    //Your Shufti Pro account Secret Key
    private static $secret_key;

    function __construct()
    {
        self::$client_id = env('SHUFTI_CLIENT_ID');
        self::$secret_key = env('SHUFTI_SECRET_KEY ');
    }

   public static function verifyDocument()
    {
        $verification_request = [
            'reference'    => 'ref-' . rand(4, 444) . rand(4, 444),
            'country'      => 'GB',
            'language'     => 'EN',
            'email'        => 'example@email.com',
            'callback_url' =>  'https://yourdomain.com/profile/notifyCallback',
            'verification_mode' => 'any',
            'ttl'         => 60,
        ];

        //Use this key if you want to perform document verification with OCR
        $verification_request['document'] = [
            'proof' => '',
            'additional_proof' => '',
            'name' => '',
            'dob'             => '',
            'age'             => '',
            'document_number' => '',
            'expiry_date'     => '',
            'issue_date'      => '',
            'allow_offline'      => '1',
            'allow_online'     => '1',
            'supported_types' => ['id_card', 'passport'],
            "gender"          =>  ""
        ];

        $post_data = json_encode($verification_request);
        //Calling Shufti Pro request API using curl
        $response = self::send_request(self::$url, $post_data);
        //Get Shufti Pro API Response
        $response_data    = $response['body'];

        $decoded_response = json_decode($response_data, true);

        return $decoded_response['verification_url'];;
    }

    private static function send_request($url, $post_data)
    {
        $auth = self::$client_id . ":" . self::$secret_key;
        $headers = ['Content-Type: application/json'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERPWD, $auth); // remove this in case of Access Token
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); // remove this in case of Access Token
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $html_response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($html_response, 0, $header_size);
        $body = substr($html_response, $header_size);
        curl_close($ch);
        return ['headers' => $headers, 'body' => $body];
    }
}
