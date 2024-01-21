<?php

namespace User {
    require("dbtable.php");
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    class DBUser implements \DBTable
    {
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
        // insert into database
        public function db_serialize(\DBDriver $driver) {
            if ($this->password == null) {
                throw new \Exception("Password not set");
            }
            $sql = "INSERT INTO user (username, email, name, surname, birth_date, profile_photo, background, bio, phone, password, online) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            try {
                $driver->query(
                    $sql,
                    $this->username,
                    $this->email,
                    $this->name,
                    $this->surname,
                    date($this->birth_date),
                    $this->profile_photo,
                    $this->background,
                    $this->bio,
                    $this->phone,
                    $this->password,
                    $this->online
                );
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }

        }

        public function check_password(string $password) {
            return password_verify($password, $this->password);
        }

        public function getUsername() {
            return $this->username;
        }

        public function getEmail() {
            return $this->email;
        }
    }

    class DBSettings implements \DBTable
    {
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
    }

    class UserUtility
    {
        public static function from_form(string $username, string $email, string $name, string $surname, string $birth_date, string $profile_photo, string $background, string $bio, string $phone, string $password = "")
        {
            $user = new DBUser($username, $email, $name, $surname, $birth_date, $profile_photo, $background, $bio, $phone);
            if ($password != "") {
                $user->create_password($password);
            }
            return $user;
        }

        public static function from_db_with_username($driver, string $username)
        {
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
                $user = new DBUser(
                    $row["username"],
                    $row["email"],
                    $row["name"],
                    $row["surname"],
                    $row["birth_date"],
                    $row["profile_photo"],
                    $row["background"],
                    $row["bio"],
                    $row["phone"],
                    $row["password"],
                    $row["online"],
                    $row["follower"],
                    $row["follows"]
                );
            } else {
                return null;
            }
            return $user;
        }

        public static function from_db_with_email(\DBDriver $driver, string $email)
        {
            $user = null;
            $sql = "SELECT * FROM user WHERE email = ?";
            try {
                $result = $driver->query($sql, $email);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user = new DBUser(
                    $row["username"],
                    $row["email"],
                    $row["name"],
                    $row["surname"],
                    $row["birth_date"],
                    $row["profile_photo"],
                    $row["background"],
                    $row["bio"],
                    $row["phone"],
                    $row["password"],
                    $row["online"]
                );
            } else {
                return null;
            }
            return $user;

        }

        public static function from_db_with_phone(\DBDriver $driver, string $phone)
        {
            $user = null;
            $sql = "SELECT * FROM user WHERE phone = ?";
            try {
                $result = $driver->query($sql, $phone);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user = new DBUser(
                    $row["username"],
                    $row["email"],
                    $row["name"],
                    $row["surname"],
                    $row["birth_date"],
                    $row["profile_photo"],
                    $row["background"],
                    $row["bio"],
                    $row["phone"],
                    $row["password"],
                    $row["online"]
                );
            } else {
                return null;
            }
            return $user;

        }

        public static function check_if_available(\DBDriver $driver, $username = "", $email = "", $phone = ""): void
        {
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

        public static function retrieve_username_from_token($token): string
        {
            if (preg_match("/Bearer\s(\S+)/", $token, $matches) !== 1) {
                throw new \Exception("Invalid token");
            } else {
                $token = $matches[1];
                $decoded = JWT::decode($token, new Key(getenv("PL_JWTKEY"), 'HS256'));
                return ((array) $decoded)["username"];
            }
        }

        public static function retrieve_online_followed($driver, $username)
        {
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
                    $user = new DBUser(
                        $row["username"],
                        $row["email"],
                        $row["name"],
                        $row["surname"],
                        $row["birth_date"],
                        $row["profile_photo"],
                        $row["background"],
                        $row["bio"],
                        $row["phone"],
                        $row["password"],
                        $row["online"]
                    );
                    array_push($users, $user);
                }
            }
            return $users;
        }

        public static function retrieve_profile_picture($driver, $username)
        {
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

        public static function retrieve_settings($driver, $username)
        {
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
    }
    class UsernameTaken extends \Exception
    {
    }
    class EmailTaken extends \Exception
    {
    }
    class PhoneTaken extends \Exception
    {
    }
}


?>