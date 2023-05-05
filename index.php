<!-- 
    James Jing
    Main page
-->


<?php
    // Start the session
    session_start();
    header("Location: registration.php");

    // Include the Encryption JavaScript file
    echo '<script src="encryption.js"></script>';
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
    <title>Login and Regist Form</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="forms-container">
        <div class="login-regist">
            <form action="index.php" class="login-form" method="post">
                <a href="#" class="login-icon">
                    <img src="img/Course_Check_Logov22-3-02.png" width="150"
                         height="150">
                </a>
                <h2 class="title">Login</h2>
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" placeholder="UserName" name="usernameLogin" maxlength="15" pattern="[a-zA-Z0-9]+" required>
                </div>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" placeholder="Password" name="passwordLogin" maxlength="25" pattern="[a-zA-Z0-9]+" required>
                </div>
                <input type="submit" value="Login" class="btn solid">
            </form>

            <form action="" method="POST" class="regist-form">
                <a href="#" class="regist-icon">
                    <img src="img/Course_Check_Logov22-3-03.png" width="150"
                         height="120">
                </a>
                
                <!-- Username Textbox -->
                <h2 class="title">Register</h2>
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" placeholder="UserName" name="usernameRegister" autocomplete= "off" maxlength="15" pattern="[a-zA-Z0-9]+" required>
                </div>

                <!-- Password Textbox -->
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" placeholder="Password" name="passwordRegister" maxlength="25" pattern="[a-zA-Z0-9]+" required>
                </div>

                <!-- Dropdown for Enrollement Year -->
                <div class="dropdown-select">
                    <label class="select-y">Classification</label>
                    <select name="year" required>
                    <option selected disabled value=""> </option>
                    <option value="freshman">Freshman</option>
                    <option value="sophmore">Sophmore</option>
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
                        include_once('DatabaseConnection.php');
                        $dbconnection = new DatabaseConnection();
                        $majors = $dbconnection->getAllMajors();
                        print_r($majors);
                        foreach($majors as $Major)
                        {
                            echo "<option>$Major</option>";
                        }
                    ?>
                    </select>
                </div>
                <input id="Register" type="submit" value="Register" class="btn solid">
            </form>
            
<?php
    include_once('DatabaseConnection.php');
    include_once('Validation.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {

        $lPassword = @$_POST['passwordLogin'];
        $lUsername = @$_POST['usernameLogin'];
        $encryptedLPassword = exec("node -p 'require(\"./encryption.js\").encryptPassword(\"$lPassword\", \"$lUsername\")'");
        $rMajor    = @$_POST['major'];
        $rPassword = @$_POST['passwordRegister'];
        $rUsername = @$_POST['usernameRegister'];
        $encryptedRPassword = exec("node -p 'require(\"./encryption.js\").encryptPassword(\"$rPassword\", \"$rUsername\")'");
        $rYear     = @$_POST['year'];
        $semester  = @$_POST['semester'];


        $validate = new Validation();
        $dbConnection = new DatabaseConnection();

        // Jacob George
        // 3/11/2023
        // Login
        if($lPassword != null && $lUsername != null)
        {
            // Set to login side of the screen
            echo '<script>document.querySelector(".container").classList.add("login-mode");</script>';

            // Validate the login
            if($validate->validateUserLogin($lUsername, $encryptedLPassword))
            {
                // Get the user information
                $user = $dbConnection->getUser($lUsername, $encryptedLPassword);

                // Tansfer to the next page
                $_SESSION["user"] = $user;
                header("Location: course_selections/course_selection.php");
                exit;
            }
        }

        // Register
        if($rPassword != NULL)
        {
            if(strlen($rPassword) <= 6)
            {
                echo"Your password is too short ";
            }
            else
            {
                // Set to register side of the screen
                echo '<script>document.querySelector(".container").classList.remove("login-mode");</script>';

                // Validate the registration
                // if($validate->validateUserRegister($rUsername, $rPassword, $rMajor, $rYear))
                // {
                // Add new user to the database 
                if($dbConnection->userExists($rUsername)){
                    echo 'Username already exists';
                }
                else
                {
                    $degreeID = $dbConnection->degreeID($rMajor);
                    $dbConnection->addNewUser($rUsername, $encryptedRPassword, $degreeID, $rYear, $semester);

                    // Get the user information
                    $user = $dbConnection->getUser($rUsername, $encryptedRPassword);

                    // Enroll student in their chosen degree
                    $dbconnection->enrollStudent($user);

                    // Tansfer to the next page
                    $_SESSION["user"] = $user;
                    header("Location: course_selections/course_selection.php");
                    exit;
                }                
           // }
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

                <button class="btn transparent" id="Login-btn">Login</button>
            </div>
            <img src="img/undraw_secure_login_pdn4 (1).svg" class="image" alt="">
        </div>
        <div class="panel right-panel">
            <div class="content">
                <h3>Are you a new student?</h3>
                <p>
                    If you are a new student,
                    you may click the button
                    in the below to regist your
                    new account
                </p>

                <button class="btn transparent" id="Regist-btn">Register</button>
            </div>
            <img src="img/undraw_developer_activity_re_39tg.svg" class="image" alt="">
        </div>
        <script src="js/app.js"></script>
    </div>
</div>
</body>
</html>
