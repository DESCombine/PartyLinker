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
            private $liked;

            public function __construct($post_id = null, $event_id = null, $username = null, $image = null, 
                    $description = null, $posted = null, $likes = null, $event_post = null, $liked = null) {
                $this->post_id = $post_id;
                $this->event_id = $event_id;
                $this->username = $username;
                $this->image = $image;
                $this->description = $description;
                $this->posted = $posted;
                $this->likes = $likes;
                $this->event_post = $event_post;
                $this->liked = $liked;
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
                    "event_post" => $this->event_post,
                    "liked" => $this->liked
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
            private $username;
            private $profile_photo;
            private $content;
            private $likes;
            private $liked;

            public function __construct($comment_id = null, $post_id = null, $username = null, 
                    $profile_photo = null, $content = null, $likes = null, $liked = null) {
                $this->comment_id = $comment_id;
                $this->post_id = $post_id;
                $this->username = $username;
                $this->profile_photo = $profile_photo;
                $this->content = $content;
                $this->likes = $likes;
                $this->liked = $liked;
            }

            public function jsonSerialize() {
                return [
                    "comment_id" => $this->comment_id,
                    "post_id" => $this->post_id,
                    "username" => $this->username,
                    "profile_photo" => $this->profile_photo,
                    "content" => $this->content,
                    "likes" => $this->likes,
                    "liked" => $this->liked
                ];
            }

            public function db_serialize($driver) {
                $sql = "INSERT INTO post_comment (comment_id, post_id, username, content, likes) VALUES (?, ?, ?, ?, ?)";
                try {
                    $driver->query($sql, $this->comment_id, $this->post_id, $this->username, $this->content, $this->likes);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        class DBPostLike implements \DBTable {
            private $post_id;
            private $username;

            public function __construct($post_id = null, $username = null) {
                $this->post_id = $post_id;
                $this->username = $username;
            }

            public function jsonSerialize() {
                return [
                    "post_id" => $this->post_id,
                    "username" => $this->username
                ];
            }

            public function db_serialize($driver) {
                $sql = "INSERT INTO post_like (post_id, username) VALUES (?, ?)";
                try {
                    $driver->query($sql, $this->post_id, $this->username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }

            public function db_delete($driver) {
                $sql = "DELETE FROM post_like WHERE post_id = ? AND username = ?";
                try {
                    $driver->query($sql, $this->post_id, $this->username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        class DBCommentLike implements \DBTable {
            private $comment_id;
            private $username;

            public function __construct($comment_id = null, $username = null) {
                $this->comment_id = $comment_id;
                $this->username = $username;
            }

            public function jsonSerialize() {
                return [
                    "comment_id" => $this->comment_id,
                    "username" => $this->username
                ];
            }

            public function db_serialize($driver) {
                $sql = "INSERT INTO comment_like (comment_id, username) VALUES (?, ?)";
                try {
                    $driver->query($sql, $this->comment_id, $this->username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }

            public function db_delete($driver) {
                $sql = "DELETE FROM comment_like WHERE comment_id = ? AND username = ?";
                try {
                    $driver->query($sql, $this->comment_id, $this->username);
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
                            FROM relationship
                            WHERE follows = ?)
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
                        $sql = "SELECT * FROM post_like WHERE post_id = ? AND username = ?";
                        try {
                            $liked = $driver->query($sql, $row["post_id"], $username)->num_rows > 0;
                        } catch (\Exception $e) {
                            throw new \Exception("Error while querying the database: " . $e->getMessage());
                        }
                        $post = new DBPost($row["post_id"], $row["event_id"], $row["username"], $row["image"], 
                                $row["description"], $row["posted"], $row["likes"], $row["event_post"], $liked);
                        array_push($posts, $post);
                    }
                }
                return $posts;
            }

            public static function comments_with_post(\DBDriver $driver, $post_id, $username) {
                $sql = "SELECT C.*, U.profile_photo
                        FROM comment C, user U
                        WHERE C.post_id = ?
                        AND C.username = U.username";
                try {
                    $result = $driver->query($sql, $post_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $comments = array();
                if ($result->num_rows > 0) {
                    for($i = 0; $i < $result->num_rows; $i++){
                        $row = $result->fetch_array();
                        $sql = "SELECT * FROM comment_like WHERE comment_id = ? AND username = ?";
                        try {
                            $liked = $driver->query($sql, $row["comment_id"], $username)->num_rows > 0;
                        } catch (\Exception $e) {
                            throw new \Exception("Error while querying the database: " . $e->getMessage());
                        }
                        $comment = new DBComment($row["comment_id"], $row["post_id"], $row["username"], 
                                $row["profile_photo"], $row["content"], $row["likes"], $liked);
                        array_push($comments, $comment);
                    }
                }
                return $comments;
            }

            public static function delete_comment(\DBDriver $driver, $comment_id) {
                $sql = "DELETE FROM comment WHERE comment_id = ?";
                try {
                    $driver->query($sql, $comment_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }

            public static function insert_like(\DBDriver $driver, $like_id, $username, $type) {
                $sql = "UPDATE";
                try {
                    switch ($type) {
                        case "post":
                            $like = new DBPostLike($like_id, $username);
                            $like->db_serialize($driver);
                            $sql = $sql . " post SET likes = likes + 1 WHERE post_id = ?";
                            break;
                        case "comment":
                            $like = new DBCommentLike($like_id, $username);
                            $like->db_serialize($driver);
                            $sql = $sql . " comment SET likes = likes + 1 WHERE comment_id = ?";
                            break;
                        default:
                            throw new \Exception("Invalid type: " . $type);
                    }
                    $driver->query($sql, $like_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }

            public static function delete_like(\DBDriver $driver, $like_id, $username, $type) {
                $sql = "UPDATE";
                try {
                    switch ($type) {
                        case "post":
                            $like = new DBPostLike($like_id, $username);
                            $like->db_delete($driver);
                            $sql = $sql . " post SET likes = likes - 1 WHERE post_id = ?";
                            break;
                        case "comment":
                            $like = new DBCommentLike($like_id, $username);
                            $like->db_delete($driver);
                            $sql = $sql . " comment SET likes = likes - 1 WHERE comment_id = ?";
                            break;
                        default:
                            throw new \Exception("Invalid type: " . $type);
                    }
                    $driver->query($sql, $like_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }
    }
?>