<?php

namespace User {
    require("dbtable.php");
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    /**
     * This class is used to wrap the user table in the database
     * @param string $username the username of the user
     * @param string $email the email of the user
     * @param string $name the name of the user
     * @param string $surname the surname of the user
     * @param string $birth_date the birth date of the user
     * @param string $profile_photo the profile photo of the user
     * @param string $background the background photo of the user
     * @param string $bio the bio of the user
     * @param string $phone the phone number of the user
     * @param string $password the password of the user (hashed)
     * @param int $online if the user is online
     * @param int $follows the number of users that the user follows
     * @param int $followers the number of users that follow the user
     * @method void __construct(string $username, string $email, string $name, string $surname, string $birth_date, string $profile_photo, string $background, string $bio, string $phone, string $password, int $online, int $follows, int $followers)
     * @method array jsonSerialize()
     * @method void create_password(string $password)
     * @method string get_password()
     * @method void db_serialize(\DBDriver $driver)
     * @method void check_password(string $password)
     * @method void update_infos(\DBDriver $driver, string $name, string $surname, string $birth_date, string $email, string $phone, string $username, string $password, int $organizer, string $profilePhoto, string $bannerPhoto, string $bio, string $language, int $notifications, int $TFA)
     */
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


        /**
         * Base constructor
         * @param string $username the username of the user
         * @param string $email the email of the user
         * @param string $name the name of the user
         * @param string $surname the surname of the user
         * @param string $birth_date the birth date of the user
         * @param string $profile_photo the profile photo of the user
         * @param string $background the background photo of the user
         * @param string $bio the bio of the user
         * @param string $phone the phone number of the user
         * @param string $password the password of the user (hashed)
         * @param int $online if the user is online
         * @param int $follows the number of users that the user follows
         * @param int $followers the number of users that follow the user
         * @return void
         */
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
        /**
         * This function is used to serialize the object to a json format
         * @return array the serialized object
         */
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
        /**
         * This function is used to hash the password
         * @param string $password the password to hash
         * @return void
         */
        public function create_password(string $password) {
            $this->password = password_hash($password, PASSWORD_DEFAULT);
        }

        public function get_password() {
            return $this->password;
        }
        /**
         * This function is used to serialize the object to a database format
         * It inserts the object into the user table
         * It also checks if the password is set
         * @param \DBDriver $driver the database driver
         * @return void
         * @throws \Exception if the password is not set
         * @throws \Exception if there is an error while querying the database
         */
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

        /**
         * This function is used to check if the password is correct
         * @param string $password the password to check
         * @return bool if the password is correct
         */
        public function check_password(string $password) {
            return password_verify($password, $this->password);
        }

        /**
         * This function is used to update the user infos
         * It updates the user table and the settings table
         * @param \DBDriver $driver the database driver
         * @param string $name the name of the user
         * @param string $surname the surname of the user
         * @param string $birth_date the birth date of the user
         * @param string $email the email of the user
         * @param string $phone the phone number of the user
         * @param string $username the username of the user
         * @param string $password the password of the user
         * @param int $organizer if the user is an organizer
         * @param string $profilePhoto the profile photo of the user
         * @param string $bannerPhoto the banner photo of the user
         * @param string $bio the bio of the user
         * @param string $language the language of the user
         * @param int $notifications if the user wants to receive notifications
         * @param int $TFA if the user wants to use 2fa
         * @return void
         * @throws \Exception if there is an error while querying the database
         * 
         */
        public function update_infos(\DBDriver $driver, string $name, string $surname, string $birth_date, string $email, string $phone, string $username, string $password, int $organizer, 
                string $profilePhoto, string $bannerPhoto, string $bio, string $language, int $notifications, int $TFA) {
            // if profilephoto or bannerphoto are null, set them to the current ones
            $user = UserUtility::from_db_with_username($driver, $username);
            if ($profilePhoto == "") {
                $profilePhoto = $user->profile_photo;
            }
            if ($bannerPhoto == "") {
                $bannerPhoto = $user->background;
            }
            if ($password == null) {
                $password = $user->password;
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


    /**
     * This class is used to wrap the settings table in the database
     * @param string $username the username of the user
     * @param string $language the language of the user
     * @param string $notifications if the user wants to receive notifications
     * @param string $twofa if the user wants to use 2fa
     * @param string $organizer if the user is an organizer
     * @method void __construct(string $username, string $language, string $notifications, string $twofa, string $organizer)
     * @method array jsonSerialize()
     * @method void db_serialize(\DBDriver $driver)
     * @method string getNotifications()
     * @method string getTFA()
     * @method string getLanguage()
     */
    class DBSettings implements \DBTable {
        private $username;
        private $language;
        private $notifications;
        private $twofa;
        private $organizer;

        /**
         * Base constructor
         * @param string $username the username of the user
         * @param string $language the language of the user
         * @param string $notifications if the user wants to receive notifications
         * @param string $twofa if the user wants to use 2fa
         * @param string $organizer if the user is an organizer
         * @return void
         */
        public function __construct($username = null, $language = "en", $notifications = "0", $twofa = "0", $organizer = "0") {
            $this->username = $username;
            $this->language = $language;
            $this->notifications = $notifications;
            $this->twofa = $twofa;
            $this->organizer = $organizer;
        }

        /**
         * This function is used to serialize the object to a json format
         * @return array the serialized object
         */
        public function jsonSerialize() {
            return [
                "username" => $this->username,
                "language" => $this->language,
                "notifications" => $this->notifications,
                "twofa" => $this->twofa,
                "organizer" => $this->organizer
            ];
        }

        /**
         * This function is used to serialize the object to a database format
         * It inserts the object into the settings table
         * @param \DBDriver $driver the database driver
         * @return void
         * @throws \Exception if there is an error while querying the database
         */
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

        public function getLanguage() {
            return $this->language;
        }
    }

    /**
     * This class is used to wrap the relationship table in the database
     * @method DBUser from_form(string $username, string $email, string $name, string $surname, string $birth_date, string $profile_photo, string $background, string $bio, string $phone, string $password = "")
     * @method DBUser from_db_with_username($driver, string $username)
     * @method array from_db_with_username_like( \DBDriver $driver, string $username )
     * @method void check_if_available(\DBDriver $driver, $username = "", $email = "", $phone = "")
     * @method void insertFeedback($driver, $feedback)
     * @method array all_infos_with_username($driver, $username)
     * @method string retrieve_username_from_token($token)
     * @method array retrieve_online_followed($driver, $username)
     * @method bool toggle_follow($driver, $username, $toFollow)
     * @method bool check_if_follows($driver, $username, $toCheck)
     * @method string retrieve_profile_picture($driver, $username)
     * @method DBSettings retrieve_settings($driver, $username)
     * @method array retrieve_followers($driver, $username)
     * @method string retrieve_email($driver, $username)
     * @method void insert_tfa($driver, $token,  $username, $code)
     * @method string retrieve_tfa($driver, $token)
     * @method string check_tfa_success($driver, $token)
     * @method void delete_tfa($driver, $token)
     * @method void reset_tfa($driver, $username)
     * @method void update_online($driver, $username, $online)
     * @method void update_timestamp($driver, $username)
     * 
     */
    class UserUtility {
        /**
         * This function is used to create a user from a form
         * It creates a user from the form data
         * @param string $username the username of the user
         * @param string $email the email of the user
         * @param string $name the name of the user
         * @param string $surname the surname of the user
         * @param string $birth_date the birth date of the user
         * @param string $profile_photo the profile photo of the user
         * @param string $background the background photo of the user
         * @param string $bio the bio of the user
         * @param string $phone the phone number of the user
         * @param string $password the password of the user
         * @return DBUser the user created from the form
         * 
         */
        public static function from_form(string $username, string $email, string $name, string $surname, 
                string $birth_date, string $profile_photo, string $background, string $bio, string $phone, string $password = "") {
            $user = new DBUser($username, $email, $name, $surname, $birth_date, $profile_photo, $background, $bio, $phone);
            if ($password != "") {
                $user->create_password($password);
            }
            return $user;
        }

        /**
         * This function retrieve a user from the database with the username
         * @param \DBDriver $driver the database driver
         * @param string $username the username of the user
         * @return DBUser|null the user retrieved from the database
         * @throws \Exception if there is an error while querying the database
         */
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

        /**
         * This function retrieve a user from the database with the username that is like the one passed
         * @param \DBDriver $driver the database driver
         * @param string $username the username of the user
         * @return array the users retrieved from the database
         * @throws \Exception if there is an error while querying the database
         */
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

        /**
         * This fucntion check if the username, email or phone are available
         * @param \DBDriver $driver the database driver
         * @param string $username the username of the user
         * @param string $email the email of the user
         * @param string $phone the phone number of the user
         * @return void
         * @throws \Exception if there is an error while querying the database
         * @throws UsernameTaken if the username is already taken
         * @throws EmailTaken if the email is already taken
         * @throws PhoneTaken if the phone is already taken
         */
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

        /**
         * This function set a feedback in the database
         * @param \DBDriver $driver the database driver
         * @param string $feedback the feedback to insert
         * @return void
         * @throws \Exception if there is an error while querying the database
         */
        public static function insertFeedback($driver, $feedback) {
            $sql = "INSERT INTO feedback (feedback, date_time) VALUES (?, ?)";
            try {
                $driver->query($sql, $feedback, date("Y-m-d H:i:s", time()));
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
        }

        /**
         * This function retrieve a user and its settings from the database with the username
         * @param \DBDriver $driver the database driver
         * @param string $username the username of the user
         * @return array|null the user and its settings retrieved from the database
         */
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

        /**
         * This function get the username from the token
         * @param string $token the token to decode
         * @return string the username retrieved from the token
         * @throws \Exception if the token is invalid
         */
        public static function retrieve_username_from_token($token): string {
            if (preg_match("/Bearer\s(\S+)/", $token, $matches) !== 1) {
                throw new \Exception("Invalid token");
            } else {
                $token = $matches[1];
                $decoded = JWT::decode($token, new Key(getenv("PL_JWTKEY"), 'HS256'));
                return ((array) $decoded)["username"];
            }
        }

        /**
         * This function retrieve all the users that are currently online and the user follows
         * @param \DBDriver $driver the database driver
         * @param string $username the username of the user
         * @return array the users retrieved from the database
         * @throws \Exception if there is an error while querying the database
         */
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

        /**
         * This function toggle the follow of a user
         * @param \DBDriver $driver the database driver
         * @param string $username the username of the user
         * @param string $toFollow the username of the user to follow
         * @return bool if the user follows the other user
         * @throws \Exception if there is an error while querying the database
         */
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
                    return false;
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            } else {
                $sql = "INSERT INTO relationship (follows, followed) VALUES (?, ?)";
                try {
                    $driver->query($sql, $username, $toFollow);
                    return true;
                } catch (\Exception $e) {
                    throw new \Exception("Error while querying the database: " . $e->getMessage());
                }
            }
        }

        /**
         * This function check if the user follows another user
         * @param \DBDriver $driver the database driver
         * @param string $username the username of the user
         * @param string $toCheck the username of the user to check
         * @return bool if the user follows the other user
         * @throws \Exception if there is an error while querying the database
         */
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

        /**
         * This function retrieve the profile picture of a user
         * @param \DBDriver $driver the database driver
         * @param string $username the username of the user
         * @return string the profile picture of the user
         * @throws \Exception if there is an error while querying the database
         */
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

        /**
         * This function retrieve the settings of a user
         * @param \DBDriver $driver the database driver
         * @param string $username the username of the user
         * @return DBSettings|null the settings retrieved from the database
         * @throws \Exception if there is an error while querying the database
         */
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

        /**
         * This function retrieve the followers of a user
         * @param \DBDriver $driver the database driver
         * @param string $username the username of the user
         * @return array the followers retrieved from the database
         * @throws \Exception if there is an error while querying the database
         */
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

        /**
         * This function retrieve the email of a user
         * @param \DBDriver $driver the database driver
         * @param string $username the username of the user
         */
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

        /**
         * Insert a new 2fa token and code in the db
         * @param \DBDriver $driver the database driver
         * @param string $token the temporary token used for the 2fa
         * @param string $username the username of the user
         * @param int $code the code to insert
         * @return void
         * @throws \Exception if there is an error while querying the database
         */
        public static function insert_tfa($driver, $token,  $username, $code) {
            $sql = "INSERT INTO tfa_code (token, username, code) VALUES (?, ?, ?)";
            try {
                $driver->query($sql, $token, $username, $code);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
        }

        /**
         * Retrieve the 2fa code from the db
         * @param \DBDriver $driver the database driver
         * @param string $token the temporary token used for the 2fa
         * @return string|null the code retrieved from the database
         * @throws \Exception if there is an error while querying the database
         */
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

        /**
         * Check if the 2fa code is correct (equals to 0)
         * @param \DBDriver $driver the database driver
         * @param string $token the temporary token used for the 2fa
         * @return string|null the username of the user if the code is correct
         * @throws \Exception if there is an error while querying the database
         */
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

        /**
         * Delete the 2fa token and code from the db
         * @param \DBDriver $driver the database driver
         * @param string $token the temporary token used for the 2fa
         * @return void
         * @throws \Exception if there is an error while querying the database
         */
        public static function delete_tfa($driver, $token) {
            $sql = "DELETE FROM tfa_code WHERE token = ?";
            try {
                $driver->query($sql, $token);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
        }

        /**
         * Reset the 2fa code to 0
         * @param \DBDriver $driver the database driver
         * @param string $token the temporary token used for the 2fa
         * @return void
         * @throws \Exception if there is an error while querying the database
         */
        public static function reset_tfa($driver, $token) {
            $sql = "UPDATE tfa_code SET code = 0 WHERE token = ?";
            try {
                $driver->query($sql, $token);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
        }

        /**
         * Update the online status of a user
         * @param \DBDriver $driver the database driver
         * @param string $username the username of the user
         * @param int $online if the user is online
         * @return void
         * @throws \Exception if there is an error while querying the database
         */
        public static function update_online($driver, $username, $online) {
            $sql = "UPDATE user SET online = ? WHERE username = ?";
            try {
                $driver->query($sql, $online, $username);
            } catch (\Exception $e) {
                throw new \Exception("Error while querying the database: " . $e->getMessage());
            }
        }

        /**
         * Update the last seen of a user
         * @param \DBDriver $driver the database driver
         * @param string $username the username of the user
         * @return array the users that have last_seen older than five minutes
         * @throws \Exception if there is an error while querying the database
         */
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