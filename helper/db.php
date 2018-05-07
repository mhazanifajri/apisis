<?php
require_once(realpath(dirname(__FILE__) . '/..') . '/config.php');

function queryGet($query){
    $db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $result = $db->query($query);

    if($result != null){
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    return null;
}

function queryExecute($query){
    $db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $result = $db->query($query);

    if($result && $db->affected_rows > 0){
        return true;
    }

    return false;
}
