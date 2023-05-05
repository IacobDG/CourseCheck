<!-- 
    James Jing
    Course Management Form
-->
<?php
    // Start the session
    session_start();

    // Includes
    include_once('includes/RetrieveData.php');
    include_once('includes/ModifyData.php');
    include_once('model/CourseIDandNumber.php');

    $degree = '';
    if(isset($_SESSION['degree'])){
        $degree = $_SESSION['degree'];
    }

    if(isset($_POST['search-courses'])){
        $_SESSION['search-courses'] = $_POST['search-courses'];
    }
    if(isset($_POST['degree'])){
        $_SESSION['degree'] = $_POST['degree'];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewpoint" content="width=device-wdith, initial-scale=1.0"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
          integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer"/>
    <link rel="icon" href="img/Course_Check_Logov22-3-03.png">
    <title>Course Management</title>
    <link rel="stylesheet" type="text/css" href="css/electives_style.css">
</head>
<body>
    <div class="wrapper">
        <img src="img/Course_Check_Logov22-3-03.png" class="logo">
        <h1>Course Management</h1>
        <a href="admin.php"><input type="submit" value="Admin Portal" onclick=""  class="btn-logout solid"></a>
        <a href="logout.php"><input type="submit" value="Logout" onclick=""  class="btn-logout solid"></a>
    </div>
    <div class="row">
        <div class="column">
            <div class="content">
                <h2>Elective Names</h2>
                <?php $majors = getAllMajors(); ?>
                <form method="POST" class="space">
                    <select name="degree">
                        <?php
                            foreach($majors as $major){
                                echo '<option value="'.$major.'" ';
                                        if($degree === $major){echo("selected");} echo '>'.$major.
                                     '</option>';
                            }
                        ?>
                    </select>
                    <button type="submit" name="select-major">Select</button>
                </form>
                <?php
                    function displayRemoveCourse(){
                        $courses = courseDataWSemester($_SESSION['degree']);
                        echo '<ul class="course-list">';
                        foreach($courses as $course){
                            echo '<li class="list-item">'.
                                '<button type="button" name="remove-course" class="remove-course" data-course-number="'.$course->getNumber().'">Remove</button>'.'&nbsp'.
                                '<span class="course-number">'.$course->getNumber().'</span>'.'&nbsp'.
                                '<span class="course-name">'.$course->getName().'</span>'.
                                '<span >'.$course->getPriority().'</span>'.
                            '</li>';
                        }
                        echo '</ul>';
                    }
                    if(isset($_POST['select-major'])){
                        displayRemoveCourse();
                    }
                    else if(isset($_SESSION['degree'])){
                        displayRemoveCourse();
                    }
                ?>
            </div>
            <script>
                    var removeButtons = document.querySelectorAll('.remove-course');
                    var addButtons = document.querySelectorAll('.add-course');

                    // Add click event listeners to all remove buttons
                    removeButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                    var message = document.createElement('div');
                    message.innerText = 'Remove the course successfully';
                    message.style.position = 'fixed';
                    message.style.top = '50%';
                    message.style.left = '50%';
                    message.style.transform = 'translate(-50%, -50%)';
                    message.style.backgroundColor = '#FF5300';
                    message.style.padding = '20px';
                    message.style.border = '1px solid black';
                    message.style.zIndex = '9999';
                    document.body.appendChild(message);
                    setTimeout(function() {
                    document.body.removeChild(message);
                }, 700); 
                });
              });
                </script>
        </div>
        <div class="column">
            <div class="content">
                <h2>Courses</h2>
                <form method="POST" class="space">
                    <input type="text" name="search-courses" id="search-courses" 
                        value="<?php echo isset($_SESSION['search-courses']) ? $_SESSION['search-courses'] : '';?>">
                    </input>
                    <button type="submit" name="submit" id="submit">Search</button>
                </form>
                <div>
                <?php
                    function displayAddElectives(){
                        $courses = searchCourses($_SESSION['search-courses']);
                        echo '<ul class="course-list">';
                        foreach($courses as $course){
                            echo '<li class="list-item">'.
                                '<button type="button" name="add-course" class="add-course" data-course-number="'.$course->getNumber().'">Add</button>'.'&nbsp'.
                                '<span class="course-number">'.$course->getNumber().'</span>'.'&nbsp'.
                                '<span class="course-name">'.$course->getName().'</span>'.
                            '</li>';
                        }
                        echo '</ul>';
                    }
                    if(isset($_POST['submit'])){
                        displayAddElectives();
                    }
                    else if(isset($_SESSION['search-courses'])){
                        displayAddElectives();
                    }
                ?>
            </div>
        </div>
    </div>
    <div id="popup">
        <h3 id="popupTitle">Select the expected semester this course will be taken in this major</h3>
        <select name="dropdown" id="dropdown">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
        </select>
        <button type="submit" id="close" class="close">Select Semester</button>
    </div>
    <script type="text/javascript">
        const input = document.getElementById("search-courses");
        input.focus();
        input.select();
        console.log("input was selected");

        popup.style.display = "none";

        const addCourseButtons    = document.querySelectorAll('.add-course');
        const removeElectiveButtons = document.querySelectorAll('.remove-course');
        const selectSemesterButton = document.querySelector('.close');
        const selectSemesterValue =document.getElementById('dropdown');

        let courseNumber = 0;

        addCourseButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {

                popup.style.display = "block";
                // Center the popup on the screen
                // popup.style.left = (window.innerWidth - popup.offsetWidth) / 2 + "px";
                // popup.style.top = (window.innerHeight - popup.offsetHeight) / 2 + "px";
                
                // Block other features until the popup is clicked
                // document.body.style.pointerEvents = "none";

                popup.appendChild(dropdown);

                courseNumber = button.getAttribute('data-course-number');
                console.log("I called the function and here's the course number: ", courseNumber);
            });
        });

        selectSemesterButton.addEventListener('click', function(event) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'includes/AddCourse.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log(xhr.responseText);
                }
            };

            const semester = selectSemesterValue.value;
            
            xhr.send('courseNumber=' + courseNumber + '&selectedSemester=' + semester);
        });

        removeElectiveButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                const courseNumber = button.getAttribute('data-course-number');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'includes/RemoveCourse.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        console.log(xhr.responseText);
                    }
                };
                xhr.send('courseNumber=' + courseNumber);
            });
        });

        document.getElementById("close").addEventListener("click", function() {
            popup.style.display = "none";
        });
    </script>
</body>
</html>