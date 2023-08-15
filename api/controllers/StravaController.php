<?php
namespace Api\Controllers;
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('__ROOT__', dirname(dirname(dirname(__FILE__))));
require_once(__ROOT__.'/api/services/DB.php');
use Services\DB;
class StravaController
{
    public $conn = null;

    public function __construct()
    {
        // Create connection
        $this->conn = (new DB())->database();
    }

    public function getActivitiesPeriod()
    {
        try
        {
            $this->getHeaders();

            $beforeString = $_GET['before'] ?? "August 1 2023";
            $afterString = $_GET['after'] ?? "June 30 2023";

            $auth_url = 'https://www.strava.com/oauth/token';

            $data = array('client_id' => '104520', 'client_secret' => '6426f3550c9f43dc018e17c9d77ea435e8ff39bd',
        'refresh_token' => 'ced5a05bc1dc04c512818f940bb4270738bc4101', 'grant_type' => 'refresh_token', 'f' => 'json');

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

            $access_token = $auth_data->access_token;

            //$time = time();
            //$lastMonth = $time - (30*60*60*24);
            $before = strtotime($beforeString);
            $after = strtotime($afterString);

            $per_page = 90; // enough for 3 activities a day for a month
            
            $activities_url = "https://www.strava.com/api/v3/athlete/activities?access_token=$access_token&after=$after&before=$before&per_page=$per_page";

            // Read JSON file
            $json_data = file_get_contents($activities_url);

            // Decode JSON data into PHP array
            $response_data = json_decode($json_data, true);

            $this->saveActivitiesToDatabase($response_data);
            exit;
            //echo json_encode($response_data, JSON_PRETTY_PRINT);
        }
        catch(\Exception $e)
        {
            var_dump($e->getMessage());
            exit;
        }
    }

    public function saveActivitiesToDatabase($activities = null)
    {
        foreach($activities as $activity)
        {
            $sql = "INSERT INTO activities(`name`, `distance`, `sport_type`, `elapsed_time`)
                VALUES (
                    '".$activity['name']."',
                    '".$activity['distance']."',
                    '".$activity['sport_type']."',
                    '".$activity['elapsed_time']."'
                )";
            if(mysqli_query($this->conn, $sql))
            {
                echo "New record created successfully";
            }
            else
            {
                echo "Error: ". $sql ."<br/>". mysqli_error($this->conn);
            }
        }

        mysqli_close($this->conn);
    }

    public function getActivities()
    {
        try
        {
            $this->getHeaders();

            $auth_url = 'https://www.strava.com/oauth/token';

            $data = array('client_id' => '104520', 'client_secret' => '6426f3550c9f43dc018e17c9d77ea435e8ff39bd',
        'refresh_token' => 'ced5a05bc1dc04c512818f940bb4270738bc4101', 'grant_type' => 'refresh_token', 'f' => 'json');

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

            $access_token = $auth_data->access_token;

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
        $this->getHeaders();

        $auth_url = 'https://www.strava.com/oauth/token';

        $data = array('client_id' => '104520', 'client_secret' => '6426f3550c9f43dc018e17c9d77ea435e8ff39bd',
    'refresh_token' => 'ced5a05bc1dc04c512818f940bb4270738bc4101', 'grant_type' => 'refresh_token', 'f' => 'json');

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
