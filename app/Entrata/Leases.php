<?php


namespace App\Entrata;


use GuzzleHttp\Client;

class Leases
{
    public static $domain;
    public static $username;
    public static $password;
    public static $property_id;
    public static $body;
    public static $headers;
    public static $url;
    protected static $requestId = 1;

    public static function getMitsLeases($unitNumber = null, $leaseStatusTypeIds = '4')
    {
        self::init();
        $method = [
            'name' => 'getMitsLeases',
            'version' => 'r1',
            'params' => [
                'propertyId' => self::$property_id,
                'includeLeaseHistory' => '0',
                'sendUnitSpaces' => '0',
                'includeOtherIncomeLeases' => '0',
                'residentFriendlyMode' => '0'
            ]
        ];
        if ($unitNumber) {
            $method['params']['unitNumber'] = $unitNumber;
        }

        if ($leaseStatusTypeIds) {
            $method['params']['leaseStatusTypeIds'] = $leaseStatusTypeIds;
        }
        $leases = json_decode(self::get($method), true);
        if (isset($leases['response']['result']['LeaseApplication']['LA_Lease'])) {
            return $leases['response']['result']['LeaseApplication']['LA_Lease'];
        } else {
            return [];
        }
    }

    public static function init()
    {
        self::$domain = config('entrata.domain') . '/api/v1/leases';
        self::$username = config('entrata.username');
        self::$password = config('entrata.password');
        self::$property_id = config('entrata.property.id');
    }

    public static function get($method)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::$domain,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => json_encode(self::getBody($method)),
            CURLOPT_HTTPHEADER => self::getHeaders()
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public static function getBody(array $method = [])
    {
        return array_merge([
            'auth' =>
                [
                    'type' => 'basic',
                ],
            'requestId' => self::$requestId++
        ], ['method' => $method]);
    }

    public static function getHeaders()
    {
        return [
            'Content-type: APPLICATION/JSON; CHARSET=UTF-8',
            'Authorization: Basic ' . base64_encode(self::$username . ":" . self::$password)
        ];
    }
}