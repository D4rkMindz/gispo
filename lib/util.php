<?php
require_once 'config.php';
/**
 * Gets HTML encoded string
 *
 * @param $string
 * @return mixed|string
 */
function gh($string){
    $string = htmlentities($string);
    return $string;
}

/**
 * Writes HTML encoded string
 *
 * @param $string
 */
function wh($string){
    echo gh($string);
}

/**
 * Replaces umlaut into double characters
 *
 * @param $string
 * @return string
 */
function uml($string){
    $upas = Array(
        "ä" => "ae",
        "ü" => "ue",
        "ö" => "oe",
        "Ä" => "Ae",
        "Ü" => "Ue",
        "Ö" => "Oe",
        "è" => "e",
        "é" => "e",
        "ô" => "o",
        "ë" => "e"

    );
    return strtr($string, $upas);
}
function getSqlTimeStamp(){
    $now = new DateTime('now', new DateTimeZone('Europe/Paris'));
    $now = $now->format(DateTime::ISO8601);
    return $now;
}
function getTimeStamp(){
    return date("Y-m-d_H-m-s");
}
function win1252($string) {
    $charset =  mb_detect_encoding(
        $string,
        "UTF-8, ISO-8859-1, ISO-8859-15",
        true
    );

    $string =  mb_convert_encoding($string, "Windows-1252", $charset);
    return $string;
}

/**
 * Gets the twitter bootstrap context class
 *
 * It is used t highlight table rows according to their status.
 * It differs between check_in and check_out use case
 * -----------
 * Check_in:
 *     if unchecked   : ''
 *     if checked_in  : success
 *     if checked_out : warning
 *     if error       : danger
 * Check_out:
 *     if unchecked   : danger
 *     if checked_in  : ''
 *     if checked_out : success
 *     if error       : danger
 *
 * @param $status
 * @param $action
 * @return string
 */
function getContext($status, $action){
    $context = '';
    if($action == 'check_in') {
        if ($status == 'present') {
            $context = 'success';
        } else {
            $context = '';
        }
    }
    elseif ($action == 'check_out') {
        if ($status == 'present') {

            $context = '';
        } else {
            $context = 'success';
        }
    }
    return $context;
}
function getButton($id,$action,$status) {
    $href = sprintf('logging.php?id=%s&logging=%s',$id,$action);
    $button = sprintf('<a class="btn btn-lg btn-danger" href="%s">Fehler Korrigieren</a>',$href);
    if($action == 'check_in') {
        if ($status == 'present') {
            $button = '<button type="button" class="btn btn-default btn-lg" disabled="disabled">Anwesend</button>';
        } else {
            $button = sprintf('<a class="btn btn-lg btn-success" href="%s">Erfassen</a>',$href);
        }
    }
    elseif ($action == 'check_out') {
        if ($status == 'present') {

            $button = sprintf('<a class="btn btn-lg btn-success" href="%s">Erfassen</a>', $href);
        } else {
            $button = '<button type="button" class="btn btn-default btn-lg" disabled="disabled">Abwesend</button>';
        }
    }

    return $button;
}
function getPicture($id = null) {
    $imgPath = 'assets/img/Students/';
    $src = $imgPath.$id.'.png';
    $picture = '<img src="assets/img/preview.png"  width="150">';
    if(file_exists($src)) {
        $picture = sprintf('<img src="%s"  width="150">', $src);
    }
    return $picture;
}