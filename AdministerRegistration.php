<!-- 
    James Jing
    Administer Registration
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
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
          integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer"/>
    <link rel="icon" href="img/Course_Check_Logov22-3-03.png">
    <title>Admin Register Form</title>
    <link rel="stylesheet" href="css/administerRegistration.css">
</head>
<body>
    <div class="container">
        <div class="forms-container">
            <div class="admin-registration">
                <form action="" method="POST" class="regist-form">
                    <a href="#" class="regist-icon">
                        <img src="img/Course_Check_Logov22-3-03.png" width="150"
                             height="150">
                    </a>
                    <!-- Username Textbox -->
                    <h2 class="title">Administer Registration</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="UserName" name="usernameRegister" autocomplete= "off" maxlength="15" pattern="[a-zA-Z0-9]+" required>
                    </div>
    
                    <!-- Password Textbox -->
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" placeholder="Password" name="passwordRegister" maxlength="25" pattern="[\x21-\x7D]+" required>
                    </div>
                    <input type="submit" value="Register" class="btn solid">
                </form>
<?php
    include_once('Validation.php');
    
    $rPassword = null;
    $rEncryptedPassword = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        $rPassword = @$_POST['passwordRegister'];
        $rUsername = @$_POST['usernameRegister'];
        $rEncryptedPassword = password_hash($rPassword, PASSWORD_DEFAULT);
        $administer = 'Y';


        $validate = new Validation();
    }

    if($rEncryptedPassword != NULL)
    {
        // echo 'Username: '.@$_POST['usernameRegister'].
        //      'Password: '.@$_POST['passwordRegister'];
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
                if($validate->validateAdminRegister($rUsername, $rPassword)){
                    // Add new user to the database 
                if(userExists($rUsername))
                {
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
                else
                {
                     // Get the user information
                     $user = getUser($rUsername, $rPassword);

                     // Add a new user into database
                     addNewUser($rUsername, $rEncryptedPassword, 'C055356A-10A1-ED11-B07D-005056ADE522', 'freshman', 'Spring',  $administer);
                     
                    // Tansfer to the next page
                    $_SESSION["user"] = $user;
                    header("Location: admin.php");
                    exit;
                }
             }
            }
    }
?>
            </div>
        </div>
    </div>
    <div class="panel-container">
        <div class="panel admin">
            <div class="content">
                <h3>Student Registration Mode</h3>
                <button class="btn transparent" id="Regist-btn" onclick="window.location.href='registration.php'">Student Registration</button>
            </div>
            <img src="img/undraw_secure_login_pdn4 (1).svg" class="image" alt="">     
        </div>
    </div>
   
</body>
</html>