<!-- 
    James Jing
    Login Form
-->
<?php
    // Start the session
    session_start();

    // Includes
    include_once('includes/RetrieveData.php');
    include_once('includes/ModifyData.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewpoint" content="width=device-wdith, initial-scale=1.0"/>
    <link rel="stylesheet" href="css/new_style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
          integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer"/>
    <link rel="icon" href="img/Course_Check_Logov22-3-03.png">
    <title> Login Form</title>
    
</head>
<body>
<div class="container login-mode">
    <div class="forms-container">
        <div class="login-regist">
            <form action="" class="login-form" method="post">
                <a href="#" class="login-icon">
                    <img src="img/Course_Check_Logov22-3-03.png" width="150"
                         height="150">
                </a>
                <h2 class="title">Login</h2>
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" placeholder="UserName" name="usernameLogin" maxlength="15" pattern="[\x21-\x7D]+" required>
                </div>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" placeholder="Password" name="passwordLogin" maxlength="25" pattern="[\x21-\x7D]+" required>
                </div>
                <input type="submit" value="Login" class="btn solid">
            </form>


            <?php
    include_once('DatabaseConnection.php');
    include_once('Validation.php');
    include_once('includes/RetrieveData.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        $lPassword = @$_POST['passwordLogin'];
        $lUsername = @$_POST['usernameLogin'];

        $validate = new Validation();

        // Login
        if($lPassword != null && $lUsername != null)
        {
            // Validate the login
            if($validate->validateUserLogin($lUsername, $lPassword))
            {
                // Get the user information
                if (!is_null(getUser($lUsername, $lPassword)))
                {
                    $user = getUser($lUsername, $lPassword);
                }
                else
                {
                    $errorMessage = "Password is incorrect";
                    echo '<div id="alert-box">
                         <div class="alert-content">
                                 <h2>' . $errorMessage . '</h2>
                             <button id="alert-close">Close</button>
                         </div>
                     </div>';
                }
                $newAdmin = $user->getUsername();
                $boolin = getAdmin($newAdmin);
                

                // Determine if the user is an administrator
                if($boolin == 'Y')
                {
                    // Transfer to the admin page
                    $_SESSION["user"] = $user;
                    header("Location: admin.php");
                    exit;
                }
                else
                {
                    // Transfer to the home page for students
                    $_SESSION["user"] = $user;
                    header("Location: changeMajorForm.php");
                    exit;
                }
            }
        }
    }
?>

<?php
    include_once('DatabaseConnection.php');
    include_once('Validation.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        $lPassword = @$_POST['passwordLogin'];
        $lUsername = @$_POST['usernameLogin'];

        $validate = new Validation();

        // Login
        if($lPassword != null && $lUsername != null)
        {
            // Validate the login
            if($validate->validateUserLogin($lUsername, $lPassword))
            {
                // Get the user information
                $user = getUser($lUsername, $lPassword);
                $newAdminister = getAdmin( $user->getUsername());


                // Determine if the user is an administrator
                if($newAdminister == 'Y')
                {
                    // Transfer to the admin page
                    $_SESSION["user"] = $user;
                    header("Location: admin.php");
                    exit;
                }
                else
                {
                    // Transfer to the home page for students
                    $_SESSION["user"] = $user;
                    header("Location: home.php");
                    exit;
                }
            }
        }
    }
?>

        </div>
    </div>

    <div class="panels-container">
        <div class="panel right-panel">
            <div class="content">
                <h3>Are you a new student?</h3>
                <p>
                    If you are a new student,
                    you may click the button
                    in the below to regist your
                    new account
                </p>

                <button class="btn transparent" id="Regist-btn" onclick="window.location.href='registration.php'">Register</button>
            </div>
            <img src="img/undraw_developer_activity_re_39tg.svg" class="image" alt="">
        </div>
    </div>
</div>
</body>
</html>