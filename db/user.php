<?php

namespace User{
    require("dbtable.php");
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    class DBUser implements \DBTable {
        private $username;
        private $email;
        private $name;
        private $surname;
        private $birth_date;
        private $photo;
        private $bio;
        private $phone;
        private $password;
        private $online;
        
        // TODO: add relationship to other user
        public function __construct($username = null, $email = null, $name = null, $surname = null, 
                $birth_date = null, $photo = null, $bio = null, $phone = null, $password = null, $online = null) {
            $this->username = $username;
            $this->email = $email;
            $this->name = $name;
            $this->surname = $surname;
            $this->birth_date = $birth_date;
            $this->photo = $photo;
            $this->bio = $bio;
            $this->phone = $phone;
            $this->password = $password;
            $this->online = $online;
        }
        public function jsonSerialize() {
            return [
                "username" => $this->username,
                "email" => $this->email,
                "name" => $this->name,
                "surname" => $this->surname,
                "birth_date" => $this->birth_date,
                "photo" => $this->photo,
                "bio" => $this->bio,
                "phone" => $this->phone,
                "online" => $this->online
            ];
        }
        public function create_password(string $password) {
            $this->password = password_hash($password, PASSWORD_DEFAULT);
        }
        // insert into database
        public function db_serialize(\DBDriver $driver) {
            if ($this->password == null) {
                throw new \Exception("Password not set");
            }
            $sql = "INSERT INTO user (username, email, name, surname, birth_date, photo, bio, phone, password, online) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            try {
                $driver->query($sql, $this->username, $this->email, $this->name, $this->surname, date($this->birth_date), 
                        $this->photo, $this->bio, $this->phone, $this->password, $this->online);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }

        }

        public function check_password(string $password) {
            return password_verify($password, $this->password);
        }
    }

    class DBPartecipation implements \DBTable {
        private $event_id;
        private $partecipant;

        public function __construct($event_id = null, $partecipant = null) {
            $this->event_id = $event_id;
            $this->partecipant = $partecipant;
        }

        public function jsonSerialize() {
            return [
                "event_id" => $this->event_id,
                "partecipant" => $this->partecipant
            ];
        }

        public function db_serialize(\DBDriver $driver) {
            $sql = "INSERT INTO partecipation (event_id, partecipant) VALUES (?, ?)";
            try {
                $driver->query($sql, $this->event_id, $this->partecipant);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
        }
    }

    class UserUtility {
        public static function from_form( string $username, string $email, string $name, string $surname, string $birth_date, string $photo, string $bio, string $phone, string $password = "" ){
            $user = new DBUser($username, $email, $name, $surname, $birth_date, $photo, $bio, $phone);
            if( $password != "" ){
                $user->create_password($password);
            }
            return $user;
        }

        public static function from_db_with_username($driver, string $username){
            $sql = "SELECT * FROM user WHERE username = ?";
            try {
                $result = $driver->query($sql, $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user = new DBUser($row["username"], $row["email"], $row["name"], $row["surname"], 
                        $row["birth_date"], $row["photo"], $row["bio"], $row["phone"], $row["password"], $row["online"]);
            } else {
                return null;
            }
            return $user;
        }

        public static function from_db_with_username_like( \DBDriver $driver, string $username ): array{
            $sql = "SELECT * FROM user WHERE username LIKE ?";

            try {
                $result = $driver->query($sql, "%".$username."%");
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            $users = array();
            if ($result->num_rows > 0) {
                for($i = 0; $i < $result->num_rows; $i++){
                    $row = $result->fetch_array();
                    $user = new DBUser($row["username"], $row["email"], $row["name"], $row["surname"], $row["birth_date"], 
                            $row["photo"], $row["bio"], $row["phone"], $row["password"], $row["online"]);
                    array_push($users, $user);
                }
            }
            return $users;
        }

        public static function from_db_with_email(\DBDriver $driver, string $email ){
            $user = null;
            $sql = "SELECT * FROM user WHERE email = ?";
            try {
                $result = $driver->query($sql, $email);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user = new DBUser($row["username"], $row["email"], $row["name"], $row["surname"], $row["birth_date"], 
                        $row["photo"], $row["bio"], $row["phone"], $row["password"], $row["online"]);
            } else {
                return null;
            }
            return $user;

        }

        public static function from_db_with_phone( \DBDriver $driver, string $phone ){
            $user = null;
            $sql = "SELECT * FROM user WHERE phone = ?";
            try {
                $result = $driver->query($sql, $phone);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user = new DBUser($row["username"], $row["email"], $row["name"], $row["surname"], $row["birth_date"], 
                        $row["photo"], $row["bio"], $row["phone"], $row["password"], $row["online"]);
            } else {
                return null;
            }
            return $user;

        }

        public static function check_if_available(\DBDriver $driver, $username="", $email="", $phone=""): void{
            if(!empty($username)){
                $sql = "SELECT * FROM user WHERE username = ?";
                try {
                    $result = $driver->query($sql, $username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                if ($result->num_rows > 0) {
                    throw new UsernameTaken("Username already taken");
                }
            } else if(!empty($email)){
                $sql = "SELECT * FROM user WHERE email = ?";
                try {
                    $result = $driver->query($sql, $email);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                if ($result->num_rows > 0) {
                    throw new EmailTaken("Email already taken");
                }
            } else if(!empty($phone)){
                $sql = "SELECT * FROM user WHERE phone = ?";
                try {
                    $result = $driver->query($sql, $phone);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                if ($result->num_rows > 0) {
                    throw new PhoneTaken("Phone already taken");
                }
            }
        }

        public static function retrieve_username_from_token($token): string {
            if(preg_match("/Bearer\s(\S+)/", $token, $matches) !== 1){
                throw new \Exception("Invalid token");
            } else {
                $token = $matches[1];
                $decoded = JWT::decode($token, new Key(getenv("PL_JWTKEY"), 'HS256'));
                return ((array) $decoded)["username"];
            }
        }

        public static function retrieve_partecipation($driver, $event_id) {
            $sql = "SELECT * FROM event_partecipation WHERE event_id = ?";
            try {
                $result = $driver->query($sql, $event_id);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            $partecipations = array();
            if ($result->num_rows > 0) {
                for($i = 0; $i < $result->num_rows; $i++){
                    $row = $result->fetch_array();
                    $partecipation = new DBPartecipation($row["event_id"], $row["partecipant"]);
                    array_push($partecipations, $partecipation);
                }
            }
            return $partecipations;
        }

        public static function retrieve_online($driver) {
            $sql = "SELECT * FROM user WHERE online = 1";
            try {
                $result = $driver->query($sql);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            $users = array();
            if ($result->num_rows > 0) {
                for($i = 0; $i < $result->num_rows; $i++){
                    $row = $result->fetch_array();
                    $user = new DBUser($row["username"], $row["email"], $row["name"], $row["surname"], $row["birth_date"], 
                            $row["photo"], $row["bio"], $row["phone"], $row["password"], $row["online"]);
                    array_push($users, $user);
                }
            }
            return $users;
        }
    }
    class UsernameTaken extends \Exception {}
    class EmailTaken extends \Exception {}
    class PhoneTaken extends \Exception {}
}


?>