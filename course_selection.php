<!-- 
    James Jing
    Course Selection Form
-->
<?php
    // Includes
    include_once('model/User.php');
    include_once('model/UpdatedCourse.php');
    include_once('includes/RetrieveData.php');
    include_once('includes/ModifyData.php');    
    include_once('includes/Prerequisites.php');
    include_once('includes/OrganizeCourses.php');

    /*/ Start the session
    session_start();
    $user = $_SESSION["user"];*/
    session_start();
    if (!isset($_SESSION["user"])) 
    {
        header("Location: registration.php");
        exit();
    }
    $user = $_SESSION["user"];
        
?>


<!DOCTYPE html>
<html>    
<head>
    <meta charset="utf-8"/>
    <link rel="icon" href="img/Course_Check_Logov22-3-03.png">
    <title>Course Selection</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="screen" href="css/selection_style.css">
    <script>
        function handleCheckbox(courseNumber, username)
        {
            var serverRequest; 
            var elementId = "checkbox" + courseNumber
			if(window.XMLHttpRequest)
			{ 
				serverRequest = new XMLHttpRequest();
				serverRequest.open("POST", "includes/checkboxcode.php?clicked=" + courseNumber + "&username=" + username + "&value=" + document.getElementById(elementId).checked, true); 
				serverRequest.send(); 
			}
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <img src="img/Course_Check_Logov22-3-03.png" class="logo">
        <h1>Course Selection Form</h1>
        <a href="logout.php"><input type="submit" value="Logout" onclick=""  class="btn-logout solid"></a>
    </div>
    <div class="instructions">
            <h2>How to Select Your Courses:</h2>
            <p>To select your courses, please follow these steps:</p>
            <ol>
                <li>Review the list of available courses in the table below.</li>
                <li>Check the box next to any courses that you have already completed.</li>
                <li>Select the grade you received for any completed courses from the drop-down menu.</li>
                <li>If a course has prerequisites, click the "Prerequisite" link to see the requirements.</li>
                <li>To view your plan of study, click the "Save & Continue" button. 
                    This will redirect you to the major change form, where you can update your major information.</li>
            </ol>


        </div>                       
            <?php
                $prereqs        = new Prerequisites();
                $organizeCourse = new OrganizeCourses();

                $courses = getEnrolledCourses($user);
                // echo '<script></script>';
                // print_r($courses);
                $semesters = $organizeCourse->organizeSelectionCourses($courses);
                // print_r($semesters);

                if(isset($_POST['SaveAndContinue'])){
                    header('Location: changeMajorForm.php');
                }

                echo '<form method="POST">';
                echo '<button type= "submit" id="btn-major-change" name="SaveAndContinue">Save&Continue</button>';
                echo '<script src="js/course-selection.js"></script>';
                // Trenton
                // 4/24/23
                // Checks if a course is an elective
               /* function checkElective($course){
                    $user = $_SESSION["user"];
                    $electives = getMajorElectives($user->getMajor());
                    
                    foreach($electives as $elective){
                        if($course->getNumber() == $elective){
                            return true;
                        } else {
                            return false;
                        }
                    }
                } */

                foreach($semesters as $i=>$semester)
                {
                    $totalCredit = 0;
                     $classLevel = "";
                    if ($i < 2) {
                         $classLevel = "Freshman";
                    } elseif ($i < 4) {
                    $classLevel = "Sophomore";
                    } elseif ($i < 6) {
                    $classLevel = "Junior";
                    } else {
                    $classLevel = "Senior";
                    }
                    echo '<div class="spring-fall-table">'.
                    '<table class="course-tables">'.
                        '<caption>'.$classLevel.'</caption>'.
                        '<thead>'.
                            '<tr>'.
                                '<th>Completed</th>'.
                                '<th>Course Number</th>'.
                                '<th>Course Name</th>'.
                                '<th>Credit</th>'.
                                '<th>Fall</th>'.
                                '<th>Spring</th>'.
                                '<th>Grade</th>'.
                                '<th>Pre</th>'.
                                '<th>Description</th>'.
                            '</tr>'.
                        '</thead>';

                    foreach($semester as $Course)
                    {
                        $totalCredit += $Course->getCreditHours();
                        $completed = ($Course->getCompleted($user)) ? "checked" : "";
                        echo '<script>console.log("course number: '.$Course->getNumber().' completed: '.$completed.'")</script>';
                        // echo '<script>console.log("loop through courses");</script>';
                        // if(checkElective($Course) || checkElectiveNull($Course, $user)){
                        //     $options = getElectiveCourseNumbers($Course->getNumber());
                        //     echo "<tr>". 
                        //          '<td><input type="submit" name ="'.$Course->getNumber().'" value="Submit"></td>'.
                        //          "<td>".$Course->getNumber()."</td>".
                        //          "<td>".
                        //             '<select id ="grade" name = "'.$Course->getNumber().'">'.
                        //                 '<option hidden>Select the course</option>';
                        //                 foreach($options as $option)
                        //                     echo '<option value="'.$option.'">'.$option.'</option>';
                        //     echo "</td>".
                        //          "<td>".$Course->getCreditHours()."</td>".
                        //          '<td></td>'.
                        //          '<td></td>'.
                        //          '<td></td>'.
                        //          '<td></td>';
                        // }
                        // else {
                            echo "<tr>". 
                                    "<td><label><input type='checkbox' id = 'checkbox" . $Course->getNumber() . "' name = 'checkbox" . $Course->getNumber() . "' onclick='handleCheckbox(\"" . $Course->getNumber() . "\",\"" . $user->getUsername() ."\")' value = '".$Course->getNumber()."' ".$completed."></label></td>".
                                    "<td>".$Course->getNumber()."</td>".
                                    "<td>".$Course->getName()."</td>".
                                    "<td>".$Course->getCreditHours()."</td>".
                                    "<td>".$Course->getFallAvailability()."</td>".
                                    "<td>".$Course->getSpringAvailability()."</td>".
                                    "<td>".
                                        "<select id ='grade' name = '".$Course->getNumber()."'>".
                                            "<option                                                              value='-'>-</option>".
                                            "<option "; if($Course->getGrade() === 'A'){echo("selected");} echo " value='A'>A</option>".
                                            "<option "; if($Course->getGrade() === 'B'){echo("selected");} echo " value='B'>B</option>".
                                            "<option "; if($Course->getGrade() === 'C'){echo("selected");} echo " value='C'>C</option>".
                                            "<option "; if($Course->getGrade() === 'D'){echo("selected");} echo " value='D'>D</option>".
                                            "<option "; if($Course->getGrade() === 'F'){echo("selected");} echo " value='F'>F</option>".
                                        "</select>".
                                    "</td>".
                                    "<td>
                                        <div class= 'pop-up-box'>
                                            <a href ='#messageBox" . $Course->getNumber() ."'>Prerequisite</a>
                                        </div>
                                        <div id= 'messageBox" . $Course->getNumber() ."' class = 'pop-up'>
                                            <div class = 'messageContent'>
                                                <h2>Prerequisite Requirement</h2>
                                                <p>";
                                                echo $prereqs->parsePrereqs($Course->getNumber());
                                                echo "</p>
                                                <a href = '#' class='messageClose'>&times;</a>
                                            </div>
                                        </div>
                                    </td>".
                                    "<td><div class= 'pop-up-box'>
                                        <a href ='#descriptMessageBox" . $Course->getNumber() ."'>Description</a>
                                        </div>
                                        <div id= 'descriptMessageBox" . $Course->getNumber() ."' class = 'pop-up'>
                                            <div class = 'messageContent'>
                                                <h2>Course Description</h2>
                                                <p>";
                                                echo getCourseDescription($Course->getNumber());
                                                echo "</p>
                                                <a href = '#' class='messageClose'>&times;</a>
                                            </div>
                                        </div>
                                    </td>";
                        // }
                    }   
            ?>
                        </tbody>
            <?php 
                             echo "<td></td>".
                                  "<td></td>".
                                  "<td></td>".
                                  "<td>Total Credits: $totalCredit</td>".
                                  "<td></td>".
                                  "<td></td>".
                                  "<td></td>".
                                  "<td></td>".
                                  "<td></td>"; 
                             echo "</tbody>";
                             echo "</table>".
                            "</div>";
                }
                '</form>';                

                // PHP code to handle form submission
               /* if (isset($_POST['update']) || isset($_POST['SaveAndContinue'])) 
                {
                    // Create an array to hold Course objects
                    $updatedCourses     = array();
                    $uncompletedCourses = array();
                    // Create an array to hold Course objects
                    $updatedCourses     = array();
                    $uncompletedCourses = array();

                    // Check if checkbox values are set and create an array with checkbox and dropdown values
                    //if (isset($_POST['checkbox'])) 
                    //{
                        $checked_boxes = $_POST['checkbox'];
                        foreach ($checked_boxes as $value) 
                        {
                            $grade = isset($_POST[$value]) ? $_POST[$value] : "-";
                            $updatedCourses[] = new UpdatedCourse($value, $grade, isset($_POST[$value]));
                        }
                    //}
                    / Loop through all checkboxes on the form
                    $checkboxes = $_POST['checkbox'];
                    foreach ($checkboxes as $checkbox)
                    {
                        if (($checkbox))
                        {
                            $grade = isset($_POST[$value]) ? $_POST[$value] : "-";
                            $updatedCourses[] = new UpdatedCourse($checkbox, $grade, "Y");
                            
                        }
                        else 
                        {
                            $updatedCourses[] = new UpdatedCourse($checkbox, "-", "N");
                        }
                    // Loop through all checkboxes on the form
                    $all_checkboxes = $_POST['all_checkboxes'];
                    foreach ($all_checkboxes as $checkbox) 
                    {
                        // Check if the checkbox was checked
                        if (in_array($checkbox, $_POST['checkbox'])) 
                        {
                            $grade = isset($_POST[$checkbox]) ? $_POST[$checkbox] : "-";
                            $updatedCourses[] = new UpdatedCourse($checkbox, $grade, "Y");
                        } 
                        else 
                        {
                            // The checkbox was not checked
                            $uncompletedCourses[] = new UpdatedCourse($checkbox, "-", "N");
                        }
                    }

                    if (empty($updatedCourses)) 
                    {
                        // No checkboxes selected
                        echo "Please select at least one checkbox.";
                    }
                    else 
                    {
                        // Build the SQL statement
                        //$updatedCourseList = array();
                        $userID = $user->getUsername();
                        
                        // Connect to the database
                        $conn = sqlsrv_connect(SERVER_NAME, CONNECTION_INFO);
                        if( $conn === false) 
                        {
                            die( print_r( sqlsrv_errors(), true));
                        }

                        updateCourseSelection($userID, $updatedCourses);
                       
                    }
                }*/

            ?>    

   
         
</body>

</html>