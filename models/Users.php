<?php

require_once('../includes/Database.php');
require_once('../includes/Bcrypt.php');

class User
{
    private $table = 'users';

    //user properties
    public $user_id;
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

        $sql = "INSERT INTO $this->table (firstname, lastname, email, password) VALUES (
            '" .$database->escape_value($this->firstname). "'
            '" .$database->escape_value($this->lastname). "'
            '" .$database->escape_value($this->email). "'
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
                return $this->get_user_details();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //get user details
    public function get_user_details() {
        global $database;

        $this->email = trim(htmlspecialchars(strip_tags($this->email)));

        $sql = "SELECT user_id, firstname, lastname, email, password FROM " .$this->table. " WHERE email = '" .$database->escape_value($this->email). "'";

        $result = $database->query($sql);
        $user_info = $database->fetch_row($result);

        return $user_info;
    }
}

$user = new User();
