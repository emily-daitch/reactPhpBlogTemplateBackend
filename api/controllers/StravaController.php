<?php
namespace Api\Controllers;
error_reporting(E_ALL);
ini_set('display_errors', '1');

include '../sharkweek.php'; // Lives outside repo / webroot

class StravaController
{
    public $conn = null;

    public function __construct()
    {

    }

    public function getActivities()
    {
        global $strava_client_id, $strava_secret, $strava_refresh, $gmaps_token;

        try
        {
            $this->getHeaders();

            $auth_url = 'https://www.strava.com/oauth/token';

            $data = array('client_id' => '104520', 'client_secret' => $strava_secret,
            'refresh_token' => $strava_refresh, 'grant_type' => 'refresh_token', 'f' => 'json');

            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context  = stream_context_create($options);
            $result = file_get_contents($auth_url, false, $context);
            
            $auth_data = json_decode($result);
            //echo "auth_data";
            //var_dump($auth_data);
            
            $access_token = $auth_data->access_token;
            //var_dump($access_token);

            $time = time();
            $lastMonth = $time - (30*60*60*24);
            $per_page = 60; // enough for 2 activities a day
            
            $activities_url = "https://www.strava.com/api/v3/athlete/activities?access_token=$access_token&after=$lastMonth&per_page=$per_page";

            // Read JSON file
            $json_data = file_get_contents($activities_url);

            // Decode JSON data into PHP array
            $response_data = json_decode($json_data);

            echo json_encode($response_data, JSON_PRETTY_PRINT);
        }
        catch(\Exception $e)
        {
            var_dump($e->getMessage());
            exit;
        }
    }

    public function getMapActivity()
    {
        global $strava_client_id, $strava_secret, $strava_refresh, $gmaps_token;

        $this->getHeaders();

        $auth_url = 'https://www.strava.com/oauth/token';

        $data = array('client_id' => '104520', 'client_secret' => $strava_secret,
    'refresh_token' => $strava_refresh, 'grant_type' => 'refresh_token', 'f' => 'json');

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($auth_url, false, $context);
        
        $auth_data = json_decode($result);
        //echo "auth_data";
        //var_dump($auth_data);

        $access_token = $auth_data->access_token;

        $map_activity_id = 8839250323;
        
        $activity_url = "https://www.strava.com/api/v3/activities/$map_activity_id?access_token=$access_token";
        $json_data = file_get_contents($activity_url);
        $response_data = json_decode($json_data);
        echo json_encode($response_data, JSON_PRETTY_PRINT);
    }

    // Uncomment for local development to resolve CORS issue
    public function getHeaders()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: *");
        header('Access-Control-Max-Age: 86400');
        header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        }
    }
}