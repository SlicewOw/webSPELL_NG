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
