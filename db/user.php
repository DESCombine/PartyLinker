<?php 
require("dbtable.php");
class User implements DBTable {
    private $username;
    private $email;
    private $name;
    private $surname;
    private $birth_date;
    private $photo;
    private $bio;
    private $phone;
    private $password;
    // TODO: add relationship to other user
    public function __construct() {
        $this->password = null;
    }
    public static function from_data( string $username, string $email, string $name, string $surname, string $birth_date, string $photo, string $bio, string $phone, string $password = "" ){
        $user = new self();
        $user->username = $username;
        $user->email = $email;
        $user->name = $name;
        $user->surname = $surname;
        $user->birth_date = $birth_date;
        $user->photo = $photo;
        $user->bio = $bio;
        $user->phone = $phone;
        if( $password != "" ){
            $user->create_password($password);
        }
        return $user;
    }

    public static function from_db_with_username( DBDriver $driver, string $username ){
        $user = new self();
        $user->username = $username;
        $sql = "SELECT * FROM user WHERE username = ?";

        try {
            $result = $driver->query($sql, $username);
        } catch (Exception $e) {
            throw new Exception("Error while querying the database: " . $e->getMessage());
        }
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user->email = $row["email"];
            $user->name = $row["name"];
            $user->surname = $row["surname"];
            $user->birth_date = $row["birth_date"];
            $user->photo = $row["photo"];
            $user->bio = $row["bio"];
            $user->phone = $row["phone"];
        } else {
            return null;
        }
        return $user;
    }

    public static function from_db_with_email(DBDriver $driver, string $email ){
        $user = new self();
        $user->email = $email;
        $sql = "SELECT * FROM user WHERE email = ?";
        try {
            $result = $driver->query($sql, $email);
        } catch (Exception $e) {
            throw new Exception("Error while querying the database: " . $e->getMessage());
        }
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user->username = $row["username"];
            $user->name = $row["name"];
            $user->surname = $row["surname"];
            $user->birth_date = $row["birth_date"];
            $user->photo = $row["photo"];
            $user->bio = $row["bio"];
            $user->phone = $row["phone"];
        } else {
            return null;
        }
        return $user;

    }

    public static function from_db_with_phone( DBDriver $driver, string $phone ){
        $user = new self();
        $user->phone = $phone;
        $sql = "SELECT * FROM user WHERE phone = ?";
        try {
            $result = $driver->query($sql, $phone);
        } catch (Exception $e) {
            throw new Exception("Error while querying the database: " . $e->getMessage());
        }
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user->username = $row["username"];
            $user->email = $row["email"];
            $user->name = $row["name"];
            $user->surname = $row["surname"];
            $user->birth_date = $row["birth_date"];
            $user->photo = $row["photo"];
            $user->bio = $row["bio"];
        } else {
            return null;
        }
        return $user;

    }

    public function json_serialize(): string {
        return json_encode(array(
            "username" => $this->username,
            "email" => $this->email,
            "name" => $this->name,
            "surname" => $this->surname,
            "birth_date" => $this->birth_date,
            "photo" => $this->photo,
            "bio" => $this->bio,
            "phone" => $this->phone
        ));
    }
    public function create_password(string $password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
    // insert into database
    public function db_serialize(DBDriver $driver) {
        if ($this->password == null) {
            throw new Exception("Password not set");
        }
        $sql = "INSERT INTO user (username, email, name, surname, birth_date, photo, bio, phone, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        try {
            $driver->query($sql, $this->username, $this->email, $this->name, $this->surname, $this->birth_date, $this->photo, $this->bio, $this->phone, $this->password);
        } catch (Exception $e) {
            throw new Exception("Error while querying the database: " . $e->getMessage());
        }

    }
}

?>