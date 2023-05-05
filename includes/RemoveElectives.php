<?php
session_start();

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
        $_SESSION['r_electives'] = array();
    }

    if (checkElectives($_SESSION['elective'], $courseNumber)) {
        $_SESSION['r_electives'][] = $courseNumber;
        echo RemoveElective($_SESSION['elective'], $courseNumber);
    }
    else {
        echo '   That course does not exist in the table   ';
    }
    
    echo 'Elective removed: ' . $courseNumber;
}
?>
