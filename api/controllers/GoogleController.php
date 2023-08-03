<?php
namespace Api\Controllers;
error_reporting(E_ALL);
ini_set('display_errors', '1');

include 'sharkweek.php'; // Lives outside repo / webroot

class GoogleController
{
    public $conn = null;

    public function __construct()
    {

    }

    public function getMapUrl()
    {
        global $gmaps_token;

        try
        {
            $this->getHeaders();
            $polyLine = $_GET['polyLine'] ?? '';

            $imgurl = "https://maps.googleapis.com/maps/api/staticmap?size=600x300&maptype=roadmap&path=enc:$polyLine&key=$gmaps_token";
            // Read JSON file
            //$img_data = file_get_contents($imgurl);

            //header('content-type: image/gif');
            //echo $img_data;
            echo json_encode($imgurl, JSON_PRETTY_PRINT);
            
            //echo $imgurl;
        }
        catch(\Exception $e)
        {
            var_dump($e->getMessage());
            exit;
        }
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