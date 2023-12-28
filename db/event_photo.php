<?php

    namespace EventPhoto {
        require_once("dbtable.php");
        require_once("dbdriver.php");
        class DBEventPhoto implements \DBTable{
            private $photo_id;
            private $event_id;	
            private $poster;
            private $photo;
            private $posted;
            private $like;

            public function __construct($photo_id = null, $event_id = null, $poster = null, $photo = null, $posted = null, $like = null) {
                $this->photo_id = $photo_id;
                $this->event_id = $event_id;
                $this->poster = $poster;
                $this->photo = $photo;
                $this->posted = $posted;
                $this->like = $like;
            }

            public function jsonSerialize() {
                return [
                    "photo_id" => $this->photo_id,
                    "event_id" => $this->event_id,
                    "poster" => $this->poster,
                    "photo" => $this->photo,
                    "posted" => $this->posted,
                    "like" => $this->like
                ];
            }

            public function db_serialize($driver) {
                $sql = "INSERT INTO event_photo (photo_id, event_id, poster, photo, posted, like) VALUES (?, ?, ?, ?, ?, ?)";
                try {
                    $driver->query($sql, $this->photo_id, $this->event_id, $this->poster, $this->photo, $this->posted, $this->like);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        class DBPhotoComment implements \DBTable {
            private $comment_id;
            private $photo_id;
            private $content;
            private $like;

            public function __construct($comment_id = null, $photo_id = null, $content = null, $like = null) {
                $this->comment_id = $comment_id;
                $this->photo_id = $photo_id;
                $this->content = $content;
                $this->like = $like;
            }

            public function jsonSerialize() {
                return [
                    "comment_id" => $this->comment_id,
                    "photo_id" => $this->photo_id,
                    "content" => $this->content,
                    "like" => $this->like
                ];
            }

            public function db_serialize($driver) {
                $sql = "INSERT INTO photo_comment (comment_id, photo_id, content, like) VALUES (?, ?, ?, ?)";
                try {
                    $driver->query($sql, $this->comment_id, $this->photo_id, $this->content, $this->like);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        class EventPhotoUtility {
            public static function from_db_with_event_id(\DBDriver $driver, $event_id) {
                $sql = "SELECT * FROM event_photo WHERE event_id = ?";
                try {
                    $result = $driver->query($sql, $event_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                if ($result->num_rows == 0) {
                    return null;
                }
                $row = $result->fetch_assoc();
                return new DBEventPhoto($row["photo_id"], $row["event_id"], $row["poster"], $row["photo"], $row["posted"], $row["like"]);
            }

            public static function from_db_with_poster(\DBDriver $driver, $poster) {
                $sql = "SELECT * FROM event_photo WHERE poster = ?";
                try {
                    $result = $driver->query($sql, $poster);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $photos = array();
                if ($result->num_rows > 0) {
                    for($i = 0; $i < $result->num_rows; $i++){
                        $row = $result->fetch_array();
                        $photo = new DBEventPhoto($row["photo_id"], $row["event_id"], $row["poster"], $row["photo"], $row["posted"], $row["like"]);
                        array_push($photos, $photo);
                    }
                }
                return $photos;
            }

            public static function recent_photos_followed(\DBDriver $driver, $user_id, $max_photos) {
                $sql = "SELECT *
                        FROM event_photo
                        WHERE poster IN (
                            SELECT followed
                            FROM follow
                            WHERE follower = ?)
                        ORDER BY posted DESC
                        LIMIT ?";
                try {
                    $result = $driver->query($sql, $user_id, $max_photos);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $photos = array();
                if ($result->num_rows > 0) {
                    for($i = 0; $i < $result->num_rows; $i++){
                        $row = $result->fetch_array();
                        $photo = new DBEventPhoto($row["photo_id"], $row["event_id"], $row["poster"], $row["photo"], $row["posted"], $row["like"]);
                        array_push($photos, $photo);
                    }
                }
                return $photos;
            }

            public static function comments_with_photo(\DBDriver $driver, $photo_id) {
                $sql = "SELECT * FROM photo_comment WHERE photo_id = ?";
                try {
                    $result = $driver->query($sql, $photo_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $comments = array();
                if ($result->num_rows > 0) {
                    for($i = 0; $i < $result->num_rows; $i++){
                        $row = $result->fetch_array();
                        $comment = new DBPhotoComment($row["comment_id"], $row["photo_id"], $row["content"], $row["like"]);
                        array_push($comments, $comment);
                    }
                }
                return $comments;

            }
        }
    }
?>