<?php 
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    


    header('Content-Type: application/json');
    global $driver;
    /*

    */

    // Get datas from the request
    $request = json_decode(file_get_contents('php://input'), true);
    $username = $request["username"];
    $email = $request["email"];
    $name = $request["name"];
    $surname = $request["surname"];
    $birth_date = $request["birth_date"];
    $photo = $request["photo"];
    $bio = $request["bio"];
    $phone = $request["phone"];
    $password = $request["password"];
    // check if the username, email and phone are available
    try {
        User\UserUtility::check_if_available($driver, $username, $email, $phone);

    } catch (User\UsernameTaken $e) {
        http_response_code(400);
        echo json_encode(array("error" => "Username already taken"));
        exit();
    } catch (User\EmailTaken $e) {
        http_response_code(400);
        echo json_encode(array("error" => "Email already taken"));
        exit();
    } catch (User\PhoneTaken $e) {
        http_response_code(400);
        echo json_encode(array("error" => "Phone already taken"));
        exit();
    }
    // create the user
    try {
        $user = User\UserUtility::from_form($username, $email, $name, $surname, $birth_date, $photo, $bio, $phone, $password);
        $user->db_serialize($driver);
    } catch (\Exception $e) {
        http_response_code(400);
        echo json_encode(array("error" => "Error while creating user: " . $e->getMessage()));
        exit();
    }
    echo json_encode(array("message" => "User created successfully"));
    $driver->close_connection();




?>