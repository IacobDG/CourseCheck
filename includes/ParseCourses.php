<?php
// Trenton Winkler
// 3/18/2023
// Parses the course number for the letter and name

function parseCourseNumber($courseNumber) {
    $course  = preg_replace('/[^a-zA-Z0-9]/', '', $courseNumber);
    $letters = preg_replace('/[^a-zA-Z]/', '', $course);
    $numbers = preg_replace('/[^0-9]/', '', $course);
    return array($letters, $numbers);
}

function getCourseLetters($courseNumber) {
    $result = parseCourseNumber($courseNumber);
    return $result[0];
}

function getCourseNumbers($courseNumber) {
    $result = parseCourseNumber($courseNumber);
    return $result[1];
}
    
?>