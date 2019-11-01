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

function shortenText($text, $text_length=255) {

    if (mb_strlen($text) > $text_length) {
        $string = wordwrap($text, $text_length);
        $string = substr($string, 0, strpos($string, "\n")) . '...';
    } else {
        $string = $text;
    }

    return $string;

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