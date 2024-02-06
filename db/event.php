<?php

    namespace Event {
        require_once("dbtable.php");
        require_once("dbdriver.php");
        /**
         * This class represents an event in the database.
         * @property int $event_id The id of the event.
         * @property string $name The name of the event.
         * @property string $location The location of the event.
         * @property string $starting_date The starting date of the event.
         * @property string $ending_date The ending date of the event.
         * @property string $vips The vips of the event.
         * @property int $max_capacity The maximum capacity of the event.
         * @property float $price The price of the event.
         * @property int $minimum_age The minimum age to partecipate to the event.
         * 
         */
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

            /**
             * Creates a new event.
             * @param int $event_id The id of the event.
             * @param string $name The name of the event.
             * @param string $location The location of the event.  
             * @param string $starting_date The starting date of the event.
             * @param string $ending_date The ending date of the event.
             * @param string $vips The vips of the event.
             * @param int $max_capacity The maximum capacity of the event.
             * @param float $price The price of the event.
             * @param int $minimum_age The minimum age to partecipate to the event.
             */
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

            /**
             * Returns the event as a json object.
             * @return array The event as a json object.
             */
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

            /**
             * Serializes the event in the database.
             * @param \DBDriver $driver The driver to use to serialize the event.
             * @return int The id of the inserted event.
             */
            public function db_serialize($driver) {
                $sql = "INSERT INTO event (name, location, starting_date, ending_date, vips, max_capacity, price, minimum_age) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                try {
                    $driver->query($sql, $this->name, $this->location, $this->starting_date, 
                            $this->ending_date, $this->vips, $this->max_capacity, $this->price, $this->minimum_age);
                    // returns the id of the inserted event
                    return $driver->get_last_inserted_id();
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }
        /**
         * This class represents a partecipation in the database.
         * @property int $event_id The id of the event.
         * @property string $username The username of the user.
         * @property string $profile_photo The profile photo of the user.
         * @property bool $partecipating True if the user is partecipating to the event, false otherwise.
         */
        class DBPartecipation implements \DBTable {
            private $event_id;
            private $username;
            private $profile_photo;
            private $partecipating;

            /**
             * Creates a new partecipation.
             * @param int $event_id The id of the event.
             * @param string $username The username of the user.
             * @param string $profile_photo The profile photo of the user.
             * @param bool $partecipating True if the user is partecipating to the event, false otherwise.
             */
            public function __construct($event_id = null, $username = null, $profile_photo = null, $partecipating = null) {
                $this->event_id = $event_id;
                $this->username = $username;
                $this->profile_photo = $profile_photo;
                $this->partecipating = $partecipating;
            }

            /**
             * Returns the partecipation as a json object.
             * @return array The partecipation as a json object.
             */
            public function jsonSerialize() {
                return [
                    "event_id" => $this->event_id,
                    "username" => $this->username,
                    "profile_photo" => $this->profile_photo,
                    "partecipating" => $this->partecipating
                ];
            }

            /**
             * Serializes the partecipation in the database.
             * @param \DBDriver $driver The driver to use to serialize the partecipation.
             */
            public function db_serialize(\DBDriver $driver) {
                $sql = "INSERT INTO partecipation (event_id, username) VALUES (?, ?)";
                try {
                    $driver->query($sql, $this->event_id, $this->username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }

            /**
             * Deletes the partecipation from the database.
             * @param \DBDriver $driver The driver to use to delete the partecipation.
             */
            public function db_delete(\DBDriver $driver) {
                $sql = "DELETE FROM partecipation WHERE event_id = ? AND username = ?";
                try {
                    $driver->query($sql, $this->event_id, $this->username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        /**
         * This class contains utility methods to work with events.
         */
        class EventUtility {
            /**
             * Creates a new event from a form.
             * @param string $name The name of the event.
             * @param string $location The location of the event.
             * @param string $starting_date The starting date of the event.
             * @param string $ending_date The ending date of the event.
             * @param string $vips The vips of the event.
             * @param int $max_capacity The maximum capacity of the event.
             * @param float $price The price of the event.
             * @param int $minimum_age The minimum age to partecipate to the event.
             * @return DBEvent The event created from the form.

             */
            public static function from_form($name, $location, $starting_date, $ending_date, $vips, $max_capacity, $price, $minimum_age) {
                return new DBEvent(null, $name, $location, $starting_date, $ending_date, $vips, $max_capacity, $price, $minimum_age);
            }
            /**
             * Creates a new event from the database.
             * @param \DBDriver $driver The driver to use to create the event.
             * @param int $event_id The id of the event.
             * @return DBEvent|null The event created from the database.
             */
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

            /**
             * Return all the events of an user from the database.
             * @param \DBDriver $driver The driver to use to create the event.
             * @param string $username The username of the user.
             * @return array The events of the user.
             */
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

            /**
             * Return all the events from the database from a given name.
             * @param \DBDriver $driver The driver to use to create the event.
             * @return array The events.
             */
            public static function from_db_with_name(\DBDriver $driver, $name) {
                $sql = "SELECT E.event_id, E.name, E.starting_date, P.image
                        FROM event E, post P 
                        WHERE E.name = ?
                        AND E.event_id = P.event_id
                        AND P.event_post = 1";
                try {
                    $result = $driver->query($sql, $name);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $events = array();
                if ($result->num_rows > 0) {
                    for($i = 0; $i < $result->num_rows; $i++){
                        $row = $result->fetch_array();
                        $event = [
                            "event_id" => $row["event_id"],
                            "name" => $row["name"],
                            "image" => $row["image"],
                            "date" => $row["starting_date"]
                        ];
                        array_push($events, $event);
                    }
                }
                return $events;
            }

            /**
             * Return all the events from the database with a name similar to the one given.
             * @param \DBDriver $driver The driver to use to create the event.
             * @param string $name The name of the event.
             * @return array The events.
             * @throws \Exception If an error occurs.
             */
            public static function from_db_with_name_like(\DBDriver $driver, $name) {
                $sql = "SELECT E.event_id, E.name, E.starting_date, P.image
                        FROM event E, post P 
                        WHERE E.name LIKE ?
                        AND E.event_id = P.event_id
                        AND P.event_post = 1";
                try {
                    $result = $driver->query($sql, "%".$name."%");
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                $events = array();
                if ($result->num_rows > 0) {
                    for($i = 0; $i < $result->num_rows; $i++){
                        $row = $result->fetch_array();
                        $event = [
                            "event_id" => $row["event_id"],
                            "name" => $row["name"],
                            "image" => $row["image"],
                            "date" => $row["starting_date"]
                        ];
                        array_push($events, $event);
                    }
                }
                return $events;
            }

            /**
             * Load recent past and future events.
             * @param \DBDriver $driver The driver to use to create the event.
             * @return array The events.
             * @throws \Exception If an error occurs.
             */
            public static function from_db_recents_and_future_events(\DBDriver $driver) {
                $sql = "SELECT * FROM event WHERE DATE_SUB(NOW(), INTERVAL 1 WEEK) ORDER BY starting_date ASC ";
                try {
                    $result = $driver->query($sql);
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

            /**
             * Load all the partecipations of an event from the database.
             * @param \DBDriver $driver The driver to use to create the event.
             * @param int $event_id The id of the event.
             * @param string $username The username of the user.
             * @return array The partecipations of the event.
             * @throws \Exception If an error occurs.
             */
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
                        $partecipation = new DBPartecipation($row["event_id"], $row["username"], $row["profile_photo"], $row["username"] == $username);
                        array_push($partecipations, $partecipation);
                    }
                }
                return $partecipations;
            }

            /**
             * Insert a partecipation in the database.
             * @param \DBDriver $driver The driver to use to create the event.
             * @param int $event_id The id of the event.
             * @param string $username The username of the user.
             */
            public static function insert_partecipation($driver, $event_id, $username) {
                $part = new DBPartecipation($event_id, $username);
                $part->db_serialize($driver);
            }

            /**
             * Delete a partecipation from the database.
             * @param \DBDriver $driver The driver to use to create the event.
             * @param int $event_id The id of the event.
             * @param string $username The username of the user.
             */
            public static function delete_partecipation($driver, $event_id, $username) {
                $part = new DBPartecipation($event_id, $username);
                $part->db_delete($driver);
            }
        }
    }
?>