<?php

namespace User {
    require("dbtable.php");
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    class DBUser implements \DBTable {
        private $username;
        private $email;
        private $name;
        private $surname;
        private $birth_date;
        private $profile_photo;
        private $background;
        private $bio;
        private $phone;
        private $password;
        private $online;
        private $follows;
        private $followers;


        // TODO: add relationship to other user
        public function __construct($username = null, $email = null, $name = null, $surname = null, $birth_date = null, $profile_photo = null, $background = null, $bio = null, $phone = null, 
                $password = null, $online = null, $follows = null, $followers = null) {
            $this->username = $username;
            $this->email = $email;
            $this->name = $name;
            $this->surname = $surname;
            $this->birth_date = $birth_date;
            $this->profile_photo = $profile_photo;
            $this->background = $background;
            $this->bio = $bio;
            $this->phone = $phone;
            $this->password = $password;
            $this->online = $online;
            $this->follows = $follows;
            $this->followers = $followers;
        }
        public function jsonSerialize() {
            return [
                "username" => $this->username,
                "email" => $this->email,
                "name" => $this->name,
                "surname" => $this->surname,
                "birth_date" => $this->birth_date,
                "profile_photo" => $this->profile_photo,
                "background" => $this->background,
                "bio" => $this->bio,
                "phone" => $this->phone,
                "online" => $this->online,
                "followed" => $this->follows,
                "followers" => $this->followers
            ];
        }
        public function create_password(string $password) {
            $this->password = password_hash($password, PASSWORD_DEFAULT);
        }

        public function get_password() {
            return $this->password;
        }
        
        public function db_serialize(\DBDriver $driver) {
            if ($this->password == null) {
                throw new \Exception("Password not set");
            }
            $sql = "INSERT INTO user (username, email, name, surname, birth_date, profile_photo, background, bio, phone, password, online) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            try {
                $driver->query($sql, $this->username, $this->email, $this->name, $this->surname, date($this->birth_date), 
                        $this->profile_photo, $this->background, $this->bio, $this->phone, $this->password, $this->online);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }

        }

        public function check_password(string $password) {
            return password_verify($password, $this->password);
        }

        public function update_infos(\DBDriver $driver, string $name, string $surname, string $birth_date, string $email, string $phone, string $username, string $password, string $gender, int $organizer, 
                string $profilePhoto, string $bannerPhoto, string $bio, string $language, int $notifications, int $TFA) {
            // if profilephoto or bannerphoto are null, set them to the current ones
            $user = UserUtility::from_db_with_username($driver, $username);
            if ($profilePhoto == "") {
                $profilePhoto = $user->profile_photo;
            }
            if ($bannerPhoto == "") {
                $bannerPhoto = $user->background;
            }       
            $sql = "UPDATE user SET email = ?, name = ?, surname = ?, birth_date = ?, profile_photo = ?, background = ?, bio = ?, phone = ?, password = ? WHERE username = ?";
            try {
                $driver->query($sql, $email, $name, $surname, date($birth_date), $profilePhoto, 
                        $bannerPhoto, $bio, $phone, $password, $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            // check if settings exists
            $sql = "SELECT * FROM settings WHERE username = ?";
            try {
                $result = $driver->query($sql, $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            if ($result->num_rows == 0) {
                $settings = new DBSettings($username, $language, $notifications, $TFA, $organizer);
                $settings->db_serialize($driver);
            } else {
                $sql = "UPDATE settings SET language = ?, notifications = ?, 2fa = ?, organizer = ? WHERE username = ?";
                try {
                    $driver->query($sql, $language, $notifications, $TFA, $organizer, $username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        public function getUsername() {
            return $this->username;
        }

        public function getEmail() {
            return $this->email;
        }
    }

    class DBSettings implements \DBTable {
        private $username;
        private $language;
        private $notifications;
        private $twofa;
        private $organizer;

        public function __construct($username = null, $language = null, $notifications = null, $twofa = null, $organizer = null) {
            $this->username = $username;
            $this->language = $language;
            $this->notifications = $notifications;
            $this->twofa = $twofa;
            $this->organizer = $organizer;
        }

        public function jsonSerialize() {
            return [
                "username" => $this->username,
                "language" => $this->language,
                "notifications" => $this->notifications,
                "twofa" => $this->twofa,
                "organizer" => $this->organizer
            ];
        }

        public function db_serialize(\DBDriver $driver) {
            $sql = "INSERT INTO settings (username, language, notifications, 2fa, organizer) 
                    VALUES (?, ?, ?, ?, ?)";
            try {
                $driver->query($sql, $this->username, $this->language, $this->notifications, $this->twofa, $this->organizer);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }

        }

        public function getNotifications() {
            return $this->notifications;
        }

        public function getTFA() {
            return $this->twofa;
        }
    }

    class UserUtility {
        public static function from_form(string $username, string $email, string $name, string $surname, 
                string $birth_date, string $profile_photo, string $background, string $bio, string $phone, string $password = "") {
            $user = new DBUser($username, $email, $name, $surname, $birth_date, $profile_photo, $background, $bio, $phone);
            if ($password != "") {
                $user->create_password($password);
            }
            return $user;
        }

        public static function from_db_with_username($driver, string $username) {
            $sql = "SELECT u.*, 
            (SELECT COUNT(*) FROM relationship WHERE followed = ?) as follower,
            (SELECT COUNT(*) FROM relationship WHERE follows = ?) as follows
            FROM user u 
            WHERE u.username = ?;";
            try {
                $result = $driver->query($sql, $username, $username, $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user = new DBUser($row["username"], $row["email"], $row["name"], $row["surname"], $row["birth_date"], 
                        $row["profile_photo"], $row["background"], $row["bio"], $row["phone"], $row["password"], 
                        $row["online"], $row["follows"], $row["follower"]
                );
            } else {
                return null;
            }
            return $user;
        }

        public static function from_db_with_username_like( \DBDriver $driver, string $username ): array{
            $sql = "SELECT * FROM user WHERE username LIKE ?";

            try {
                $result = $driver->query($sql, $username."%");
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            $users = array();
            if ($result->num_rows > 0) {
                for($i = 0; $i < $result->num_rows; $i++){
                    $row = $result->fetch_array();
                    $user = new DBUser($row["username"], $row["email"], $row["name"], $row["surname"], $row["birth_date"], 
                            $row["profile_photo"], $row["background"], $row["bio"], $row["phone"], $row["password"], $row["online"]);
                    array_push($users, $user);
                }
            }
            return $users;
        }

        public static function from_db_with_email(\DBDriver $driver, string $email) {
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
                        $row["profile_photo"], $row["background"], $row["bio"], $row["phone"], $row["password"], $row["online"]
                );
            } else {
                return null;
            }
            return $user;

        }

        public static function from_db_with_phone(\DBDriver $driver, string $phone) {
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
                        $row["profile_photo"], $row["background"], $row["bio"], $row["phone"], $row["password"], $row["online"]
                );
            } else {
                return null;
            }
            return $user;

        }

        public static function check_if_available(\DBDriver $driver, $username = "", $email = "", $phone = ""): void {
            if (!empty($username)) {
                $sql = "SELECT * FROM user WHERE username = ?";
                try {
                    $result = $driver->query($sql, $username);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                if ($result->num_rows > 0) {
                    throw new UsernameTaken("Username already taken");
                }
            } else if (!empty($email)) {
                $sql = "SELECT * FROM user WHERE email = ?";
                try {
                    $result = $driver->query($sql, $email);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
                if ($result->num_rows > 0) {
                    throw new EmailTaken("Email already taken");
                }
            } else if (!empty($phone)) {
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

        public static function insertFeedback($driver, $feedback) {
            $sql = "INSERT INTO feedback (feedback, date_time) VALUES (?, ?)";
            try {
                $driver->query($sql, $feedback, date("Y-m-d H:i:s", time()));
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
        }

        public static function all_infos_with_username($driver, $username) {
            $sql = "SELECT u.*, s.language, s.notifications, s.2fa, s.organizer FROM user u, settings s WHERE u.username = s.username AND u.username = ?";
            try {
                $result = $driver->query($sql, $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user = new DBUser($row["username"], $row["email"], $row["name"], $row["surname"], $row["birth_date"], 
                        $row["profile_photo"], $row["background"], $row["bio"], $row["phone"], $row["password"], $row["online"]
                );
                $settings = new DBSettings($row["username"], $row["language"], $row["notifications"], $row["2fa"], $row["organizer"]);
                return array($user, $settings);
            } else {
                return null;
            }
        }

        public static function retrieve_username_from_token($token): string {
            if (preg_match("/Bearer\s(\S+)/", $token, $matches) !== 1) {
                throw new \Exception("Invalid token");
            } else {
                $token = $matches[1];
                $decoded = JWT::decode($token, new Key(getenv("PL_JWTKEY"), 'HS256'));
                return ((array) $decoded)["username"];
            }
        }

        public static function retrieve_online_followed($driver, $username) {
            $sql = "SELECT u.* FROM user u, relationship r 
                    WHERE u.username = r.followed AND r.follows = ? AND u.online = 1";
            try {
                $result = $driver->query($sql, $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            $users = array();
            if ($result->num_rows > 0) {
                for ($i = 0; $i < $result->num_rows; $i++) {
                    $row = $result->fetch_array();
                    $user = new DBUser($row["username"], $row["email"], $row["name"], $row["surname"], $row["birth_date"], 
                            $row["profile_photo"], $row["background"], $row["bio"], $row["phone"], $row["password"], $row["online"]);
                    array_push($users, $user);
                }
            }
            return $users;
        }

        public static function toggle_follow($driver, $username, $toFollow) {
            $sql = "SELECT * FROM relationship WHERE follows = ? AND followed = ?";
            try {
                $result = $driver->query($sql, $username, $toFollow);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }

            if ($result->num_rows > 0) {
                $sql = "DELETE FROM relationship WHERE follows = ? AND followed = ?";
                try {
                    $driver->query($sql, $username, $toFollow);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            } else {
                $sql = "INSERT INTO relationship (follows, followed) VALUES (?, ?)";
                try {
                    $driver->query($sql, $username, $toFollow);
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        public static function check_if_follows($driver, $username, $toCheck) {
            $sql = "SELECT * FROM relationship WHERE follows = ? AND followed = ?";
            try {
                $result = $driver->query($sql, $username, $toCheck);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }

            if ($result->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }

        public static function retrieve_profile_picture($driver, $username) {
            $sql = "SELECT profile_photo FROM user WHERE username = ?";
            try {
                $result = $driver->query($sql, $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row["profile_photo"];
            } else {
                return null;
            }
        }

        public static function retrieve_settings($driver, $username) {
            $sql = "SELECT * FROM settings WHERE username = ?";
            try {
                $result = $driver->query($sql, $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $settings = new DBSettings($row["username"], $row["language"], $row["notifications"], $row["2fa"], $row["organizer"]);
            } else {
                return null;
            }
            return $settings;
        }

        public static function retrieve_followers($driver, $username) {
            $sql = "SELECT u.* FROM user u, relationship r 
                    WHERE u.username = r.follows
                    AND r.followed = ?";
            try {
                $result = $driver->query($sql, $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            $users = array();
            if ($result->num_rows > 0) {
                for ($i = 0; $i < $result->num_rows; $i++) {
                    $row = $result->fetch_array();
                    $user = new DBUser($row["username"], $row["email"], $row["name"], $row["surname"], $row["birth_date"], 
                            $row["profile_photo"], $row["background"], $row["bio"], $row["phone"], $row["password"], $row["online"]);
                    array_push($users, $user);
                }
            }
            return $users;
        }

        public static function retrieve_email($driver, $username) {
            $sql = "SELECT email FROM user WHERE username = ?";
            try {
                $result = $driver->query($sql, $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row["email"];
            } else {
                return null;
            }
        }

        public static function insert_tfa($driver, $token,  $username, $code) {
            $sql = "INSERT INTO tfa_code (token, username, code) VALUES (?, ?, ?)";
            try {
                $driver->query($sql, $token, $username, $code);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
        }

        public static function retrieve_tfa($driver, $token) {
            $sql = "SELECT code FROM tfa_code WHERE token = ?";
            try {
                $result = $driver->query($sql, $token);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row["code"];
            } else {
                return null;
            }
        }

        public static function check_tfa_success($driver, $token) {
            $sql = "SELECT code, username FROM tfa_code WHERE token = ?";
            try {
                $result = $driver->query($sql, $token);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($row["code"] == 0) {
                    return $row["username"];
                } else {
                    return null;
                }
            } else {
                return null;
            }
        }

        public static function delete_tfa($driver, $token) {
            $sql = "DELETE FROM tfa_code WHERE token = ?";
            try {
                $driver->query($sql, $token);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
        }

        public static function reset_tfa($driver, $token) {
            $sql = "UPDATE tfa_code SET code = 0 WHERE token = ?";
            try {
                $driver->query($sql, $token);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
        }

        public static function update_online($driver, $username, $online) {
            $sql = "UPDATE user SET online = ? WHERE username = ?";
            try {
                $driver->query($sql, $online, $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
        }

        public static function update_timestamps($driver, $username) {
            date_default_timezone_set('Europe/Rome');
            $sql = "UPDATE user SET last_seen = ? WHERE username = ?";
            try {
                $driver->query($sql, date("Y-m-d H:i:s", time()), $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            $sql = "SELECT u.username, u.last_seen 
                    FROM user u, relationship r 
                    WHERE u.username = r.followed 
                    AND r.follows = ? 
                    AND u.online = 1";
            try {
                $result = $driver->query($sql, $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            // returns all the users that have last_seen older than five minutes
            $users = array();
            if ($result->num_rows > 0) {
                for ($i = 0; $i < $result->num_rows; $i++) {
                    $row = $result->fetch_array();
                    $last_seen = strtotime($row["last_seen"]);
                    $now = strtotime(date("Y-m-d H:i:s"));
                    if ($now - $last_seen > 300) {
                        array_push($users, $row["username"]);
                    }
                }
            }
            return $users;
        }
    }
    class UsernameTaken extends \Exception { }
    class EmailTaken extends \Exception { }
    class PhoneTaken extends \Exception { }
}

?>