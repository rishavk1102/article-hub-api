<?php

//headers
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header('Access-Control-Allow-Method: DELETE');
header('Access-Control-Allow-Headers: Origin, Content-type, Auth_Key, Accept');

include_once '../../models/Users.php';
include_once '../../models/Articles.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
        $headers = apache_request_headers();
        $auth_key = $headers['Auth_Key'];
        $user->auth_key = $auth_key;

        if ($user->verify_auth_key() == true) {
            $json = file_get_contents('php://input');
            $data = json_decode($json);

            if ($articles->validate_article_params($data->article_id)) {
                $articles->article_id = $data->article_id;
            } else {
                die(header('HTTP/1.1 402 article_id is required'));
            }

            if ($articles->validate_article_params($data->user_id)) {
                $articles->user_id = $data->user_id;
            } else {
                die(header('HTTP/1.1 402 user_id is required'));
            }

            //delete article
            if ($articles->delete_article() === true) {
                echo json_encode(array('success'=> 1, 'message'=> 'Article deleted!'));
            } else {
                echo json_encode(array('success'=> 0, 'message'=> 'AArticle deletion failed!'));
            }
        } else {
            die(header('HTTP/1.1 401 Unauthorized'));
        }
    } else {
        die(header('HTTP/1.1 415 Content Type Invalid'));
    }
} else {
    die(header('HTTP/1.1 405 Request ethod Not Valid'));
}
