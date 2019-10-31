<?php

function getDefaultUrlStr($url) {
    if (!stristr($url, 'https://')) {
        $url = 'https://' . $url;
    }
    return $url;
}