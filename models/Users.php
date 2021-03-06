<?php

require "/opt/lampp/htdocs/api_code/vendor/autoload.php";
use \Firebase\JWT\JWT;

require_once('/opt/lampp/htdocs/api_code/includes/Database.php');
require_once('/opt/lampp/htdocs/api_code/includes/Bcrypt.php');

class User
{
    private $table = 'users';

    //user properties
    public $user_id;
    public $auth_key;
    public $firstname;
    public $lastname;
    public $email;
    public $password;
    public $status;

    public function __construct()
    {
    }

    //validating user params
    public function validate_params($value)
    {
        if (!empty($value)) {
            return true;
        } else {
            return false;
        }
    }

    //check unique email
    public function check_unique_email()
    {
        global $database;

        $this->email = trim(htmlspecialchars(strip_tags($this->email)));

        $sql = "SELECT user_id FROM " .$this->table. " WHERE email = '" .$database->escape_value($this->email). "'";
        $result = $database->query($sql);
        $user_info = $database->fetch_row($result);

        if (empty($user_info)) {
            return true;
        } else {
            return false;
        }
    }

    //create user
    public function create_user()
    {
        global $database;

        $this->firstname = trim(htmlspecialchars(strip_tags($this->firstname)));
        $this->lastname = trim(htmlspecialchars(strip_tags($this->lastname)));
        $this->email = trim(htmlspecialchars(strip_tags($this->email)));
        $this->password = trim(htmlspecialchars(strip_tags($this->password)));

        $hashed_password = Bcrypt::hashPassword($this->password);

        $this->auth_key = md5(rand());

        $sql = "INSERT INTO $this->table (auth_key, firstname, lastname, email, password) VALUES (
            '" .$database->escape_value($this->auth_key). "',
            '" .$database->escape_value($this->firstname). "',
            '" .$database->escape_value($this->lastname). "',
            '" .$database->escape_value($this->email). "',
            '" .$database->escape_value($hashed_password). "'
        )";

        $user_saved = $database->query($sql);

        if ($user_saved) {
            return true;
        } else {
            return false;
        }
    }

    //check user credentials
    public function check_user_credentials()
    {
        global $database;

        $this->email = trim(htmlspecialchars(strip_tags($this->email)));

        $sql = "SELECT user_id, email, password FROM " .$this->table. " WHERE email = '" .$database->escape_value($this->email). "'";

        $result = $database->query($sql);
        $info = $database->fetch_row($result);

        if (!empty($info)) {
            //checking password
            $hashed_password = $info['password'];
            $password = trim(htmlspecialchars(strip_tags($this->password)));
            $match_password = Bcrypt::checkPassword($password, $hashed_password);

            if ($match_password) {
                $secret_key = "YOUR_SECRET_KEY";
                $issuer_claim = "THE_ISSUER"; // this can be the servername
                $audience_claim = "THE_AUDIENCE";
                $issuedat_claim = time(); // issued at
                $notbefore_claim = $issuedat_claim + 10; //not before in seconds
                $expire_claim = $issuedat_claim + 60; // expire time in seconds
                $token = array(
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => array(
                        "id" => $this->user_id,
                        "firstname" => $this->firstname,
                        "lastname" => $this->lastname,
                        "email" => $this->email
                ));
                
                $result = [];
                $jwt = JWT::encode($token, $secret_key);

                $result['jwt'] = $jwt;
                $result['info'] = $this->get_user_details();

                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //get user details
    public function get_user_details()
    {
        global $database;

        $this->email = trim(htmlspecialchars(strip_tags($this->email)));

        $sql = "SELECT user_id, auth_key, firstname, lastname, email, password FROM " .$this->table. " WHERE email = '" .$database->escape_value($this->email). "'";

        $result = $database->query($sql);
        $user_info = $database->fetch_row($result);

        return $user_info;
    }

    //function to verify user authKey
    public function verify_auth_key()
    {
        $this->auth_key = trim(htmlspecialchars(strip_tags($this->auth_key)));

        global $database;

        $sql = "SELECT user_id, firstname, lastname, email, auth_key FROM ". $this->table ."
        WHERE auth_key = '".$database->escape_value($this->auth_key)."'";

        $result = $database->query($sql);
        $user_info = $database->fetch_row($result);

        if (empty($user_info)) {
            return false;
        } else {
            return true;
        }
    }
}

$user = new User();
