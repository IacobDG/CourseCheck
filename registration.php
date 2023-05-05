<?php
    // Start the session
    session_start();

    // Includes
    include_once('includes/RetrieveData.php');
    include_once('includes/ModifyData.php');

    if(isset($_SESSION["user"])){
        header("Location: course_selection.php");
    }

    // Include the Encryption JavaScript file
    //echo '<script src="encryption.js"></script>';

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
    <title>Student Register Form</title>
    <link rel="stylesheet" href="css/new_style.css">
</head>
<body>
<div class="container">
    <div class="forms-container">
        <div class="login-regist">
            <form action="" method="POST" class="regist-form">
                <a href="#" class="regist-icon">
                    <img src="img/Course_Check_Logov22-3-03.png" width="150"
                         height="150">
                </a>
                
                <!-- Username Textbox -->
                <h2 class="title">Student Registration</h2>
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" placeholder="UserName" name="usernameRegister" autocomplete= "off" maxlength="15" pattern="[\x21-\x7D]+" required>
                </div>

                <!-- Password Textbox -->
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" placeholder="Password" name="passwordRegister" maxlength="25" pattern="[\x21-\x7D]+" required>
                </div>

                <!-- Dropdown for Enrollement Year -->
                <div class="dropdown-select">
                    <label class="select-y">Classification</label>
                    <select name="year" required>
                    <option selected disabled value=""> </option>
                    <option value="freshman">Freshman</option>
                    <option value="sophomore">Sophomore</option>
                    <option  value="junior">Junior</optio>
                    <option value="senior">Senior</option>
                    </select>
                </div>

                <!-- Dropdown for Major Selection -->
                <div class="dropdown-select">
                <label class="select-y">Major Selection</label>
                    <select name="major" required>
                    <option selected disabled value=""> </option>
                    <?php
                        // include_once('DatabaseConnection.php');
                        $majors = getAllMajors();
                        print_r($majors);
                        foreach($majors as $Major)
                        {
                            echo "<option>$Major</option>";
                        }
                    ?>
                    </select>
                </div>

                <div class="dropdown-semesters">
                    <label class="select-s">Semester Started</label>
                    <select name="semester" required>
                        <option selected disabled value=""> </option>
                        <option value="Fall">Fall</option>
                        <option value="Spring">Spring</option>
                    </select>
                </div>
                <input id="Register" type="submit" value="Register" class="btn solid">
            </form>
            
<?php
    include_once('Validation.php');

    $rPassword = null;
    $rEncryptedPassword = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        $rMajor             = @$_POST['major'];
        $rPassword          = @$_POST['passwordRegister'];
        $rUsername          = @$_POST['usernameRegister'];
        $rEncryptedPassword = password_hash($rPassword, PASSWORD_DEFAULT);
        $rYear              = @$_POST['year'];
        $semester           = @$_POST['semester'];


        $validate = new Validation();
    }

    // Register
    // James Jing
    if($rEncryptedPassword != NULL)
    {
        // echo 'Username: '.@$_POST['usernameRegister'].
        //      'Password: '.@$_POST['passwordRegister'].
        //      'Encrypted Password: '.$rEncryptedPassword;
        // echo '<script>console.log("Username: '.@$_POST['usernameRegister'].'")</script>';
        // echo '<script>console.log("Password: '.@$_POST['passwordRegister'].'")</script>';
        // echo '<script>console.log("Encrypted Password: '.$rEncryptedPassword.'")</script>';
        if(strlen($rPassword) <= 6)
        {
            echo '<div id="alert-box">
                    <div class="alert-content">
                        <h2>Your password is too short</h2>
                        <p>Please enter a password that is at least 7 characters long.</p>
                        <button id="alert-close">Close</button>
                    </div>
                </div>';
            // Add some JavaScript to close the alert box when the user clicks the close button
            echo '<script>
                    var alertBox = document.getElementById("alert-box");
                    var alertClose = document.getElementById("alert-close");
                    alertClose.addEventListener("click", function() {
                        alertBox.classList.remove("show");
                    });
                    alertBox.classList.add("show");
                </script>';
        }
        else
        {  
            // echo '<script>console.log("0. before everything else")</script>';
            if($validate->validateUserRegister($rUsername, $rEncryptedPassword, $rMajor, $rYear)){
                // echo '<script>console.log("0.5 after validate")</script>';
                // Add new user to the database 
                if(!userExists($rUsername)){
                     $degreeID = degreeID($rMajor);
                     addNewUser($rUsername, $rEncryptedPassword, $degreeID, $rYear, @$semester);
                     // Enroll student in their chosen degree
                     // Tansfer to the next page
                     $user = getUser($rUsername, $rPassword);
                     enrollStudent($user);
                     $_SESSION["user"] = $user;
                     
                     header("Location: course_selection.php");
                     // print_r($_SESSION["user"]);
                     exit;
                }else{
                    echo '<div id="alert-box">
                         <div class="alert-content">
                             <h2>Username already exists</h2>
                             <button id="alert-close">Close</button>
                         </div>
                     </div>';
                    // Add some JavaScript to close the alert box when the user clicks the close button
                    echo '<script>
                         var alertBox = document.getElementById("alert-box");
                         var alertClose = document.getElementById("alert-close");
                         alertClose.addEventListener("click", function() {
                             alertBox.classList.remove("show");
                         });
                         alertBox.classList.add("show");
                     </script>';
                }
            }
         }
    }
?>
        </div>
    </div>

    <div class="panels-container">
        <div class="panel left-panel">
            <div class="content">
                <h3>One of us</h3>
                <p>
                    If you are a student you
                    may click the button in the below
                    to login to you account
                </p>

                <button class="btn transparent" id="Regist-btn" onclick="window.location.href='login.php';">Login</button>
            </div>
            <div class="content">
                <h3>Administer Registration Mode</h3>
                <button class="btn transparent" id="Regist-btn" onclick="window.location.href='AdministerRegistration.php';">Admin Registration</button>
            </div>
            <img src="img/undraw_reading_book_re_kqpk.svg" class="image-2" alt="">
        </div>
    </div>
</div>
</body>
</html>