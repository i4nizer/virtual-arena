<?php


// Util -> str params
function getTimeStatus($startDtStr, $endDtStr, $timezone) {
    // Convert to time
    $startDt = new DateTime($startDtStr, new DateTimeZone($timezone));
    $endDt = new DateTime($endDtStr, new DateTimeZone($timezone));
    $currentDt = new DateTime('now', new DateTimeZone($timezone));

    if($currentDt < $startDt) return "Preparation";
    else if($currentDt >= $endDt) return "Ended";
    else return "Ongoing";
}