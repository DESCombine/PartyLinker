<?php

    namespace EventPost {
        require_once("dbtable.php");
        require_once("dbdriver.php");
        class DBEventPost implements \DBTable{
            private $event_id;	
            private $organizer;
            private $name;
            private $description;
            private $location;
            private $image;
            private $starting_date;
            private $ending_date;
            private $posted;
            private $like;
            private $vip;
            private $max_capacity;
            private $price;
            private $minimum_age;

            public function __construct($event_id = null, $organizer = null, $name = null, 
                    $description = null, $location = null, $image = null, $starting_date = null, 
                    $ending_date = null, $posted = null, $like = null, $vip = null, $max_capacity = null, 
                    $price = null, $minimum_age = null) {
                $this->event_id = $event_id;
                $this->organizer = $organizer;
                $this->name = $name;
                $this->description = $description;
                $this->location = $location;
                $this->image = $image;
                $this->starting_date = $starting_date;
                $this->ending_date = $ending_date;
                $this->posted = $posted;
                $this->like = $like;
                $this->vip = $vip;
                $this->max_capacity = $max_capacity;
                $this->price = $price;
                $this->minimum_age = $minimum_age;
            }

            public function jsonSerialize() {
                return [
                    "event_id" => $this->event_id,
                    "organizer" => $this->organizer,
                    "name" => $this->name,
                    "description" => $this->description,
                    "location" => $this->location,
                    "image" => $this->image,
                    "starting_date" => $this->starting_date,
                    "ending_date" => $this->ending_date,
                    "posted" => $this->posted,
                    "like" => $this->like,
                    "vip" => $this->vip,
                    "max_capacity" => $this->max_capacity,
                    "price" => $this->price,
                    "minimum_age" => $this->minimum_age
                ];
            }

            public function db_serialize($driver) {
                $sql = "INSERT INTO event_post (event_id, organizer, name, description, location, image, 
                        starting_date, ending_date, posted, like, vip, max_capacity, price, minimum_age) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                try {
                    $driver->query($sql, $this->event_id, $this->organizer, $this->name, $this->description, 
                            $this->location, $this->image, $this->starting_date, $this->ending_date, $this->posted, 
                            $this->like, $this->vip, $this->max_capacity, $this->price, $this->minimum_age);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        class DBEventComment implements \DBTable {
            private $comment_id;
            private $event_id;
            private $content;
            private $like;

            public function __construct($comment_id = null, $event_id = null, $content = null, $like = null) {
                $this->comment_id = $comment_id;
                $this->event_id = $event_id;
                $this->content = $content;
                $this->like = $like;
            }

            public function jsonSerialize() {
                return [
                    "comment_id" => $this->comment_id,
                    "event_id" => $this->event_id,
                    "content" => $this->content,
                    "like" => $this->like
                ];
            }

            public function db_serialize($driver) {
                $sql = "INSERT INTO event_comment (comment_id, event_id, content, like) VALUES (?, ?, ?, ?)";
                try {
                    $driver->query($sql, $this->comment_id, $this->event_id, $this->content, $this->like);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        class EventPostUtility {
            public static function from_db_with_event_id(\DBDriver $driver, $event_id) {
                $sql = "SELECT * FROM event_post WHERE event_id = ?";
                try {
                    $result = $driver->query($sql, $event_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                if ($result->num_rows == 0) {
                    return null;
                }
                $row = $result->fetch_assoc();
                return new DBEventPost($row["event_id"], $row["organizer"], $row["name"], $row["description"], 
                        $row["location"], $row["image"], $row["starting_date"], $row["ending_date"], $row["posted"], 
                        $row["like"], $row["vip"], $row["max_capacity"], $row["price"], $row["minimum_age"]);
            }

            public static function from_db_with_organizer(\DBDriver $driver, $organizer) {
                $sql = "SELECT * FROM event_photo WHERE organizer = ?";
                try {
                    $result = $driver->query($sql, $organizer);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $posts = array();
                if ($result->num_rows > 0) {
                    for($i = 0; $i < $result->num_rows; $i++){
                        $row = $result->fetch_array();
                        $post = new DBEventPost($row["event_id"], $row["organizer"], $row["name"], $row["description"], 
                                $row["location"], $row["image"], $row["starting_date"], $row["ending_date"], $row["posted"], 
                                $row["like"], $row["vip"], $row["max_capacity"], $row["price"], $row["minimum_age"]);
                        array_push($posts, $post);
                    }
                }
                return $posts;
            }

            public static function recent_events_followed(\DBDriver $driver, $user_id, $max_photos) {
                $sql = "SELECT *
                        FROM event_post
                        WHERE organizer IN (
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
                $posts = array();
                if ($result->num_rows > 0) {
                    for($i = 0; $i < $result->num_rows; $i++){
                        $row = $result->fetch_array();
                        $post = new DBEventPost($row["event_id"], $row["organizer"], $row["name"], $row["description"], 
                                $row["location"], $row["image"], $row["starting_date"], $row["ending_date"], $row["posted"], 
                                $row["like"], $row["vip"], $row["max_capacity"], $row["price"], $row["minimum_age"]);
                        array_push($posts, $post);
                    }
                }
                return $posts;
            }

            public static function comments_with_event(\DBDriver $driver, $event_id) {
                $sql = "SELECT * FROM event_comment WHERE event_id = ?";
                try {
                    $result = $driver->query($sql, $event_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $comments = array();
                if ($result->num_rows > 0) {
                    for($i = 0; $i < $result->num_rows; $i++){
                        $row = $result->fetch_array();
                        $comment = new DBEventComment($row["comment_id"], $row["event_id"], $row["content"], $row["like"]);
                        array_push($comments, $comment);
                    }
                }
                return $comments;

            }
        }
    }
?>