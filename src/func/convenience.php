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