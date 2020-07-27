<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . '..') . $ds;
require_once("{$base_dir}includes{$ds}Database.php");

class Article
{
    private $table = 'articles';

    //article properties
    public $article_id;
    public $user_id;
    public $category_id;
    public $article_title;
    public $article_body;
    public $article_created_on;
    public $status;

    public function __construct()
    {
    }

    //validating user params
    public function validate_article_params($value)
    {
        if (!empty($value)) {
            return true;
        } else {
            return false;
        }
    }

    //create article
    public function create_article()
    {
        global $database;

        $this->user_id = filter_var($this->user_id, FILTER_VALIDATE_INT);
        $this->category_id = filter_var($this->category_id, FILTER_VALIDATE_INT);
        $this->article_title = trim(htmlspecialchars(strip_tags($this->article_title)));
        $this->article_body = trim(htmlspecialchars(strip_tags($this->article_body)));
        $this->article_created_on = date('Y-m-d');

        $sql = "INSERT INTO $this->table (user_id, category_id, article_title, article_body, article_created_on)
                VALUES ('".$database->escape_value($this->user_id)."',
                '".$database->escape_value($this->category_id)."',
                '".$database->escape_value($this->article_title)."',
                '".$database->escape_value($this->article_body)."',
                '".$database->escape_value($this->article_created_on)."')";

        $article_saved = $database->query($sql);

        if ($article_saved) {
            return true;
        } else {
            return false;
        }
    }

    //get user article
    public function get_user_article()
    {
        global $database;

        $this->article_id = filter_var($this->article_id, FILTER_VALIDATE_INT);
        $this->user_id = filter_var($this->user_id, FILTER_VALIDATE_INT);

        $sql = "SELECT articles.article_id, articles.user_id, articles.category_id, articles.article_title, 
                articles.article_body, 
                categories.category_title FROM " .$this->table. " 
                JOIN categories on categories.category_id = articles.category_id 
                JOIN users on users.user_id = articles.user_id
                WHERE articles.article_id = '" .$database->escape_value($this->article_id). "' && 
                articles.user_id = '" .$database->escape_value($this->user_id). "'";

        $result = $database->query($sql);
        $article_info = $database->fetch_row($result);

        if (!empty($article_info)) {
            return $article_info;
        } else {
            false;
        }
    }

    //update article
    public function update_article()
    {
        global $database;

        $this->article_id = filter_var($this->article_id, FILTER_VALIDATE_INT);
        $this->user_id = filter_var($this->user_id, FILTER_VALIDATE_INT);
        $this->category_id = filter_var($this->category_id, FILTER_VALIDATE_INT);

        $sql = "UPDATE ". $this->table ." SET
                category_id = '".$database->escape_value($this->category_id)."',
                article_title = '".$database->escape_value($this->article_title)."',
                article_body = '".$database->escape_value($this->article_body)."'
                WHERE article_id = '".$database->escape_value($this->article_id)."' &&
                user_id = '".$database->escape_value($this->user_id)."'";

        $article_updated = $database->query($sql);

        if ($article_updated) {
            return true;
        } else {
            return false;
        }
    }

    //delete article
    public function delete_article()
    {
        global $database;

        $this->article_id = filter_var($this->article_id, FILTER_VALIDATE_INT);
        $this->user_id = filter_var($this->user_id, FILTER_VALIDATE_INT);

        $sql = "DELETE FROM ". $this->table ." 
                WHERE article_id = '".$database->escape_value($this->article_id)."' &&
                user_id = '".$database->escape_value($this->user_id)."'";

        $article_deleted = $database->query($sql);

        if ($article_deleted) {
            return true;
        } else {
            return false;
        }
    }

    //latest articles
    public function get_latest_article() {
        global $database;

        $sql = "SELECT articles.article_id, articles.user_id, articles.category_id, articles.article_title,
                articles.article_body,
                categories.category_title, users.user_id, users.firstname, users.lastname
                FROM ". $this->table ."
                JOIN categories on articles.category_id = categories.category_id
                JOIN users on users.user_id = articles.user_id order by articles.article_id desc limit 5";

        $result = $database->http_build_query($sql);
        $article_info = $database->fetch_array($result);

        return $article_info;

    }
}

$articles = new Article();
