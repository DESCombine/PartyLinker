<?php
    require_once(getenv("PL_ROOTDIRECTORY"). "php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY"). "php/img_upload_handler.php");

    header("Content-Type: application/json");

    if(!is_uploaded_file($_FILES["img"]["tmp_name"])) {
        echo json_encode(array("error" => "No file uploaded."));
        exit();
    }

    try {
        echo json_encode(array("filename" => img_handler($_FILES["img"])));
    } catch(InvalidImageSizeException $e) {
        echo json_encode(array("error" => $e->getMessage()));
    } catch(InvalidImageTypeException $e) {
        echo json_encode(array("error" => $e->getMessage()));
    } catch(Exception $e) {
        echo json_encode(array("error" => $e->getMessage()));
    }



?>