<!-- 
    James Jing
    Course Change Form
-->
<?php
    // Includes
    include_once('includes/RetrieveData.php');
    include_once('includes/ModifyData.php');
    include_once('model/User.php');
    include_once('includes/Prerequisites.php');
    include_once('includes/OrganizeCourses.php');
    include_once('model/MajorChangeRequest.php');

    session_start();
    if (!isset($_SESSION["user"])) 
    {
        header("Location: registration.php");
        exit();
    }
    $user = $_SESSION["user"];

    if(isset($_POST['select'])){
        $_SESSION['usermajor'] = $_POST['majors'];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }    

    if (isset($_POST['submit'])) {
        echo '<script>console.log("dropdown major: '.$_POST['majors'].'")</script>';
        $_SESSION['usermajor'] = $_POST['majors'];
        $Major = @$_POST['majors'];
        submitMajor(new MajorChangeRequest($user->getUsername(), $Major));
        echo '<div id="confirm-box">
                    <div class="confirm-content">
                        <h2>Congratulations! Your major change request submit successfully！</h2>
                        <button id="confirm-close">OK</button>
                    </div>
                </div>';
    }
    echo '<script>
            document.getElementById("confirm-close").addEventListener("click", function() {
            document.getElementById("confirm-box").style.display = "none";
            });
        </script>';

    if(isset($_SESSION['usermajor'])){
        $userMajor = $_SESSION['usermajor'];
    } else {
        $userMajor = $user->getMajor();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width = device-width", initial-scale="1.0"/>
        <link rel="icon" href="img/Course_Check_Logov22-3-03.png">
        <title>Plan of Study</title>
        <link  rel="stylesheet" href="css/change_style.css"/>
        <script src="https://kit.fontawesome.com/64d58efce2.js"
        crossorigin="anonymous">
        </script>
    </head>
    <body>
        
    <header>
        <div class="wrapper">
        <img src="img/Course_Check_Logov22-3-03.png" class="logo">
        <h1>Home</h1>
        <a href="course_selection.php"><input id="btn-course-selection" class="btn-course-selection solid" value="Go to Course Selection"/></a>
        <script src="js/change-major.js"></script>
        <a href="logout.php"><input type="submit" value="Logout" class="btn-logout solid"></a>
        </div>
    </header>

    <div class="instructions">
    <h2>Instructions</h2>
        <ol>
            <li>Select the major you would like to change to from the dropdown menu.</li>
            <li>User may see a plan of study he need to take for that major</li>
            <li>If you want to view the courses required for that major, click on the "View Major" button.</li>
            <li>After you have reviewed the courses and made your decision, click on the "Submit Major Change Request" button to submiit your major request.</li>
        </ol>
    </div>
   
        <div class="container">
            <div class="content">
                <h2>Please select a major to change it</h2>
                <h2>Please select a major to change it</h2>
            </div>
           <form action="" method="post">
           <div class="submit-button">
            <select name="majors" required>
                <?php
                    $majors = getAllMajors();
                    foreach($majors as $Major)
                    {
                        $select = ($Major == $userMajor) ? "selected" : ""; 
                        echo '<option '.$select.' value="'.$Major.'">'.$Major.'</option>';
                    }
                ?>
            </select>
            <input type="submit" name="select" value="View Major" class="btn-submit solid"> 
            <input type="submit" name="submit" value="Submit Major Change Request" class="btn-submit solid">
            </div>
           </form>
            
            <?php
                $organizer = new OrganizeCourses();
                $prereqs        = new Prerequisites();
                $uncompleteCourses = getUserUncompletedCoursesv2($user->getUsername(), $userMajor);
                $uncompleteSemesters = $organizer->splitByExpectedSemester($uncompleteCourses);

                foreach($uncompleteSemesters as $semester) {
                    $totalCredit = 0;
                    echo '<table class="table1">'.
                        '<caption>Courses</caption>'.
                        '<tbody>';

                    echo "<tr>".
                        "<th>Course Number</th>".
                        "<th>Course Name</th>".
                        "<th>Course Credit</th>".
                        "<th>Fall</th>".
                        "<th>Spring</th>".
                        "<th>Prerequisite</th>".
                    "</tr>";

                    foreach($semester as $Course)
                    {
                        $totalCredit += $Course->getCreditHours();
                        echo "<tr>".
                                    "<td>".$Course->getNumber()."</td>".
                                    "<td>".$Course->getName()."</td>".
                                    "<td>".$Course->getCreditHours()."</td>".
                                    "<td>".$Course->getFallAvailability()."</td>".
                                    "<td>".$Course->getSpringAvailability()."</td>".
                                    "<td><div class= 'pop-up-box'>
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
                                "</tr>";        
                    }
                    echo "<td></td>".
                        "<td></td>".
                        "<td>Total Credits: $totalCredit</td>".
                        "<td></td>".
                        "<td></td>".
                        "<td></td>";
                        echo '</tbody>'.
                             '</table><br>';
               }
            ?>
            </tbody>
            </table>
    </body>
</html>