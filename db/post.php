<?php

    namespace Post {
        require_once("dbtable.php");
        require_once("dbdriver.php");
        /**
         * Class that represents a post in the database.
         * @property int $post_id The post's id.
         * @property int $event_id The event's id.
         * @property string $username The username of the user that posted the image.
         * @property string $image The image's path.
         * @property string $description The post's description.
         * @property string $posted The date and time the post was made.
         * @property int $likes The number of likes the post has.
         * @property int $event_post Whether the post is an event post or not.
         * @property string $profile_photo The user's profile photo.
         * @property bool $liked Whether the post was liked by the user or not.
         */
        class DBPost implements \DBTable{
            private $post_id;
            private $event_id;
            private $username;
            private $image;
            private $description;
            private $posted;
            private $likes;
            private $event_post;
            private $profile_photo;
            private $liked;

            /**
             * Creates a new instance of DBPost.
             * @param int $post_id The post's id.
             * @param int $event_id The event's id.
             * @param string $username The username of the user that posted the image.
             * @param string $image The image's path.
             * @param string $description The post's description.
             * @param string $posted The date and time the post was made.
             * @param int $likes The number of likes the post has.
             * @param int $event_post Whether the post is an event post or not.
             * @param string $profile_photo The user's profile photo.
             * @param bool $liked Whether the post was liked by the user or not.
             */
            public function __construct($post_id = null, $event_id = null, $username = null, $image = null, 
                    $description = null, $posted = null, $likes = null, $event_post = null, $profile_photo = null, $liked = null) {
                $this->post_id = $post_id;
                $this->event_id = $event_id;
                $this->username = $username;
                $this->image = $image;
                $this->description = $description;
                $this->posted = $posted;
                $this->likes = $likes;
                $this->event_post = $event_post;
                $this->profile_photo = $profile_photo;
                $this->liked = $liked;
            }

            /**
             * Returns the object's properties as an associative array.
             * @return array The object's properties as an associative array.
             */
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
                    "profile_photo" => $this->profile_photo,
                    "liked" => $this->liked
                ];
            }

            /**
             * Inserts the object's properties into the database.
             * @param \DBDriver $driver The database driver.
             */
            public function db_serialize($driver) {
                $sql = "INSERT INTO post (event_id, username, image, description, event_post) 
                        VALUES (?, ?, ?, ?, ?)";
                try {
                    $driver->query($sql, $this->event_id, $this->username, $this->image, 
                            $this->description, $this->event_post);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }

            public function getUser() {
                return $this->username;
            }
        }

        /**
         * Class that represents a comment in the database.
         * @property int $comment_id The comment's id.
         * @property int $post_id The post's id.
         * @property string $username The username of the user that posted the comment.
         * @property string $profile_photo The user's profile photo.
         * @property string $content The comment's content.
         * @property int $likes The number of likes the comment has.
         * @property bool $liked Whether the comment was liked by the user or not.
         * @property bool $owner Whether the comment was made by the user or not.
         */
        class DBComment implements \DBTable {
            private $comment_id;
            private $post_id;
            private $username;
            private $profile_photo;
            private $content;
            private $likes;
            private $liked;
            private $owner;

            /**
             * Creates a new instance of DBComment.
             * @param int $comment_id The comment's id.
             * @param int $post_id The post's id.
             * @param string $username The username of the user that posted the comment.
             * @param string $profile_photo The user's profile photo.
             * @param string $content The comment's content.
             * @param int $likes The number of likes the comment has.
             * @param bool $liked Whether the comment was liked by the user or not.
             * @param bool $owner Whether the comment was made by the user or not.
             * @return void
             */
            public function __construct($comment_id = null, $post_id = null, $username = null, 
                    $profile_photo = null, $content = null, $likes = null, $liked = null, $owner = null) {
                $this->comment_id = $comment_id;
                $this->post_id = $post_id;
                $this->username = $username;
                $this->profile_photo = $profile_photo;
                $this->content = $content;
                $this->likes = $likes;
                $this->liked = $liked;
                $this->owner = $owner;
            }

            /**
             * Returns the object's properties as an associative array.
             * @return array The object's properties as an associative array.
             */
            public function jsonSerialize() {
                return [
                    "comment_id" => $this->comment_id,
                    "post_id" => $this->post_id,
                    "username" => $this->username,
                    "profile_photo" => $this->profile_photo,
                    "content" => $this->content,
                    "likes" => $this->likes,
                    "liked" => $this->liked,
                    "owner" => $this->owner
                ];
            }

            /**
             * Inserts the object's properties into the database.
             * @param \DBDriver $driver The database driver.
             */
            public function db_serialize($driver) {
                $sql = "INSERT INTO comment (post_id, username, content) VALUES (?, ?, ?)";
                try {
                    $driver->query($sql, $this->post_id, $this->username, $this->content);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }

            /**
             * Deletes the object's properties from the database.
             * @param \DBDriver $driver The database driver.
             * @return void
             */
            public function db_delete($driver) {
                $sql = "DELETE FROM comment WHERE comment_id = ?";
                try {
                    $driver->query($sql, $this->comment_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }

            public function getUser() {
                return $this->username;
            }
        }

        /**
         * Class that represents a like in the database.
         * @property int $post_id The post's id.
         * @property string $username The username of the user that liked the post.
         */
        class DBPostLike implements \DBTable {
            private $post_id;
            private $username;

            /**
             * Creates a new instance of DBPostLike.
             * @param int $post_id The post's id.
             * @param string $username The username of the user that liked the post.
             * @return void
             */
            public function __construct($post_id = null, $username = null) {
                $this->post_id = $post_id;
                $this->username = $username;
            }

            /**
             * Returns the object's properties as an associative array.
             * @return array The object's properties as an associative array.
             */
            public function jsonSerialize() {
                return [
                    "post_id" => $this->post_id,
                    "username" => $this->username
                ];
            }

            /**
             * Inserts the object's properties into the database.
             * @param \DBDriver $driver The database driver.
             * @return void
             */
            public function db_serialize($driver) {
                $sql = "INSERT INTO post_like (post_id, username) VALUES (?, ?)";
                try {
                    $driver->query($sql, $this->post_id, $this->username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }

            /**
             * Deletes the object's properties from the database.
             * @param \DBDriver $driver The database driver.
             * @return void
             */
            public function db_delete($driver) {
                $sql = "DELETE FROM post_like WHERE post_id = ? AND username = ?";
                try {
                    $driver->query($sql, $this->post_id, $this->username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        /**
         * Class that represents a like in the database.
         * @property int $comment_id The comment's id.
         * @property string $username The username of the user that liked the comment.
         */
        class DBCommentLike implements \DBTable {
            private $comment_id;
            private $username;

            /**
             * Creates a new instance of DBCommentLike.
             * @param int $comment_id The comment's id.
             * @param string $username The username of the user that liked the comment.
             * @return void
             */
            public function __construct($comment_id = null, $username = null) {
                $this->comment_id = $comment_id;
                $this->username = $username;
            }

            /**
             * Returns the object's properties as an associative array.
             * @return array The object's properties as an associative array.
             */
            public function jsonSerialize() {
                return [
                    "comment_id" => $this->comment_id,
                    "username" => $this->username
                ];
            }

            /**
             * Inserts the object's properties into the database.
             * @param \DBDriver $driver The database driver.
             * @return void
             */
            public function db_serialize($driver) {
                $sql = "INSERT INTO comment_like (comment_id, username) VALUES (?, ?)";
                try {
                    $driver->query($sql, $this->comment_id, $this->username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }

            /**
             * Deletes the object's properties from the database.
             * @param \DBDriver $driver The database driver.
             * @return void
             */
            public function db_delete($driver) {
                $sql = "DELETE FROM comment_like WHERE comment_id = ? AND username = ?";
                try {
                    $driver->query($sql, $this->comment_id, $this->username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        /**
         * Class that provides utility methods for the Post namespace.
         */
        class PostUtility {
            /**
             * Returns a DBPost object from the database with the given post id.
             * @param \DBDriver $driver The database driver.
             * @param int $post_id The post's id.
             * @return DBPost|null The DBPost object.
             */
            public static function from_db_with_post_id(\DBDriver $driver, $post_id) {
                $sql = "SELECT * FROM post WHERE post_id = ?";
                try {
                    $result = $driver->query($sql, $post_id);
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

            /**
             * Returns a DBPost object from the given form data.
             * @param int $event_id The event's id.
             * @param string $username The username of the user that posted the image.
             * @param string $image The image's path.
             * @param string $description The post's description.
             * @param int $event_post Whether the post is an event post or not.
             * @return DBPost The DBPost object.
             */
            public static function from_form($event_id, $username, $image, $description, $event_post) {
                return new DBPost(null, $event_id, $username, $image, $description, null, null, $event_post);
            }

            /**
             * Returns a DBPost object from the database with the given event id.
             * @param \DBDriver $driver The database driver.
             * @param int $event_id The event's id.
             * @return DBPost|null The DBPost object.
             */
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

            /**
             * Returns the description of the post with the given post id.
             * @param \DBDriver $driver The database driver.
             * @param int $post_id The post's id.
             * @return string|null The post's description.
             */
            public static function get_description_with_post_id(\DBDriver $driver, $post_id) {
                $sql = "SELECT description FROM post WHERE post_id = ?";
                try {
                    $result = $driver->query($sql, $post_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                if ($result->num_rows == 0) {
                    return null;
                }
                $row = $result->fetch_assoc();
                return $row["description"];
            }

            /**
             * Returns all posts from the database with the given event id.
             * @param \DBDriver $driver The database driver.
             * @param int $event_id The event's id.
             * @param string $username The username of the user
             * @return array|null An array of DBPost objects.
             * @throws \Exception If an error occurs while querying the database.
             */
            public static function from_db_all_posts_with_event_id(\DBDriver $driver, $event_id, $username) {
                $sql = "SELECT * FROM post WHERE event_id = ? AND event_post = 0";
                try {
                    $result = $driver->query($sql, $event_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                if ($result->num_rows == 0) {
                    return null;
                }
                $posts = array();
                for($i = 0; $i < $result->num_rows; $i++){
                    $row = $result->fetch_array();
                    $sql = "SELECT * FROM post_like WHERE post_id = ? AND username = ?";
                    try {
                        $liked = $driver->query($sql, $row["post_id"], $username)->num_rows > 0;
                    } catch (\Exception $e) {
                        throw new \Exception("Error while querying the database: " . $e->getMessage());
                    }
                    $post = new DBPost($row["post_id"], $row["event_id"], $row["username"], $row["image"], 
                            $row["description"], $row["posted"], $row["likes"], $row["event_post"], null, $liked);
                    array_push($posts, $post);
                }
                return $posts;
            }

            /**
             * Returns all posts from the database with the given username.
             * @param \DBDriver $driver The database driver.
             * @param string $username The username of the user.
             * @param string $watcher The username of the user watching the posts.
             * @return array|null An array of DBPost objects.
             * @throws \Exception If an error occurs while querying the database.
             */
            public static function from_db_with_username(\DBDriver $driver, $username, $watcher) {
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
                        $sql = "SELECT * FROM post_like WHERE post_id = ? AND username = ?";
                        try {
                            $liked = $driver->query($sql, $row["post_id"], $watcher)->num_rows > 0;
                        } catch (\Exception $e) {
                            throw new \Exception("Error while querying the database: " . $e->getMessage());
                        }
                        $post = new DBPost($row["post_id"], $row["event_id"], $row["username"], $row["image"], 
                                $row["description"], $row["posted"], $row["likes"], $row["event_post"], null, $liked);
                        array_push($posts, $post);
                    }

                }
                return $posts;
            }

            /**
             * Returns all posts from the database from the users followed by the given username.
             * @param \DBDriver $driver The database driver.
             * @param string $username The username of the user.
             * @param int $max_posts The maximum number of posts to return.
             * @return array|null An array of DBPost objects.
             * @throws \Exception If an error occurs while querying the database.
             */
            public static function recent_posts_followed(\DBDriver $driver, $username, $max_posts) {
                $sql = "SELECT P.*, U.profile_photo
                        FROM post P, user U
                        WHERE P.username IN (
                            SELECT followed
                            FROM relationship
                            WHERE follows = ?)
                        AND P.username = U.username
                        ORDER BY P.posted DESC
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
                                $row["description"], $row["posted"], $row["likes"], $row["event_post"], $row['profile_photo'], $liked);
                        array_push($posts, $post);
                    }
                }
                return $posts;
            }

            /**
             * Return a comment from the database with the given comment id.
             * @param \DBDriver $driver The database driver.
             * @param int $comment_id The comment's id.
             * @return DBComment|null The DBComment object.
             */
            public static function comment_with_id(\DBDriver $driver, $comment_id) {
                $sql = "SELECT * FROM comment WHERE comment_id = ?";
                try {
                    $result = $driver->query($sql, $comment_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                if ($result->num_rows == 0) {
                    return null;
                }
                $row = $result->fetch_assoc();
                return new DBComment($row["comment_id"], $row["post_id"], $row["username"], 
                        null, $row["content"], $row["likes"]);
            }

            /**
             * Returns all comments from the database with the given post id.
             * @param \DBDriver $driver The database driver.
             * @param int $post_id The post's id.
             * @param string $username The username of the user.
             * @return array|null An array of DBComment objects.
             * @throws \Exception If an error occurs while querying the database.
             */
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
                        $owner = false;
                        $sql = "SELECT * FROM post WHERE post_id = ? AND username = ?";
                        try {
                            $owner = $driver->query($sql, $post_id, $username)->num_rows > 0;
                        } catch (\Exception $e) {
                            throw new \Exception("Error while querying the database: " . $e->getMessage());
                        }
                        if (!$owner) {
                            $owner = $row["username"] == $username;
                        }
                        $comment = new DBComment($row["comment_id"], $row["post_id"], $row["username"], 
                                $row["profile_photo"], $row["content"], $row["likes"], $liked, $owner);
                        array_push($comments, $comment);
                    }
                }
                return $comments;
            }

            /**
             * Insert a comment into the database.
             * @param \DBDriver $driver The database driver.
             * @param int $post_id The post's id.
             * @param string $username The username of the user.
             * @param string $content The comment's content.
             * @return void
             */
            public static function insert_comment(\DBDriver $driver, $post_id, $username, $content) {
                $comment = new DBComment(null, $post_id, $username, null, $content);
                $comment->db_serialize($driver);
            }

            /**
             * Delete a comment from the database with the given comment id.
             * @param \DBDriver $driver The database driver.
             * @param int $comment_id The comment's id.
             * @return void
             */
            public static function delete_comment(\DBDriver $driver, $comment_id) {
                $com = new DBComment($comment_id);
                $com->db_delete($driver);
            }

            /**
             * Insert a like into the database.
             * @param \DBDriver $driver The database driver.
             * @param int $like_id The like's id.
             * @param string $username The username of the user.
             * @param string $type The type of the like.
             * @return void
             */
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

            /**
             * Delete a like from the database.
             * @param \DBDriver $driver The database driver.
             * @param int $like_id The like's id.
             * @param string $username The username of the user.
             * @param string $type The type of the like.
             * @return void
             */
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

            /**
             * Returns a DBPost object from the database with the given event id.
             * @param \DBDriver $driver The database driver.
             * @param int $post_id The post's id.
             * @param string $username The username of the user.
             * @return DBPost|null The DBPost object.
             */
            public static function load_post_event($driver, $event_id, $username) {
                $sql = "SELECT * FROM post WHERE event_id = ? AND event_post = 1";
                try {
                    $result = $driver->query($sql, $event_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $row = $result->fetch_assoc();
                $sql = "SELECT * FROM post_like WHERE post_id = ? AND username = ?";
                try {
                    $liked = $driver->query($sql, $row["post_id"], $username)->num_rows > 0;
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                return new DBPost($row["post_id"], $row["event_id"], $row["username"], $row["image"], 
                    $row["description"], $row["posted"], $row["likes"], $row["event_post"], null, $liked);
            }
        }
    }
?>