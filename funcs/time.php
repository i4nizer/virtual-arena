<?php


// Util -> str params
function getTimeStatus($startDtStr, $endDtStr, $timezone) {
    // Convert to time
    $startDt = new DateTime($startDtStr, new DateTimeZone($timezone));
    $endDt = new DateTime($endDtStr, new DateTimeZone($timezone));
    $currentDt = new DateTime('now', new DateTimeZone($timezone));

    if($currentDt->diff($startDt)->invert) return "Ended";        // After
    else if($currentDt->diff($endDt)->invert) return "Ongoing";   // During
    else return "Preparation";                                    // Before
}