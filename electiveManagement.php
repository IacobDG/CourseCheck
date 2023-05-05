<!-- 
    James Jing
    Elective Management Form
-->
<?php
    // Start the session
    session_start();

    // Includes
    include_once('includes/RetrieveData.php');
    include_once('includes/ModifyData.php');
    include_once('model/CourseIDandNumber.php');

    $elective = '';
    if(isset($_SESSION['elective'])){
        $elective = $_SESSION['elective'];
    }

    if(isset($_POST['search-courses'])){
        $_SESSION['search-courses'] = $_POST['search-courses'];
    }
    if(isset($_POST['elective'])){
        $_SESSION['elective'] = $_POST['elective'];
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
    <title>Elective Management</title>
    <link rel="stylesheet" type="text/css" href="css/electives_style.css">
</head>
<body>
    <div class="wrapper">
        <img src="img/Course_Check_Logov22-3-03.png" class="logo">
        <h1>Elective Management</h1>
        <a href="admin.php"><input type="submit" value="Admin Portal" onclick=""  class="btn-logout solid"></a>
        <a href="logout.php"><input type="submit" value="Logout" onclick=""  class="btn-logout solid"></a>
    </div>
    <div class="row">
        <!-- Manage Electives -->
        <div>
            <!-- <div class="content">
                <h2>Elective Management</h2>
                <form>
                    <label>New Elective Name</label>
                    <input type="text" name="newName">
                    <button type="submit" name="">Submit</button>
                </form>
                <?php
                    function displayElectives(){
                        $electives = getElectives();
                        echo '<ul class="course-list">';
                        foreach($electives as $elective){
                            echo '<li class="list-item">'.
                                '<button type="button" name="remove-elective" class="remove-elective" data-course-number="'.$elective.'">Remove</button>'.'&nbsp'.
                                '<span class="course-number">'.$elective.'</span>'.
                            '</li>';
                        }
                        echo '</ul>';
                    }

                    if(isset($_SESSION['elective'])){
                        displayElectives();
                    }
                ?>
            </div> -->
        </div>
        <!-- View Elective Courses and Remove Them -->
        <div class="column">
            <div class="content">
                <h2>Elective Names</h2>
                <?php $electiveOptions = getElectives(); ?>
                <form method="POST" class="space">
                    <select name="elective">
                        <?php
                            $selected = (isset($_SESSION['elective'])) ? $_SESSION['elective']: 'ANY';
                            foreach($electiveOptions as $option){
                                echo '<option value="'.$option.'" ';
                                        if($selected === $option){echo("selected");} echo '>'.$option.
                                     '</option>';
                            }
                        ?>
                    </select>
                    <button type="submit" name="select-elective">Select</button>
                </form>
                <?php
                    function displayRemoveElectives(){
                        $courses = getElectiveCourses($_SESSION['elective']);
                        echo '<ul class="course-list">';
                        foreach($courses as $course){
                            echo '<li class="list-item">'.
                                '<button type="button" name="remove-elective" class="remove-elective" data-course-number="'.$course->getNumber().'">Remove</button>'.'&nbsp'.
                                '<span class="course-number">'.$course->getNumber().'</span>'.'&nbsp'.
                                '<span class="course-name">'.$course->getName().'</span>'.
                            '</li>';
                        }
                        echo '</ul>';
                    }
                    if(isset($_POST['select-elective'])){
                        displayRemoveElectives();
                    }
                    else if(isset($_SESSION['elective'])){
                        displayRemoveElectives();
                    }
                ?>
            </div>
        </div>
        <!-- Add Elective Courses -->
        <div class="column">
            <div class="content">
                <h2>Course Options</h2>
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
                                '<button type="button" name="add-elective" class="add-elective" data-course-number="'.$course->getNumber().'">Add</button>'.'&nbsp'.
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
                <script>
                    var removeButtons = document.querySelectorAll('.remove-elective');
                    var addButtons = document.querySelectorAll('.add-elective');

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

                // Add click event listeners to all add buttons
                addButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                var message = document.createElement('div');
                message.innerText = 'Add the course successfully';
                message.style.position = 'fixed';
                message.style.top = '50%';
                message.style.left = '50%';
                message.style.transform = 'translate(-50%, -50%)';
                message.style.backgroundColor = '#25367F';
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

                <script type="text/javascript">
                    const input = document.getElementById("search-courses");
                    input.focus();
                    input.select();

                    const addElectiveButtons    = document.querySelectorAll('.add-elective');
                    const removeElectiveButtons = document.querySelectorAll('.remove-elective');

                    // document.getElementById("popup").style.display = "none";
                    // function openPopup() {
                    //     document.getElementById("popup").style.display = "block";
                    // }

                    // function closePopup() {
                    //     document.getElementById("popup").style.display = "none";
                    // }

                    addElectiveButtons.forEach(function(button) {
                        button.addEventListener('click', function(event) {
                            const courseNumber = button.getAttribute('data-course-number');

                            // openPopup();

                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', 'includes/AddElectives.php', true);
                            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState == 4 && xhr.status == 200) {
                                    console.log(xhr.responseText);
                                }
                            };
                            xhr.send('courseNumber=' + courseNumber);
                        });
                    });

                    removeElectiveButtons.forEach(function(button) {
                        button.addEventListener('click', function(event) {
                            const courseNumber = button.getAttribute('data-course-number');

                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', 'includes/RemoveElectives.php', true);
                            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState == 4 && xhr.status == 200) {
                                    console.log(xhr.responseText);
                                }
                            };
                            xhr.send('courseNumber=' + courseNumber);
                        });
                    });
                </script>
            </div>
        </div>
        <!-- <div id="popup">
            <h1>This is a popup!</h1>
            <p>This is the content of the popup.</p>
            <button onclick="closePopup()">Close</button>
        </div> -->
    </div>
</body>
</html>