<?php

//headers
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header('Access-Control-Allow-Method: POST');
header('Access-Control-Allow-Headers: Origin, Content-type, Auth_Key, Accept');

include_once '../../models/Users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
        /*
        *
        *   Auth key is'nt required for registration
        *
        */
        // $headers = apache_request_headers();
        // $auth_key = $headers['Auth_Key'];

        // $verified = $user->verify_auth_key();
        // if ($verified == true) {
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        if ($user->validate_params($data->email)) {
            $user->email = $data->email;
        } else {
            die(header('HTTP/1.1 402 Email is required'));
        }

        if ($user->validate_params($data->password)) {
            $user->password = $data->password;
        } else {
            die(header('HTTP/1.1 402 Password is required'));
        }

        if ($info = $user->check_user_credentials()) {
            echo json_encode(array('success'=> 1, 'user'=> $info));
        } else {
            echo json_encode(array('success'=> 1, 'message'=> 'invalid email or password...'));
        }
        // } else {
        //     die(header('HTTP/1.1 401 Unauthorized'));
        // }
    } else {
        die(header('HTTP/1.1 415 Content Type Invalid'));
    }
} else {
    die(header('HTTP/1.1 405 Request ethod Not Valid'));
}
