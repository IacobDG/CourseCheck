<?php
// Trenton
// 4/21/23
// Adds the course to the major if not currently in there

session_start();

include_once('RetrieveData.php');
include_once('ModifyData.php');

if (isset($_POST['courseNumber'])) {
    $courseNumber = $_POST['courseNumber'];
    $selectedSemester = $_POST['selectedSemester'];

    function checkCourse($degree, $courseNumber){
        $courses = getDegreeCourses($degree);
        $check = false;

        echo 'degree: '.$degree.      '              ';
        echo 'courseNumber: '.$courseNumber.'          
        ';

        foreach($courses as $course){
            if($course == $courseNumber){
                echo $course. ' == '.$courseNumber.'      ';
                $check = true;
            }
        }

        return $check;
    }

    if (!isset($_SESSION['degrees'])) {
        $_SESSION['degrees'] = array();
    }

    if (!checkCourse($_SESSION['degree'], $courseNumber)) {
        $_SESSION['degrees'][] = $courseNumber;
        echo addCourse($_SESSION['degree'], $courseNumber, $selectedSemester);
        echo 'Course added: ' . $courseNumber;
    }
    else{
        echo 'Course not added';
    }
}
?>
