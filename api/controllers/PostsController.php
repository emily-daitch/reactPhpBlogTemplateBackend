<?php
namespace Api\Controllers;
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('__ROOT__', dirname(dirname(dirname(__FILE__))));
require_once(__ROOT__.'/api/services/DB.php');
use Services\DB;
class PostsController
{
    public $conn = null;

    public function __construct()
    {
        // Create connection
        $this->conn = (new DB())->database();
    }

    /**
     * Getting posts from third party api
     */
    public function getPosts()
    {
        try
        {
            // Getting data
            $url = "https://jsonplaceholder.typicode.com/posts";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_ENCODING, 0);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));            
        
            // Getting images
            $url = "https://jsonplaceholder.typicode.com/photos";

            $chImg = curl_init();
            curl_setopt($chImg, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($chImg, CURLOPT_HEADER, 0);
            curl_setopt($chImg, CURLOPT_ENCODING, 0);
            curl_setopt($chImg, CURLOPT_MAXREDIRS, 10);
            curl_setopt($chImg, CURLOPT_TIMEOUT, 30);
            curl_setopt($chImg, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($chImg, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($chImg, CURLOPT_URL, $url);
            curl_setopt($chImg, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($chImg, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));            
        
            $responseData = json_decode(curl_exec($ch), true);
            $responseImages = json_decode(curl_exec($chImg), true);
            $combinedArray = [];

            // Combining data
            foreach($responseData as $resData)
            {
                if(isset($responseImages[$resData['id']]))
                {
                    $resData['image'] = $responseImages[$resData['id']]["url"];
                }

                $combinedArray[] = $resData;
            }

            $this->savePostsToDatabase($combinedArray);
            exit;
        }
        catch(\Exception $e)
        {
            var_dump($e->getMessage());
            exit;
        }
    }

    public function savePostsToDatabase($posts = null)
    {
        foreach($posts as $post)
        {
            $sql = "INSERT INTO posts(`user_id`, `title`, `content`, `image`)
                VALUES (
                    '".$post['userId']."',
                    '".$post['title']."',
                    '".$post['body']."',
                    '".$post['image']."'
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

    public function getPostsFromDatabase()
    {
        try
        {
            $this->getHeaders();

            $perPage = $_GET['limit'] ?? 5;
            $pageNumber = $_GET['offset'] ?? 0;
            $postsArray = [];

            $sql = "SELECT * FROM posts";
            $totalPosts = mysqli_num_rows(mysqli_query($this->conn, $sql));

            $sql = "SELECT * FROM posts ORDER BY id LIMIT $perPage OFFSET $pageNumber";
            $response = mysqli_query($this->conn, $sql);

            if($response)
            {
                while($row = mysqli_fetch_assoc($response))
                {
                    $postsArray['posts'][] = $row;
                }
            }
            else
            {
                echo "Error ". $sql. "<br/>" . mysqli_error($this->conn);
            }

            $postsArray['count'] = $totalPosts;

            mysqli_close($this->conn);
            echo json_encode($postsArray, JSON_PRETTY_PRINT);
        }
        catch(\Exception $e)
        {
            var_dump($e->getMessage());
            exit;
        }
    }

    public function getSearchResults()
    {
        try
        {
            $this->getHeaders();

            $postsArray = [];
            $keyword = $_GET['keyword'] ?? null;

            if($keyword)
            {
                $sql = "SELECT id,title FROM posts WHERE title LIKE '%$keyword%' LIMIT 5";

                $response = mysqli_query($this->conn, $sql);

                if($response)
                {
                    while($row = mysqli_fetch_assoc($response))
                    {
                        $postsArray['posts'][] = $row;
                    }
                }
            }

            echo json_encode($postsArray, JSON_PRETTY_PRINT);

        }
        catch(\Exception $e)
        {
            echo "Exception";
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

    public function getCurrentTopic()
    {
        try
        {
            $this->getHeaders();
            $currentTopic = null;
            $id = $_GET['id'] ?? null;

            if($id)
            {
                $sql = "SELECT * FROM posts WHERE id='".$id."'";

                $response = mysqli_query($this->conn, $sql);

                if($response)
                {
                    while($row = mysqli_fetch_assoc($response))
                    {
                        $currentTopic = $row;
                    }
                }
            }

            echo json_encode($currentTopic, JSON_PRETTY_PRINT);
        }
        catch(\Exception $e)
        {
            var_dump($e->getMessage);
            exit;
        }
    }
}