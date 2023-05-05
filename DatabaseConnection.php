<?php
    include_once('model/User.php');

    // Jacob George
    // 2/18/2023
    // Every function that will connect to the database will be held in this class.

    class DatabaseConnection{
        private $serverName     = 'cssqlserver';
        private $connectionInfo = array( "Database"=>'CS414_s23_team2', "UID"=>'cs414_s23_team2_web', "PWD"=>'team2_web');
        
        // Test that you can connect to the database
        function testConnection(){
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);

            if( $conn ) {
                echo "Connection established.<br />";
            } 
            else {
                echo "Connection could not be established.<br />";
                die( print_r( sqlsrv_errors(), true));
            }

            sqlsrv_close($conn);
        }

        // Checks if a user exists in the database
        function userExists($UserName){
            
            $exists = false;

            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Execute the SQL statement
            $sql = "SELECT USER_ID
                      FROM [USER]
                     WHERE USER_ID = ?";
            $stmt = sqlsrv_query( $conn, $sql, array(&$UserName));
            if( $stmt === false) {
                die( print_r( sqlsrv_errors(), true) );
            }
            $queryResult = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);

            // Check if the username was returned
            if($queryResult == null) {
                $exists = false;
            }
            else {
                if($queryResult['USER_ID'] == $UserName) {
                    $exists = true;
                }
            }

            sqlsrv_free_stmt( $stmt);
            sqlsrv_close($conn);

            return $exists;
        }

        // Trenton
        // 2/22/23
        // Queries for the degree id when the degree name is given
        function degreeID($UserMajor){
            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Get the degree ID from the db
            $degreeIdQuery = "SELECT [DEG_ID]
                                FROM [DEGREE]
                               WHERE [DEG_NAME] = ?;";
            $degreeID = sqlsrv_query( $conn, $degreeIdQuery, array(&$UserMajor));
            if($degreeID === false){
                die( print_r( sqlsrv_errors(), true));
            }

            $queryResult = sqlsrv_fetch_array($degreeID, SQLSRV_FETCH_NUMERIC);

            sqlsrv_close($conn);
            return $queryResult[0];
        }

        // Trenton
        // 2/24/23
        // Pull course data into an array
        function courseData($UserMajor) {
            include_once( 'model/course.php');

            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Get the degree ID from the db
            $degreeId = $this->degreeID($UserMajor);

            // Execute the SQL statement
            $sql = "SELECT    C.CRS_ID, CRS_NAME, CRS_CREDITS_COUNT, CRS_FALL, CRS_SPRING
                    FROM      [dbo].[COURSE] AS C
                    FULL JOIN [dbo].[PLAN OF STUDY] AS P ON C.CRS_ID = P.CRS_ID
                    WHERE     P.DEG_ID = ?;";
            $courses = sqlsrv_query( $conn, $sql, array(&$degreeId));
            if($courses === false){
                die( print_r( sqlsrv_errors(), true));
            }

            // Create courses array and populate it with data
            $coursesData = array();
            while($queryRow = sqlsrv_fetch_array($courses, SQLSRV_FETCH_ASSOC)){
                array_push($coursesData, new Course($queryRow['CRS_ID'], 
                                                    $queryRow['CRS_NAME'], 
                                                    $queryRow['CRS_CREDITS_COUNT'], 
                                                    $queryRow['CRS_FALL'], 
                                                    $queryRow['CRS_SPRING']));
            }

            sqlsrv_close($conn);
            return $coursesData;
        }

        // Lujia
        // 2/24/23
        // Inserting username, password, major, and classification
        function userRegistrationInsert($registeredUser){
            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Execute the SQL statement
            $sql = "INSERT INTO USER(USER_ID, USER_PASSWORD, DEG_ID, USER_YEAR) VALUES ('?', '?', '?', '?')";
            $results = sqlsrv_query( $conn, $sql, array($registeredUser->getUsername(), $registeredUser->getPassword(),
                                                        $registeredUser->getMajor() ,$registeredUser->getClassification(), $registeredUser->getSemester(), $registeredUser->getAdminValue()));
            if($results === false){
                die( print_r( sqlsrv_errors(), true));
            } else {
                $x = "User was inserted";
            }
           
            sqlsrv_close($conn);
            return $x;
        }

        // Check new user exist in the database
        // LuJia Huang
        // 03/09/23
        function checkUserExist($userName)
        {
            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Execute the SQL statement
            $sql = "SELECT USER_ID 
                      FROM [USER] 
                     WHERE USER_ID = ?";
            $results = sqlsrv_query( $conn, $sql, array($userName));

            // Sets the message to true if there is a user or false if not
            if(sqlsrv_num_rows($results) > 0){
                $message = true;
            } else{
                $message = false;
            }

            sqlsrv_close($conn);
            return $message;
        }

        // Add a new user to the database
        function addNewUser($userName, $password, $degreeID, $year, $semester, $adminValue) 
        {
            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);   

            // Execute the SQL statement
            $sql = "INSERT INTO [USER](USER_ID, USER_PASSWORD, DEG_ID, USER_YEAR, USER_SEMESTER, USER_IsAdminister)
                    VALUES (?, ?, ?, ?, ?, ?);";
            $stmt = sqlsrv_prepare($conn, $sql, array(&$userName, &$password, $degreeID, $year, $semester, $adminValue));

            if (sqlsrv_execute( $stmt ) === false) 
            {
                echo "Row insertion failed";  
                die(print_r(sqlsrv_errors(), true)); 
            } else
                echo "Row successfully inserted";

            sqlsrv_free_stmt( $stmt);
            sqlsrv_close($conn);
            
        }

        // Trenton
        // 2/27/2023
        // Pull all of the majors in the database
        function getAllMajors(){
            
            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Execute the SQL statement
            $sql = "SELECT DEG_NAME FROM DEGREE;";
            $majorsData = sqlsrv_query($conn, $sql);
            if($majorsData === false){
                die( print_r( sqlsrv_errors(), true));
            }

            // Create majors array and populate it with data
            $majors = array();
            while( $row = sqlsrv_fetch_array( $majorsData, SQLSRV_FETCH_NUMERIC) ){
                array_push($majors, $row[0]);
            }

            sqlsrv_close($conn);
            return $majors;
        }

        // Jacob
        // 2/27/2023
        // Get a user from the DB
        /*function getUser($userName, $password)
        {

            $isCorrectLogin = false;

            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Check password
            $passwordQuery = "SELECT u.USER_ID, u.USER_PASSWORD
                            FROM [USER] as u
                            WHERE u.USER_ID = ?";
            $stmt = sqlsrv_query( $conn, $passwordQuery, array(&$userName));
            if( $stmt === false) {
                die( print_r( sqlsrv_errors(), true) );
            }

            $passQueryResult = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
            if($passQueryResult == null) 
            {
                $isCorrectLogin = false;
            }
            else
            {
                if($passQueryResult['USER_PASSWORD'] === $password) 
                {
                    // Execute the SQL statement
                    $sql = "SELECT u.USER_ID, u.USER_YEAR, d.DEG_NAME, u.USER_SEMESTER, u.USER_IsAdminister
                            FROM [USER] as u
                            JOIN DEGREE as d
                                ON u.DEG_ID = d.DEG_ID
                            WHERE u.USER_ID = ? 
                            AND u.USER_PASSWORD = ?;";
                    $stmt = sqlsrv_query( $conn, $sql, array(&$userName, $password));
                    if( $stmt === false) {
                        die( print_r( sqlsrv_errors(), true) );
                    }
                    
                    // Get the results and put them in a user object
                    $queryResult = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
                    if($queryResult == null) 
                    {
                        $isCorrectLogin = false;
                    }
                    else
                    {
                        $isCorrectLogin = true;
                        $user = new User($queryResult['USER_ID'], $queryResult['USER_YEAR'], $queryResult['DEG_NAME'], $queryResult['USER_SEMESTER'], $queryResult['USER_IsAdminister']);
                    }
                }
                else {
                    $isCorrectLogin = false;
                }
            }

            // Free the connection
            sqlsrv_free_stmt( $stmt);
            sqlsrv_close($conn);

            // Return the user if the login was correct
            if($isCorrectLogin)
                return $user;
            else
                return false;
        }*/
        function getUser($userName, $password)
        {

            $isCorrectLogin = false;

            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Check password
            $passwordQuery = "SELECT u.USER_ID, u.USER_PASSWORD
                            FROM [USER] as u
                            WHERE u.USER_ID = ?";
            $stmt = sqlsrv_query( $conn, $passwordQuery, array(&$userName));
            if( $stmt === false) {
                die( print_r( sqlsrv_errors(), true) );
            }

            $passQueryResult = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
            if($passQueryResult == null) 
            {
                $isCorrectLogin = false;
            }
            else
            {
                if(password_verify($password, $passQueryResult['USER_PASSWORD'])) 
                {
                    // Execute the SQL statement
                    $sql = "SELECT u.USER_ID, u.USER_YEAR, d.DEG_NAME, u.USER_SEMESTER
                            FROM   [USER] as u
                            JOIN   DEGREE as d
                            ON     u.DEG_ID = d.DEG_ID
                            WHERE  u.USER_ID = ? 
                            AND    u.USER_PASSWORD = ?;";
                    $stmt = sqlsrv_query( $conn, $sql, array(&$userName, $passQueryResult['USER_PASSWORD']));
                    if( $stmt === false) {
                        die( print_r( sqlsrv_errors(), true) );
                    }
                    
                    // Get the results and put them in a user object
                    $queryResult = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
                    if($queryResult == null) 
                    {
                        echo "sql is bad?";
                        $isCorrectLogin = false;
                    }
                    else
                    {
                        $isCorrectLogin = true;
                        $user = new User($queryResult['USER_ID'], $queryResult['USER_YEAR'], $queryResult['DEG_NAME'], $queryResult['USER_SEMESTER']);
                    }
                }
                else 
                {
                    $isCorrectLogin = false;
                }
            }

            if ($user == null)
            {
                return $isCorrectLogin;
            }
            else
            {
                return $user;
            }
        }


        // Trenton Winkler
        // 3/4/23
        // Returns the course data for the course selection page
        function extraCourseData($UserMajor) {
            include_once( 'model/CourseSelect.php');

            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Get the degree ID from the db
            $degreeId = $this->degreeID($UserMajor);

            // Execute the SQL statement
            $sql = "SELECT    C.CRS_ID, CRS_NAME, CRS_CREDITS_COUNT, CRS_FALL, CRS_SPRING,
                              SE_COMPLETED, SE_GRADE, POS_EXPECTED_SEMESTER
                    FROM      [dbo].[COURSE] AS C
                    FULL JOIN [dbo].[PLAN OF STUDY] AS P ON C.CRS_ID = P.CRS_ID
                    FULL JOIN [dbo].[STUDENT ENROLLMENT] AS S ON C.CRS_ID = S.CRS_ID
                    WHERE     P.DEG_ID = ?;";
            $courses = sqlsrv_query( $conn, $sql, array(&$degreeId));
            if($courses === false){
                die( print_r( sqlsrv_errors(), true));
            }

            // Create courses array and populate it with data
            $coursesData = array();
            while($queryRow = sqlsrv_fetch_array($courses, SQLSRV_FETCH_ASSOC)){
                array_push($coursesData, new CourseSelection($queryRow['CRS_ID'], 
                                                             $queryRow['CRS_NAME'], 
                                                             $queryRow['CRS_CREDITS_COUNT'], 
                                                             $queryRow['CRS_FALL'], 
                                                             $queryRow['CRS_SPRING'],
                                                             $queryRow['SE_COMPLETED'],
                                                             $queryRow['SE_GRADE'],
                                                             $queryRow['POS_EXPECTED_SEMESTER']));
            }

            sqlsrv_close($conn);
            return $coursesData;
        }

        // Trenton Winkler
        // 3/6/23
        // Insert user and desired major into the database
        function submitMajor($majorChangeRequest){
            include_once('model/MajorChangeRequest.php');
            
            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Execute the SQL statement
            $sql = "INSERT INTO  [DEGREE CHANGE REQUEST](USER_ID, DEG_ID, DCR_RESOLVED)
                    VALUES (?, (select DEG_ID
                                  from DEGREE
                                 where DEG_NAME = ?), 'N')";
            $insertion = sqlsrv_query( $conn, $sql, array($majorChangeRequest->getUsername(),
                                                          $majorChangeRequest->getDesiredMajor()));

            if($insertion === false){
                $successMsg = "Failed to insert";
                die( print_r( sqlsrv_errors(), true));
            } else {
                $successMsg = "Successfully inserted";
            }
           
            sqlsrv_close($conn);
            return $successMsg;
        }

        // Joey Foucha Jr
        // 3/8/23
        // Pull course data into an array
        function getDegreeCourses($UserMajor) 
        {
            include_once('model/course.php');

            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Get the degree ID from the db
            $degreeId = $this->degreeID($UserMajor);

            // Execute the SQL statement
            $sql = "SELECT    CRS_ID
                    FROM      [PLAN OF STUDY] AS P
                    WHERE     P.DEG_ID = ?;";
            $courses = sqlsrv_query($conn, $sql, array(&$degreeId));
            if($courses === false)
            {
                die( print_r( sqlsrv_errors(), true));
            }

            // Create courses array and populate it with data
            $courseIDs = array();
            while($queryRow = sqlsrv_fetch_array($courses, SQLSRV_FETCH_ASSOC))
            {
                array_push($courseIDs, $queryRow['CRS_ID']);
            }
            
            sqlsrv_close($conn);
            return $courseIDs;
        }


        // Joey Foucha Jr
        // 3/8/23
        // Code to create entries into the student_enrollement table based on user's degree/POS
        function enrollStudent(User $user)
        {            
            // Connect to the database
            $conn = sqlsrv_connect($this->serverName, $this->connectionInfo);
            if( $conn === false) 
            {
                die( print_r( sqlsrv_errors(), true));
            }

            $Course_IDs = $this->getDegreeCourses($user->getMajor());
            $USER_ID = $user->getUsername();

            // Loop over the array and insert each value into the database    
            foreach ($Course_IDs as $CRS_ID) 
            {
                // Prepare the statement
                $sql = ("INSERT INTO [STUDENT ENROLLMENT] (USER_ID, CRS_ID, SE_COMPLETED, SE_GRADE) 
                         VALUES      (?, ?, ?, ?);");
                $stmt = sqlsrv_prepare($conn, $sql, array($USER_ID, $CRS_ID, 'N', '-'));

                if (sqlsrv_execute($stmt) === false) 
                {
                    $successMsg = "Row insertion failed";  
                    die(print_r(sqlsrv_errors(), true)); 
                } 
                else
                {
                    $successMsg = "Row successfully inserted";
                }
            }
            
            // Close the statement and the connection
            sqlsrv_free_stmt($stmt);
            sqlsrv_close($conn);

            return $successMsg;
        }

        // Joey Foucha Jr
        // 3/19/23
        // Code to update the user's course information
        function updateCourseSelection(User $user)
        {
            //$completedCourses = $_POST["Completed"];
            //$new_grades = $_POST["Grades"];
            include_once('model/UpdatedCourse.php');

            $updatedCourseList = array();
            $userID = $user->getUsername();
            $courseNumber = '';
            $newGrade = '';
            
            // Loop through the $_POST or $_GET array
            foreach ($_GET as $key => $value) 
            {
                echo $key;
                echo $value;
                // Check if the input element is a checkbox and starts with "checkbox_"
                if (substr($key, 0, 9) == "checkbox_" && $value == "checked") 
                {
                    // Store the checkbox value in the $checkbox_array
                    $courseNumber = (string)substr($key, 9);
                    echo 'yolo';

                    // Check if the input element is a checkbox and starts with "checkbox_"
                    if (substr($key, 0, 9) == "dropdown_" && substr($key, 9) == $courseNumber) 
                    {
                        // Store the checkbox value in the $checkbox_array
                        $newGrade = (string)$value;
                        echo $newGrade;
                    }
                }
                
                if ($courseNumber != '')
                {
                    // Create a new updated course object and add it to the array
                    $courseUpdate = new UpdatedCourse($courseNumber, $newGrade);
                    $updatedCourseList[] = $courseUpdate;
                }
            }
            
            if ($updatedCourseList != null)
            {
                // Connect to the database
                $conn = sqlsrv_connect($this->serverName, $this->connectionInfo);
                if( $conn === false) 
                {
                    die( print_r( sqlsrv_errors(), true));
                }
                
                // For each course checkbox, update the grade and completion status
                foreach ($updatedCourseList as $updatedCourse)
                {
                    // Execute the SQL statement
                    $sql = "UPDATE [STUDENT ENROLLMENT]
                            SET    SE_COMPLETED = 'Y', SE_GRADE = $updatedCourse->getGrade()
                            WHERE  USER_ID = $userID AND CRS_ID = $updatedCourse->getNumber();";
                    
                    $courseStatus = sqlsrv_query($conn, $sql, array(&$updatedCourses));
                    if($courseStatus === false)
                    {
                        die(print_r(sqlsrv_errors(), true));
                    }
                }

                echo "Update Successful";
                sqlsrv_close($conn);
            }
        }
        /*function updateCourseSelection(User $user)
        {
            include_once('model/UpdatedCourse.php');

            $updatedCourseList = array();
            $userID = $user->getUsername();

            // Connect to the database
            $conn = sqlsrv_connect($this->serverName, $this->connectionInfo);
            if( $conn === false) 
            {
                die( print_r( sqlsrv_errors(), true));
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                echo "hello world";
            }

            // Loop through the updated courses
            foreach ($_GET as $key => $value) 
            {
                // Check if the input element is a checkbox and starts with "checkbox_"
                if (substr($key, 0, 9) == "checkbox_" && $value == "checked") 
                {
                    
                    // Get the course number and new grade from the corresponding dropdown
                    $courseNumber = substr($key, 9);
                    $newGrade = $_GET["dropdown_".$courseNumber];

                    // Create a new updated course object and add it to the array
                    $courseUpdate = new UpdatedCourse($courseNumber, $newGrade);
                    $updatedCourseList[] = $courseUpdate;

                    // Execute the SQL statement to update the grade and completion status
                    $sql = "UPDATE [STUDENT ENROLLMENT]
                            SET    SE_COMPLETED = 'Y', SE_GRADE = ?
                            WHERE  USER_ID = ? AND CRS_ID = ?";
                    $params = array($newGrade, $userID, $courseNumber);
                    $courseStatus = sqlsrv_query($conn, $sql, $params);
                    if($courseStatus === false)
                    {
                        die(print_r(sqlsrv_errors(), true));
                    }
                }
            }

            sqlsrv_close($conn);
        }*/



        // Jacob George
        // 3/11/23
        // Get the prereq info for a course
        function getClassPrereqs($courseID) 
        {
            $returnedRows = [];

            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Execute the SQL statement
            $sql = "SELECT CR_PREREQUICITE, CR_YEAR, CR_OPTION_NUMBER, CR_CURRENT_ENROLLMENT, CR_COURSE_GRADE
                      FROM [COURSE REQUIREMENTS]
                     WHERE CRS_ID = ?;";
            $stmt = sqlsrv_query( $conn, $sql, array(&$courseID));
            if( $stmt === false) {
                die( print_r( sqlsrv_errors(), true) );
            }

            // Put the results in an array.
            if (sqlsrv_has_rows($stmt)) 
            {
                $index = 0;
                while ($row = sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC))
                {
                    $returnedRows[$index] = $row;
                    $index++;
                }
            }

            sqlsrv_free_stmt( $stmt);
            sqlsrv_close($conn);

            return $returnedRows;
        }

        // Jacob George
        // 3/22/23
        // Get the user's uncompleted classes
        function getUserUncompletedCourses($userID, $userDegree) 
        {
            include_once( 'model/CourseSelect.php');

            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Execute the SQL statement
            $sql = "SELECT DISTINCT se.CRS_ID, CRS_NAME, CRS_CREDITS_COUNT, CRS_FALL, CRS_SPRING, SE_COMPLETED, SE_GRADE, POS_EXPECTED_SEMESTER
                      FROM [STUDENT ENROLLMENT] se
                      JOIN [COURSE] c ON c.CRS_ID = se.CRS_ID
                      JOIN [PLAN OF STUDY] p ON c.CRS_ID = p.CRS_ID
                      JOIN [DEGREE] d ON d.DEG_ID = p.DEG_ID
                     WHERE SE_COMPLETED = 'N'
                       AND USER_ID = ?
                       AND DEG_NAME = ?;";
            $courses = sqlsrv_query( $conn, $sql, array(&$userID, &$userDegree));
            if($courses === false){
                die( print_r( sqlsrv_errors(), true));
            }

            // Create courses array and populate it with data
            $coursesData = array();
            while($queryRow = sqlsrv_fetch_array($courses, SQLSRV_FETCH_ASSOC)){
                array_push($coursesData, new CourseSelection($queryRow['CRS_ID'], 
                                                             $queryRow['CRS_NAME'], 
                                                             $queryRow['CRS_CREDITS_COUNT'], 
                                                             $queryRow['CRS_FALL'], 
                                                             $queryRow['CRS_SPRING'],
                                                             $queryRow['SE_COMPLETED'],
                                                             $queryRow['SE_GRADE'],
                                                             $queryRow['POS_EXPECTED_SEMESTER']));
            }

            sqlsrv_close($conn);
            return $coursesData;
        }

        // Joey Foucha Jr
        // 3/22/23
        // Gets the information regarding a students course load from STUDENT ENROLLMENT
        function getEnrolledCourses(User $user)
        {
            include_once( 'model/CourseSelect.php');

            // Connect to the database
            $conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
            if( $conn === false) {
                die( print_r( sqlsrv_errors(), true));
            }

            // Get the User ID
            $userId = $user->getUsername();

            // Execute the SQL statement
            $sql = "SELECT    S.CRS_ID, CRS_NAME, CRS_CREDITS_COUNT, CRS_FALL, CRS_SPRING,
                              SE_COMPLETED, SE_GRADE
                    FROM      [COURSE] AS C
                    FULL JOIN [STUDENT ENROLLMENT] as S
                    ON        C.CRS_ID = S.CRS_ID
                    WHERE     S.USER_ID = ?;";
            $courses = sqlsrv_query($conn, $sql, array($userId));
            if($courses === false)
            {
                die( print_r(sqlsrv_errors(), true));
            }

            // Create courses array and populate it with data
            $enrolledCourses = array();
            while($queryRow = sqlsrv_fetch_array($courses, SQLSRV_FETCH_ASSOC))
            {
                array_push($enrolledCourses, new CourseSelection($queryRow['CRS_ID'], 
                                                                 $queryRow['CRS_NAME'], 
                                                                 $queryRow['CRS_CREDITS_COUNT'], 
                                                                 $queryRow['CRS_FALL'], 
                                                                 $queryRow['CRS_SPRING'],
                                                                 $queryRow['SE_COMPLETED'],
                                                                 $queryRow['SE_GRADE'],
                                                                 $priority = null));
            }

            sqlsrv_close($conn);
            return $enrolledCourses;
        }
        
    }
?>