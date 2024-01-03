<?php

    namespace Post {
        require_once("dbtable.php");
        require_once("dbdriver.php");
        class DBPost implements \DBTable{
            private $post_id;
            private $event_id;
            private $username;
            private $image;
            private $description;
            private $posted;
            private $likes;
            private $event_post;

            public function __construct($post_id = null, $event_id = null, $username = null, $image = null, 
                    $description = null, $posted = null, $likes = null, $event_post = null) {
                $this->post_id = $post_id;
                $this->event_id = $event_id;
                $this->username = $username;
                $this->image = $image;
                $this->description = $description;
                $this->posted = $posted;
                $this->likes = $likes;
                $this->event_post = $event_post;
            }

            public function jsonSerialize() {
                return [
                    "post_id" => $this->post_id,
                    "event_id" => $this->event_id,
                    "username" => $this->username,
                    "image" => $this->image,
                    "description" => $this->description,
                    "posted" => $this->posted,
                    "likes" => $this->likes,
                    "event_post" => $this->event_post
                ];
            }

            public function db_serialize($driver) {
                $sql = "INSERT INTO post (post_id, event_id, username, image, description, posted, likes, event_post) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                try {
                    $driver->query($sql, $this->post_id, $this->event_id, $this->username, $this->image, 
                            $this->description, $this->posted, $this->likes, $this->event_post);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        class DBComment implements \DBTable {
            private $comment_id;
            private $post_id;
            private $content;
            private $likes;

            public function __construct($comment_id = null, $post_id = null, $content = null, $likes = null) {
                $this->comment_id = $comment_id;
                $this->post_id = $post_id;
                $this->content = $content;
                $this->likes = $likes;
            }

            public function jsonSerialize() {
                return [
                    "comment_id" => $this->comment_id,
                    "post_id" => $this->post_id,
                    "content" => $this->content,
                    "likes" => $this->likes
                ];
            }

            public function db_serialize($driver) {
                $sql = "INSERT INTO post_comment (comment_id, post_id, content, likes) VALUES (?, ?, ?, ?)";
                try {
                    $driver->query($sql, $this->comment_id, $this->post_id, $this->content, $this->likes);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        class PostUtility {
            public static function from_db_with_event_id(\DBDriver $driver, $event_id) {
                $sql = "SELECT * FROM post WHERE event_id = ?";
                try {
                    $result = $driver->query($sql, $event_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                if ($result->num_rows == 0) {
                    return null;
                }
                $row = $result->fetch_assoc();
                return new DBPost($row["post_id"], $row["event_id"], $row["username"], $row["image"], 
                        $row["description"], $row["posted"], $row["likes"], $row["event_post"]);
            }

            public static function from_db_with_username(\DBDriver $driver, $username) {
                $sql = "SELECT * FROM post WHERE username = ?";
                try {
                    $result = $driver->query($sql, $username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $posts = array();
                if ($result->num_rows > 0) {
                    for($i = 0; $i < $result->num_rows; $i++){
                        $row = $result->fetch_array();
                        $post = new DBPost($row["post_id"], $row["event_id"], $row["username"], $row["image"], 
                                $row["description"], $row["posted"], $row["likes"], $row["event_post"]);
                        array_push($posts, $post);
                    }
                }
                return $posts;
            }

            public static function recent_posts_followed(\DBDriver $driver, $username, $max_posts) {
                $sql = "SELECT *
                        FROM post
                        WHERE username IN (
                            SELECT followed
                            FROM follow
                            WHERE follower = ?)
                        ORDER BY posted DESC
                        LIMIT ?";
                try {
                    $result = $driver->query($sql, $username, $max_posts);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $posts = array();
                if ($result->num_rows > 0) {
                    for($i = 0; $i < $result->num_rows; $i++){
                        $row = $result->fetch_array();
                        $post = new DBPost($row["post_id"], $row["event_id"], $row["username"], $row["image"], 
                                $row["description"], $row["posted"], $row["likes"], $row["event_post"]);
                        array_push($posts, $post);
                    }
                }
                return $posts;
            }

            public static function comments_with_post(\DBDriver $driver, $post_id) {
                $sql = "SELECT * FROM comment WHERE post_id = ?";
                try {
                    $result = $driver->query($sql, $post_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $comments = array();
                if ($result->num_rows > 0) {
                    for($i = 0; $i < $result->num_rows; $i++){
                        $row = $result->fetch_array();
                        $comment = new DBComment($row["comment_id"], $row["post_id"], $row["content"], $row["likes"]);
                        array_push($comments, $comment);
                    }
                }
                return $comments;
            }
        }
    }
?>