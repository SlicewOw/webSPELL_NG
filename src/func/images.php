<?php

function getUserImage($user_id, $category) {

    $default_image = 'no' . $category . '.gif';

    if (!is_int($user_id) || ($user_id < 1)) {
        return $default_image;
    }

    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT
                    `" . $category . "`
                FROM `" . PREFIX . "user`
                WHERE `userID` = " . $user_id
        )
    );

    return (!empty($ds[$category])) ? $ds[$category] : $default_image;

}

/**
 * Delete images
 */
function deleteAllImagesByFilePath($filepath, $file_name_without_extension) {

    $filepath = str_replace(
        array('../', './'),
        array('', ''),
        $filepath
    );

    $imageTypeArray = array(
        '.gif',
        '.jpg',
        '.png'
    );

    foreach ($imageTypeArray as $image_extension) {
        $complete_file_path = __DIR__ . '/../../' . $filepath . $file_name_without_extension . $image_extension;
        if (file_exists($complete_file_path)) {
            @unlink($complete_file_path);
        }
    }

}
