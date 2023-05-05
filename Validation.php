<?php
    // include_once 'DatabaseConnection.php';
    include_once 'includes/RetrieveData.php';
    include_once 'includes/ModifyData.php';

    define('USER_MAX_LENGTH',     15);
    define('USER_MIN_LENGTH',     1);
    define('PASSWORD_MAX_LENGTH', 25);
    define('PASSWORD_MIN_LENGTH', 8);

    // Validates User Input
    class Validation
    {   

        // Validate a user login
        function validateUserLogin($userName, $password)
        {
            $isValid = true;
            $errorMessage = '';
            
            // Check the username is alpha-numeric
            //if(!ctype_alnum($userName))
            //{
              //  $isValid = false;
             //   $errorMessage .= "Username is not alpha-numeric.";
           // }

            // Check that the user is in the DB
            // $dbConnection = new DatabaseConnection();
            if(!userExists($userName))
            {
                $isValid = false;
                $errorMessage .= "This account has not been created."; 
            }

            // Check that the password is correct
            if(getUser($userName, $password) == false)
            {
                $isValid = false;
                $errorMessage .= "Password is incorrect"; 
            }

            if (!$isValid) 
            {
                if (!$isValid) 
                {
                    echo '<div id="alert-box">
                         <div class="alert-content">
                                 <h2>' . $errorMessage . '</h2>
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
                echo "<style>
                /* Add some CSS styles for the alert box */
                #alert-box {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                    opacity: 0;
                    pointer-events: none;
                }
                
                .alert-content {
                    background-color: #fff;
                    padding: 2em;
                    border-radius: 1em;
                    box-shadow: 0 0 20px rgba(0,0,0,0.5);
                    animation: alert-show 0.5s ease-in-out forwards;
                    max-width: 80%;
                    text-align: center;
                    position: relative;
                }
                
                .alert-header {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin-bottom: 1em;
                }
                
                .alert-header h2 {
                    margin-top: 0;
                    margin-bottom: 0;
                    font-size: 2.5em;
                    color: #FF5300;
                    font-weight: bold;
                    margin-left: 0.5em;
                    text-transform: uppercase;
                }
                
                .alert-icon {
                    font-size: 3em;
                    color: #FF5300;
                    transform: rotate(45deg);
                }
                
                .alert-content p {
                    margin-bottom: 2em;
                    font-size: 1.2em;
                    line-height: 1.5;
                }
                
                #alert-close {
                    background-color: #FF5300;
                    color: #fff;
                    border: none;
                    border-radius: 0.5em;
                    padding: 0.5em 1em;
                    margin-top: 1em;
                    cursor: pointer;
                    transition: all 0.2s ease-in-out;
                    font-size: 1.2em;
                    position: absolute;
                    bottom: -1.5em;
                    right: 0;
                    left: 0;
                    margin: auto;
                }
                
                #alert-close:hover {
                    background-color: #fff;
                    color: #FF5300;
                    box-shadow: 0 0 10px rgba(255,83,0,0.5);
                }
                
                #alert-box.show {
                    opacity: 1;
                    pointer-events: auto;
                }
                
                @media only screen and (max-width: 600px) {
                    .alert-content {
                        max-width: 90%;
                    }
                }
                
                @keyframes alert-show {
                    from {
                        transform: translateY(-100px);
                        opacity: 0;
                    }
                    to {
                        transform: translateY(0);
                        opacity: 1;
                    }
                }

                
                    </style>";
            }
            return $isValid;
        }

        // Validate a user login
        function validateUserRegister($userName, $password, $selected_major, $year)
        {
            echo '<script>console.log("validate: username == '.$userName.'")</script>';
            echo '<script>console.log("validate: password == '.$password.'")</script>';
            
            $isValid = true;
            $errorMessage = '';
            
            // Check the username is alpha-numeric
            //if(!ctype_alnum($userName))
            //{
              //  $isValid = false;
              //  $errorMessage .= "Username is not alpha-numeric.";
            //}

            // Check the password is alpha-numeric
            //if(!ctype_alnum($password))
            //{
           //     $isValid = false;
           //     $errorMessage .= "Password is not alpha-numeric";
           // }

            // Check the username is less than the max length
            if(strlen($userName) > USER_MAX_LENGTH)
            {
                $isValid = false;
                $errorMessage .= "Username is too long."; 
            }

            /*// Check the password is less than the max length
            if(strlen($password) > PASSWORD_MAX_LENGTH)
            {
                $isValid = false;
                $errorMessage .= "Password is too long."; 
            }*/

            /*/ Check the username is greater than the min length
            if(strlen($userName) < USER_MIN_LENGTH)
            {
                $isValid = false;
                $errorMessage .= "Username is too short."; 
            }

            // Check the password is greater than the min length
            if(strlen($password) < PASSWORD_MIN_LENGTH)
            {
                $isValid = false;
                $errorMessage .= "Password is too short."; 
            }*/

            // Check that the user is in the DB
            // $dbConnection = new DatabaseConnection();
            if(userExists($userName))
            {
                $isValid = false;
                $errorMessage .= "This account already exists. Try logging in."; 
            }

            // Check that the password is correct
            // if(getUser($userName, $password) != false)
            // {
            //     $isValid = false;
            //     $errorMessage .= "Password is incorrect"; 
            // }

            // Check that the year selection is appropriate
            $majors = getAllMajors();
            $value = true;
            foreach($majors as $major){
                if($selected_major == $major)
                    $value = false;
            }
            if($value){
                $isValid = false;
                $errorMessage .= "Major is an invalid selection.";
            }

            // Check that the year selection is appropriate
            if($year != "freshman" && $year != "sophomore" && $year != "junior" && $year != "senior")
            {
                $isValid = false;
                $errorMessage .= "Year is an invalid selection";
            }

            if (!$isValid) {
                if (!$isValid) {
                    echo '<div id="alert-box">
                         <div class="alert-content">
                                 <h2>' . $errorMessage . '</h2>
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
                echo "<style>
                /* Add some CSS styles for the alert box */
                #alert-box {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                    opacity: 0;
                    pointer-events: none;
                }
                
                .alert-content {
                    background-color: #fff;
                    padding: 2em;
                    border-radius: 1em;
                    box-shadow: 0 0 20px rgba(0,0,0,0.5);
                    animation: alert-show 0.5s ease-in-out forwards;
                    max-width: 80%;
                    text-align: center;
                    position: relative;
                }
                
                .alert-header {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin-bottom: 1em;
                }
                
                .alert-header h2 {
                    margin-top: 0;
                    margin-bottom: 0;
                    font-size: 2.5em;
                    color: #FF5300;
                    font-weight: bold;
                    margin-left: 0.5em;
                    text-transform: uppercase;
                }
                
                .alert-icon {
                    font-size: 3em;
                    color: #FF5300;
                    transform: rotate(45deg);
                }
                
                .alert-content p {
                    margin-bottom: 2em;
                    font-size: 1.2em;
                    line-height: 1.5;
                }
                
                #alert-close {
                    background-color: #FF5300;
                    color: #fff;
                    border: none;
                    border-radius: 0.5em;
                    padding: 0.5em 1em;
                    margin-top: 1em;
                    cursor: pointer;
                    transition: all 0.2s ease-in-out;
                    font-size: 1.2em;
                    position: absolute;
                    bottom: -1.5em;
                    right: 0;
                    left: 0;
                    margin: auto;
                }
                
                #alert-close:hover {
                    background-color: #fff;
                    color: #FF5300;
                    box-shadow: 0 0 10px rgba(255,83,0,0.5);
                }
                
                #alert-box.show {
                    opacity: 1;
                    pointer-events: auto;
                }
                
                @media only screen and (max-width: 600px) {
                    .alert-content {
                        max-width: 90%;
                    }
                }
                
                @keyframes alert-show {
                    from {
                        transform: translateY(-100px);
                        opacity: 0;
                    }
                    to {
                        transform: translateY(0);
                        opacity: 1;
                    }
                }
                    </style>";
            }
            return $isValid;
        }

        function validateAdminRegister($userName, $password)
        {
            $isValid = true;
            $errorMessage = '';
            
            // Check the username is alpha-numeric
            //if(!ctype_alnum($userName))
            //{
               // $isValid = false;
                //$errorMessage .= "Username is not alpha-numeric.";
            //}

            // Check the username is less than the max length
            if(strlen($userName) > USER_MAX_LENGTH)
            {
                $isValid = false;
                $errorMessage .= "Username is too long."; 
            }

            // Check the password is less than the max length
            if(strlen($password) > PASSWORD_MAX_LENGTH)
            {
                $isValid = false;
                $errorMessage .= "Password is too long."; 
            }

            // Check that the user is in the DB
            // $dbConnection = new DatabaseConnection();
            if(userExists($userName))
            {
                $isValid = false;
                $errorMessage .= "This account already exists. Try logging in."; 
            }

            // Check that the password is correct
            // if(getUser($userName, $password) != false)
            // {
            //     $isValid = false;
            //     $errorMessage .= "Password is incorrect"; 
            // }


            if (!$isValid) {
                if (!$isValid) {
                    echo '<div id="alert-box">
                         <div class="alert-content">
                                 <h2>' . $errorMessage . '</h2>
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
                echo "<style>
                /* Add some CSS styles for the alert box */
                #alert-box {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                    opacity: 0;
                    pointer-events: none;
                }
                
                .alert-content {
                    background-color: #fff;
                    padding: 2em;
                    border-radius: 1em;
                    box-shadow: 0 0 20px rgba(0,0,0,0.5);
                    animation: alert-show 0.5s ease-in-out forwards;
                    max-width: 80%;
                    text-align: center;
                    position: relative;
                }
                
                .alert-header {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin-bottom: 1em;
                }
                
                .alert-header h2 {
                    margin-top: 0;
                    margin-bottom: 0;
                    font-size: 2.5em;
                    color: #FF5300;
                    font-weight: bold;
                    margin-left: 0.5em;
                    text-transform: uppercase;
                }
                
                .alert-icon {
                    font-size: 3em;
                    color: #FF5300;
                    transform: rotate(45deg);
                }
                
                .alert-content p {
                    margin-bottom: 2em;
                    font-size: 1.2em;
                    line-height: 1.5;
                }
                
                #alert-close {
                    background-color: #FF5300;
                    color: #fff;
                    border: none;
                    border-radius: 0.5em;
                    padding: 0.5em 1em;
                    margin-top: 1em;
                    cursor: pointer;
                    transition: all 0.2s ease-in-out;
                    font-size: 1.2em;
                    position: absolute;
                    bottom: -1.5em;
                    right: 0;
                    left: 0;
                    margin: auto;
                }
                
                #alert-close:hover {
                    background-color: #fff;
                    color: #FF5300;
                    box-shadow: 0 0 10px rgba(255,83,0,0.5);
                }
                
                #alert-box.show {
                    opacity: 1;
                    pointer-events: auto;
                }
                
                @media only screen and (max-width: 600px) {
                    .alert-content {
                        max-width: 90%;
                    }
                }
                
                @keyframes alert-show {
                    from {
                        transform: translateY(-100px);
                        opacity: 0;
                    }
                    to {
                        transform: translateY(0);
                        opacity: 1;
                    }
                }
                    </style>";
            }
            return $isValid;
        }

        
    }

?>