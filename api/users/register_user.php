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

        //validation of parametres
        if ($user->validate_params($data->firstname)) {
            $user->firstname = $data->firstname;
        } else {
            die(header('HTTP/1.1 402 First Name is required'));
        }

        if ($user->validate_params($data->lastname)) {
            $user->lastname = $data->lastname;
        } else {
            die(header('HTTP/1.1 402 Last Name is required'));
        }

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

        //check unique email
        if ($user->check_unique_email()) {

                //create user
            if ($user->create_user()) {
                echo json_encode(array('success'=>1, 'message'=>'User is registered!'));
            } else {
                echo json_encode(array('success'=>0, 'message'=>'User registration failed!'));
            }
        } else {
            echo json_encode(array('success'=>0, 'message'=>'This email is already taken.'));
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
