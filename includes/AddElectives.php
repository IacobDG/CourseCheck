<?php
// Trenton
// 4/14/23
// Adds the course to the elective option table if not currently in there

session_start();

include_once('RetrieveData.php');
include_once('ModifyData.php');

if (isset($_POST['courseNumber'])) {
    $courseNumber = $_POST['courseNumber'];

    function checkElectives($electiveName, $courseNumber){
        $courses = getElectiveCourseNumbers($electiveName);
        $check = false;

        echo 'electiveName: '.$electiveName;
        echo 'courseNumber: '.$courseNumber.'\n\n\n';

        foreach($courses as $course){
            if($course == $courseNumber){
                echo 'db course.getNumber: '.$course;
                echo 'courseNumber: '.$courseNumber;
                $check = true;
            }
        }

        return $check;
    }

    if (!isset($_SESSION['electives'])) {
        $_SESSION['electives'] = array();
    }

    if (/*!in_array($courseNumber, $_SESSION['electives']) &&*/ !checkElectives($_SESSION['elective'], $courseNumber)) {
        $_SESSION['electives'][] = $courseNumber;
        echo AddElective($_SESSION['elective'], $courseNumber);
        echo 'Elective added: ' . $courseNumber;
    }
    else{
        echo 'Elective not added';
    }
}
?>
