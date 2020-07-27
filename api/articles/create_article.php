<?php

require "/opt/lampp/htdocs/api_code/vendor/autoload.php";
use \Firebase\JWT\JWT;

//headers
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header('Access-Control-Allow-Method: POST');
header('Access-Control-Allow-Headers: Origin, Content-type, Auth_Key, Authorization, X-Requested-With, Accept');

include_once '../../models/Users.php';
include_once '../../models/Articles.php';

$secret_key = "YOUR_SECRET_KEY";
$jwt = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'];
    $arr = explode(" ", $authHeader);
    $jwt = $arr[1];

    if ($jwt) {
        try {
            $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

            if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
                $auth_key = $headers['Auth_Key'];
                $user->auth_key = $auth_key;
        
                if ($user->verify_auth_key() == true) {
                    $json = file_get_contents('php://input');
                    $data = json_decode($json);
        
                    if ($articles->validate_article_params($data->user_id)) {
                        $articles->user_id = $data->user_id;
                    } else {
                        die(header('HTTP/1.1 402 user_id is required'));
                    }
        
                    if ($articles->validate_article_params($data->category_id)) {
                        $articles->category_id = $data->category_id;
                    } else {
                        die(header('HTTP/1.1 402 category_id is required'));
                    }
        
                    if ($articles->validate_article_params($data->article_title)) {
                        $articles->article_title = $data->article_title;
                    } else {
                        die(header('HTTP/1.1 402 article_title is required'));
                    }
        
                    if ($articles->validate_article_params($data->article_body)) {
                        $articles->article_body = $data->article_body;
                    } else {
                        die(header('HTTP/1.1 402 article_body is required'));
                    }
        
                    //create article
                    if ($articles->create_article() === true) {
                        echo json_encode(array('success'=> 1, 'message'=> 'Article added!'));
                    } else {
                        echo json_encode(array('success'=> 0, 'message'=> 'AArticle creation failed!'));
                    }
                } else {
                    die(header('HTTP/1.1 401 Unauthorized'));
                }
            } else {
                die(header('HTTP/1.1 415 Content Type Invalid'));
            }
        } catch (Exception $e) {
            http_response_code(401);

            echo json_encode(array(
                "message" => "Access denied.",
                "error" => $e->getMessage()
            ));
        }
    }
} else {
    die(header('HTTP/1.1 405 Request ethod Not Valid'));
}
