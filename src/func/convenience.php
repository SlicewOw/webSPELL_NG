<?php

function getDefaultUrlStr($url) {
    if (!stristr($url, 'https://')) {
        $url = 'https://' . $url;
    }
    return $url;
}

function getStartValue($page, $max) {

    if (!is_integer($page) || ($page < 1)) {
        $page = 1;
    }

    if ($page < 2) {
        return 0;
    } else {
        return ($page * $max) - $max;
    }

}

function getAction() {
    return isset($_GET['action']) ? getinput($_GET['action']) : '';
}

function getPage() {

    if (isset($_GET['page'])) {

        $page = (int)$_GET['page'];

        if ($page < 1) {
            $page = 1;
        }

    } else {
        $page = 1;
    }

    return $page;

}

function getCountOfPages($gesamt, $max) {
    $pages = 1;
    for ($n=$max; $n<=$gesamt; $n+=$max) {
        if ($gesamt>$n) {
            $pages++;
        }
    }
    return $pages;
}

function getSortOrderType($default_sort_order="ASC") {

    $type = $default_sort_order;

    if (isset($_GET[ 'type' ]) && (($_GET[ 'type' ] == 'ASC') || ($_GET[ 'type' ] == 'DESC'))) {
        $type = $_GET[ 'type' ];
    }

    return $type;

}

function getSortOrderValue($default_sort_value, $allowed_sort_value_array) {

    $value = $default_sort_value;

    if (isset($_GET[ 'sort' ])) {

        $tmp_value = $_GET[ 'sort' ];
        if (in_array($tmp_value, $allowed_sort_value_array)) {
            $value = $tmp_value;
        }

    }

    return $value;

}

function shortenText($text, $text_length=255) {

    if (mb_strlen($text) > $text_length) {
        $string = wordwrap($text, $text_length);
        $string = substr($string, 0, strpos($string, "\n")) . '...';
    } else {
        $string = $text;
    }

    return $string;

}

function getLanguagesAsOptions($selected_language='') {

    $filepath = __DIR__ . '/../../languages/';

    $query = safe_query("SELECT lang, language FROM " . PREFIX . "news_languages");

    $mysql_langs = array();
    while ($sql_lang = mysqli_fetch_assoc($query)) {
        $mysql_langs[ $sql_lang[ 'lang' ] ] = $sql_lang[ 'language' ];
    }

    $langs = array();
    if ($dh = opendir($filepath)) {
        while ($file = mb_substr(readdir($dh), 0, 2)) {
            if ($file != "." && $file != ".." && is_dir($filepath . $file)) {
                if (isset($mysql_langs[ $file ])) {
                    $name = $mysql_langs[ $file ];
                    $name = ucfirst($name);
                    $langs[ $name ] = $file;
                } else {
                    $langs[ $file ] = $file;
                }
            }
        }
        closedir($dh);
    }

    ksort($langs, SORT_NATURAL);

    $langdirs = '';
    foreach ($langs as $lang => $flag) {
        $langdirs .= '<option value="' . $flag . '">' . $lang . '</option>';
    }

    if (!empty($selected_language)) {
        $langdirs = str_replace(
            'value="' . $selected_language . '"',
            'value="' . $selected_language . '" selected="selected"',
            $langdirs
        );
    }

    return $langdirs;

}

function uploadFile($post_name, $filename, $filepath, $fail_if_no_file=true, $is_image_upload=true, $mime_types=array('image/jpeg', 'image/png', 'image/gif')) {

    try {

        global $_language;

        $upload = new \webspell\HttpUpload($post_name);

        if (!$upload->hasFile()) {
            if ($fail_if_no_file) {
                return false;
            } else {
                throw new \InvalidArgumentException($_language->module['no_image']);
            }
        } else if ($upload->hasError() !== false) {
            throw new \InvalidArgumentException($upload->translateError());
        } else if (!$upload->supportedMimeType($mime_types)) {
            throw new \InvalidArgumentException($_language->module['unsupported_image_type']);
        }

        if ($is_image_upload) {

            $imageInformation = getimagesize($upload->getTempFile());
            if (!is_array($imageInformation)) {
                throw new \InvalidArgumentException($_language->module['broken_image']);
            }

            switch ($imageInformation[2]) {
                case 1:
                    $endung = '.gif';
                    break;
                case 3:
                    $endung = '.png';
                    break;
                default:
                    $endung = '.jpg';
                    break;
            }

            $file = $filename . $endung;

        } else {
            $file = $upload->getFileName();
        }

        if (!$upload->saveAs($filepath . $file, true)) {
            throw new \InvalidArgumentException($_language->module['cannot_save_upload']);
        }

        @chmod($filepath . $file, 755);

        return $file;

    } catch (Exception $e) {
        return false;
    }

}

/**
 * Sort stuff in database
 */
function sortContentByParameters($captcha_hash, $sort_array, $table, $unique_identifier) {

    global $_language;

    $CAPCLASS = new \webspell\Captcha;
    if (!$CAPCLASS->checkCaptcha(0, $captcha_hash)) {
        throw new \InvalidArgumentException($_language->module[ 'transaction_invalid' ]);
    }

    if (!is_array($sort_array) || count($sort_array) < 1) {
        throw new \InvalidArgumentException($_language->module[ 'sort_array_empty' ]);
    }

    foreach ($sort_array as $sortString) {

        $sortKeyValueArray = explode("-", $sortString);

        if (count($sortKeyValueArray) != 2) {
            return;
        }

        $sort_value = (int)$sortKeyValueArray[1];
        $sort_id = $sortKeyValueArray[0];

        safe_query(
            "UPDATE `" . PREFIX . $table . "`
                SET `sort` = " . $sort_value . "
                WHERE `" . $unique_identifier . "` = '" . $sort_id . "'"
        );

    }

}