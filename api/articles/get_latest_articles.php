<?php

//headers
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header('Access-Control-Allow-Method: GET');
header('Access-Control-Allow-Headers: Origin, Content-type, Auth_Key, Accept');

include_once '../../models/Users.php';
include_once '../../models/Articles.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
        $headers = apache_request_headers();
        $auth_key = $headers['Auth_Key'];
        $user->auth_key = $auth_key;

        if ($user->verify_auth_key() == true) {
            
            //get latest articles
            $latest = $articles->get_latest_article();
            echo json_encode(array('success'=> 1, 'articles'=> $latest));
        } else {
            die(header('HTTP/1.1 401 Unauthorized'));
        }
    } else {
        die(header('HTTP/1.1 415 Content Type Invalid'));
    }
} else {
    die(header('HTTP/1.1 405 Request ethod Not Valid'));
}
