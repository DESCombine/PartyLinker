<?php

    namespace Event {
        require_once("dbtable.php");
        require_once("dbdriver.php");
        class DBEvent implements \DBTable{
            private $event_id;	
            private $name;
            private $location;
            private $starting_date;
            private $ending_date;
            private $vips;
            private $max_capacity;
            private $price;
            private $minimum_age;

            public function __construct($event_id = null, $name = null, $location = null, $starting_date = null, 
                    $ending_date = null, $vips = null, $max_capacity = null, $price = null, $minimum_age = null) {
                $this->event_id = $event_id;
                $this->name = $name;
                $this->location = $location;
                $this->starting_date = $starting_date;
                $this->ending_date = $ending_date;
                $this->vips = $vips;
                $this->max_capacity = $max_capacity;
                $this->price = $price;
                $this->minimum_age = $minimum_age;
            }

            public function jsonSerialize() {
                return [
                    "event_id" => $this->event_id,
                    "name" => $this->name,
                    "location" => $this->location,
                    "starting_date" => $this->starting_date,
                    "ending_date" => $this->ending_date,
                    "vips" => $this->vips,
                    "max_capacity" => $this->max_capacity,
                    "price" => $this->price,
                    "minimum_age" => $this->minimum_age
                ];
            }

            public function db_serialize($driver) {
                $sql = "INSERT INTO event (event_id, name, location, starting_date, ending_date, vips, max_capacity, price, minimum_age) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                try {
                    $driver->query($sql, $this->event_id, $this->name, $this->location, $this->starting_date, 
                            $this->ending_date, $this->vips, $this->max_capacity, $this->price, $this->minimum_age);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        class DBPartecipation implements \DBTable {
            private $event_id;
            private $username;
            private $profile_photo;

            public function __construct($event_id = null, $username = null, $profile_photo = null) {
                $this->event_id = $event_id;
                $this->username = $username;
                $this->profile_photo = $profile_photo;
            }

            public function jsonSerialize() {
                return [
                    "event_id" => $this->event_id,
                    "username" => $this->username,
                    "profile_photo" => $this->profile_photo
                ];
            }

            public function db_serialize(\DBDriver $driver) {
                $sql = "INSERT INTO partecipation (event_id, username) VALUES (?, ?)";
                try {
                    $driver->query($sql, $this->event_id, $this->username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        class EventUtility {
            public static function from_db_with_event_id(\DBDriver $driver, $event_id) {
                $sql = "SELECT * FROM event WHERE event_id = ?";
                try {
                    $result = $driver->query($sql, $event_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                if ($result->num_rows == 0) {
                    return null;
                }
                $row = $result->fetch_assoc();
                return new DBEvent($row["event_id"], $row["name"], $row["location"], $row["starting_date"], 
                        $row["ending_date"], $row["vips"], $row["max_capacity"], $row["price"], $row["minimum_age"]);
            }

            public static function from_db_with_username(\DBDriver $driver, $username) {
                $sql = "SELECT * FROM event WHERE username = ?";
                try {
                    $result = $driver->query($sql, $username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $events = array();
                if ($result->num_rows > 0) {
                    for($i = 0; $i < $result->num_rows; $i++){
                        $row = $result->fetch_array();
                        $event = new DBEvent($row["event_id"], $row["name"], $row["location"], $row["starting_date"], 
                                $row["ending_date"], $row["vips"], $row["max_capacity"], $row["price"], $row["minimum_age"]);
                        array_push($events, $event);
                    }
                }
                return $events;
            }

            public static function retrieve_partecipations($driver, $event_id, $username) {
                $sql = "SELECT P.*, U.profile_photo
                        FROM partecipation P, user U
                        WHERE P.event_id = ?
                        AND P.username = U.username";
                try {
                    $result = $driver->query($sql, $event_id);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $partecipations = array();
                if ($result->num_rows > 0) {
                    for ($i = 0; $i < $result->num_rows; $i++) {
                        $row = $result->fetch_array();
                        $partecipation = new DBPartecipation($row["event_id"], $row["username"], $row["profile_photo"]);
                        array_push($partecipations, $partecipation);
                    }
                }
                return $partecipations;
            }

            public static function insert_partecipation($driver, $event_id, $username) {
                $part = new DBPartecipation($event_id, $username);
                $part->db_serialize($driver);
            }

            public static function delete_partecipation($driver, $event_id, $username) {
                $sql = "DELETE FROM partecipation WHERE event_id = ? AND username = ?";
                try {
                    $driver->query($sql, $event_id, $username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }
    }
?>