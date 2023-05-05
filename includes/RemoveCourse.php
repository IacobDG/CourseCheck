<?php
session_start();

include_once('ModifyData.php');

if (isset($_POST['courseNumber'])) {
    $courseNumber = $_POST['courseNumber']; // course number that will be added or removed

    function checkElectives($degree, $courseNumber){
        $courses = getDegreeCourses($degree);
        $check = false;

        echo 'degree: '.      $degree.      '               ';
        echo 'courseNumber: '.$courseNumber.'               ';

        foreach($courses as $course){
            if($course == $courseNumber){
                $check = true;
            }
        }

        return $check;
    }

    if (!isset($_SESSION['degrees'])) {
        $_SESSION['degrees'] = array();
    }

    // checks if the elective is in the major. If so, 
    if (checkElectives($_SESSION['degree'], $courseNumber)) {
        // $_SESSION['degree'][] = $courseNumber;
        echo removeCourse($_SESSION['degree'], $courseNumber);
        echo 'Elective removed: ' . $courseNumber;
    }
    else {
        echo '   That course does not exist in the table   ';
    }
}
?>
