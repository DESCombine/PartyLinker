<?php

function img_handler($file) {
    $max_image_size = 31457280;
    $allowed_image_types = array('image/png', 'image/jpeg', 'image/gif');
    $allowed_image_extensions = array('png', 'jpg', 'jpeg', 'gif');
    list($width, $height, $type, $attr) = getimagesize($file['tmp_name']);
    if($file['size'] > $max_image_size) {
        throw new InvalidImageSizeException('Image size is too large.');
    }
    if(!in_array($file['type'], $allowed_image_types)) {
        throw new InvalidImageTypeException('Image type is not allowed.');
    }
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if(!in_array($file_extension, $allowed_image_extensions)) {
        throw new InvalidImageTypeException('Image extension is not allowed.');
    }
    // move the image to the folder /static/img/uploads with a unique name
    $new_file_name = uniqid() . '.' . $file_extension;
    $new_file_path = getenv("PL_ROOTDIRECTORY").'static/img/uploads/' . $new_file_name;
    if(!move_uploaded_file($file['tmp_name'], $new_file_path)) {
        throw new Exception('Failed to move uploaded file.');
    }
    return $new_file_name;

}

class InvalidImageSizeException extends Exception {
    public function __construct($message = null, $code = 0) {
        parent::__construct($message, $code);
    }
}

class InvalidImageTypeException extends Exception {
    public function __construct($message = null, $code = 0) {
        parent::__construct($message, $code);
    }
}
?>