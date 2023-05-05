<?php
// Jacob George
// 2/18/2023
// Every function that will connect to the database will be held in this file.

// Includes
spl_autoload('autoLoader');

include_once('DatabaseConstants.php');
include_once('./model/User.php');
include_once('./model/Course.php');
include_once('./model/CourseSelect.php');
include_once('./model/CourseIDandNumber.php');

// Test that you can connect to the database
function testConnection(){
    $conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);

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
    $conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
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
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
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
	include_once('./model/Course.php');
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Get the degree ID from the db
	$degreeId = degreeID($UserMajor);

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

// Check new user exist in the database
// LuJia Huang
// 03/09/23
function checkUserExist($userName)
{
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
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

// Trenton
// 2/27/2023
// Pull all of the majors in the database
function getAllMajors(){
	
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
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

// Jacob && Joey Foucha Jr
// 2/27/2023 && 4/17/23
// Get a user from the DB
/*function getUser($userName, $password)
{

	$isCorrectLogin = false;

	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
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
	else{
		if($passQueryResult['USER_PASSWORD'] === $password) {
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
	}*/

function getUser($userName, $password)
{
	// echo '<script>console.log("5. getUser is active")</script>';
	$isCorrectLogin = false;

	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
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
		// echo '<script>console.log("6. getUser: Did not work: 1st level if.")</script>';
		$isCorrectLogin = false;
	}
	else
	{
		// $password = "password";
		echo '<script>console.log("7. password == '.$password.'")</script>';
		echo '<script>console.log("8. query password == '.$passQueryResult['USER_PASSWORD'].'")</script>';
		
		
		//$hash = ;

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
				echo '<script>console.log("9. sql is bad?")</script>';
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
			echo '<script>console.log("10. Did not work: password not verified")</script>';
			$isCorrectLogin = false;
		}
	}

	// Free the connection
	sqlsrv_free_stmt( $stmt);
	sqlsrv_close($conn);


	// Return the user if the login was correct
	if($isCorrectLogin){
		return $user;
	}		
	else{
		echo '<script>console.log("This is empty")</script>';
		return NULL;
	}		
}

// Trenton Winkler
// 3/4/23
// Returns the course data for the course selection page
function extraCourseData($UserMajor) {
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Get the degree ID from the db
	$degreeId = degreeID($UserMajor);

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

// Joey Foucha Jr
// 3/8/23
// Pull course data into an array
function getDegreeCourses($UserMajor) 
{
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Get the degree ID from the db
	$degreeId = degreeID($UserMajor);

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

// Jacob George
// 3/11/23
// Get the prereq info for a course
function getClassPrereqs($courseID) 
{
	$returnedRows = [];

	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
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
function getUserUncompletedCourses($userID, $userMajor) 
{
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
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
	$courses = sqlsrv_query( $conn, $sql, array(&$userID, &$userMajor));
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
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Get the User ID
	$userId = $user->getUsername();
	$degreeID = degreeID($user->getMajor());

	// Execute the SQL statement
	$sql = "SELECT S.CRS_ID, CRS_NAME, CRS_CREDITS_COUNT, CRS_FALL, CRS_SPRING,
			       SE_COMPLETED, SE_GRADE, POS_EXPECTED_SEMESTER
			FROM   [COURSE] AS C
			JOIN   [STUDENT ENROLLMENT] as S ON C.CRS_ID = S.CRS_ID
			join   [PLAN OF STUDY] as p on p.CRS_ID = c.CRS_ID
			WHERE  S.USER_ID = ?
			AND    P.DEG_ID  = ?
			ORDER BY P.POS_EXPECTED_SEMESTER;";
	$courses = sqlsrv_query($conn, $sql, array(&$userId, $degreeID));
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
														 $queryRow['POS_EXPECTED_SEMESTER']));
	}

	sqlsrv_close($conn);
	return $enrolledCourses;
}

// Joey Foucha Jr
// 3/22/23
// Auto checks a course that should have been completed
function autoCheck(User $user, string $course)
{
	include_once( 'model/CourseSelect.php');
	//include_once('DatabaseConnection.php');
	$shouldComplete = false;

	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) 
	{
		die( print_r( sqlsrv_errors(), true));
	}

	// Get the User ID
	$userId = $user->getUsername();

	// Execute the SQL statement
	$sql = "SELECT    POS_EXPECTED_SEMESTER
			FROM      [USER] AS U
			FULL JOIN [PLAN OF STUDY] AS P
			ON        U.DEG_ID = P.DEG_ID
			WHERE     CRS_ID = ? AND USER_ID = ?;";
	$expectedSemester = sqlsrv_query($conn, $sql, array($course, $userId));
	if($expectedSemester === false)
	{
		die( print_r(sqlsrv_errors(), true));
	}

	$semester = sqlsrv_fetch_array($expectedSemester, SQLSRV_FETCH_NUMERIC);

	if ($user->getClassification() === 'sophomore' && $semester[0] <= 2)
	{
		$shouldComplete = true;
	}
	if ($user->getClassification() === 'junior' && $semester[0] <= 4)
	{
		$shouldComplete = true;
	}
	if ($user->getClassification() === 'senior' && $semester[0] <= 6)
	{
		$shouldComplete = true;
	}

	sqlsrv_close($conn);
	return $shouldComplete;
}

// Check new user exist in the database
// LuJia Huang
// 03/09/23
function getCourseDescription($courseNumber)
{
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Execute the SQL statement
	$sql = "SELECT CRS_DESCRIPT 
			  FROM [COURSE] 
			 WHERE CRS_ID = ?";
	$descriptionQuery = sqlsrv_query( $conn, $sql, array($courseNumber));

	if($descriptionQuery === false)
	{
		die( print_r( sqlsrv_errors(), true));
	}

	// Create courses array and populate it with data
	$description = sqlsrv_fetch_array($descriptionQuery, SQLSRV_FETCH_NUMERIC);

	sqlsrv_close($conn);
	return $description[0];
}

// Trenton
// 2/24/23
// Pull course data into an array
function searchCourses($crsValue) {
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Execute the SQL statement
	$sql = "SELECT CRS_ID, CRS_NAME
			FROM   [COURSE]
			WHERE  CRS_ID   LIKE ?
			OR     CRS_NAME LIKE ?;";
	$params = array('%'.$crsValue.'%', '%'.$crsValue.'%');
	$courses = sqlsrv_query( $conn, $sql, $params);
	if($courses === false){
		die( print_r( sqlsrv_errors(), true));
	}

	// Create courses array and populate it with data
	$coursesData = array();
	while($queryRow = sqlsrv_fetch_array($courses, SQLSRV_FETCH_ASSOC)){
		array_push($coursesData, new CourseIDandNumber($queryRow['CRS_ID'], $queryRow['CRS_NAME']));
	}

	sqlsrv_close($conn);
	return $coursesData;
}

// Trenton Winkler
// 4/14/23
// Returns all of the electives
function getElectives(){
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Execute the SQL statement
	$sql = "SELECT ELECT_NAME
	        FROM ELECTIVES";
	$courses = sqlsrv_query( $conn, $sql);
	if($courses === false){
		die( print_r( sqlsrv_errors(), true));
	}

	// Create courses array and populate it with data
	$electives = array();
	while($queryRow = sqlsrv_fetch_array($courses, SQLSRV_FETCH_NUMERIC)){
		array_push($electives, $queryRow[0]);
	}

	sqlsrv_close($conn);
	return $electives;
}

// Trenton
// 4/14/23
// Returns the elective courses for a selected elective
function getElectiveCourses($electiveName){
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Execute the SQL statement
	$sql = "SELECT CRS_ID, CRS_NAME
			FROM   ELECTIVE_OPTION
			JOIN   COURSE ON (OPTION_CRS = CRS_ID)
			WHERE  OPTION_NAME = ?
			ORDER BY CRS_ID;";
	$courses = sqlsrv_query( $conn, $sql, array(&$electiveName));
	if($courses === false){
		die( print_r( sqlsrv_errors(), true));
	}

	// Create courses array and populate it with data
	$electives = array();
	while($queryRow = sqlsrv_fetch_array($courses, SQLSRV_FETCH_ASSOC)){
		array_push($electives, new CourseIDandNumber($queryRow['CRS_ID'], $queryRow['CRS_NAME']));
	}

	sqlsrv_close($conn);
	return $electives;
}

// Trenton
// 4/16/23
// Returns the elective course numbers for a selected elective
function getElectiveCourseNumbers($electiveName){
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Execute the SQL statement
	$sql = "SELECT CRS_ID
			FROM   ELECTIVE_OPTION
			JOIN   COURSE ON (OPTION_CRS = CRS_ID)
			WHERE  OPTION_NAME = ?;";
	$courses = sqlsrv_query( $conn, $sql, array(&$electiveName));
	if($courses === false){
		die( print_r( sqlsrv_errors(), true));
	}

	// Create courses array and populate it with data
	$electives = array();
	while($queryRow = sqlsrv_fetch_array($courses, SQLSRV_FETCH_NUMERIC)){
		array_push($electives, $queryRow[0]);
	}

	sqlsrv_close($conn);
	return $electives;
}


// James Jing
// 4/22/23
// Get te bool value of the administer
function getAdmin($userName) {
    // Connect to the database
    $con = sqlsrv_connect(SERVER_NAME, CONNECTION_INFO);
    if ($con === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Execute the SQL statement
    $sql = "SELECT USER_IsAdminister
            FROM [USER]
            WHERE USER_ID = ?;";
    $admin = sqlsrv_query($con, $sql, array(&$userName));
    if ($admin === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Fetch the result and return the admin status
    $result = sqlsrv_fetch_array($admin);
    $adminStatus = $result['USER_IsAdminister'];

    // Close the database connection and return the admin status
    sqlsrv_close($con);
    return $adminStatus;
}

// Jacob George
// 4/25/23
// Get the user's uncompleted classes
function getUserUncompletedCoursesv2($userID, $desiredMajor) 
{
	// Connect to the database
	$con = sqlsrv_connect(SERVER_NAME, CONNECTION_INFO);
	if ($con === false) {
		die(print_r(sqlsrv_errors(), true));
	}

	// Execute the SQL statement
	$sql = "{call getUncompletedCourses2(?, ?)}";
	//{call YourStoredProcedureName(?, ?)}
	$stmt = sqlsrv_query($con, $sql, array(&$userID, $desiredMajor));
	if ($stmt === false) {
		die(print_r(sqlsrv_errors(), true));
	}

	// Create courses array and populate it with data
	do {
		$coursesData = array();
		while($queryRow = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
			array_push($coursesData, new CourseSelection($queryRow['CRS_ID'], 
														$queryRow['CRS_NAME'], 
														$queryRow['CRS_CREDITS_COUNT'], 
														$queryRow['CRS_FALL'], 
														$queryRow['CRS_SPRING'],
														$queryRow['SE_COMPLETED'],
														$queryRow['SE_GRADE'],
														$queryRow['POS_EXPECTED_SEMESTER']));
		}
	} while (sqlsrv_next_result($stmt));

	// $print_r();
	sqlsrv_close($con);
	return $coursesData;
}

// James Jing
// 4/26/23
// Get the data of the degree change request
function getDegreeChangeRequests() {
    // Connect to the database
    $con = sqlsrv_connect(SERVER_NAME, CONNECTION_INFO);
    if ($con === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Execute the SQL statement
    $sql = "SELECT dcr.USER_ID,
                   (SELECT id.DEG_NAME
                    FROM   DEGREE AS id
                    JOIN   [USER] AS iu ON (iu.DEG_ID = id.DEG_ID)
                    WHERE  dcr.USER_ID = iu.USER_ID) AS currMajor,
                   d.DEG_NAME AS newMajor
            FROM  [DEGREE CHANGE REQUEST] AS dcr
            JOIN  [USER] AS u ON (u.USER_ID = dcr.USER_ID)
            JOIN  DEGREE AS d ON (d.DEG_ID  = dcr.DEG_ID)
			WHERE DCR_RESOLVED = 'N';";
    $request = sqlsrv_query($con, $sql);
    if ($request === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Fetch the results and return them as an array
    $results = array();
    while ($row = sqlsrv_fetch_array($request, SQLSRV_FETCH_ASSOC)) {
        $results[] = $row;
    }

    // Close the database connection and return the results
    sqlsrv_close($con);
    return $results;
}

// Trenton
// 4/24/23
// Gets all of the electives of a particular major
function getMajorElectives($degree){
	// Connect to the database
    $conn = sqlsrv_connect(SERVER_NAME, CONNECTION_INFO);
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

	$degreeID = degreeID($degree);

    // Execute the SQL statement
	$sql = "SELECT p.CRS_ID FROM [PLAN OF STUDY] AS p 
			JOIN COURSE AS c ON (c.CRS_ID = p.CRS_ID)
			WHERE c.CRS_ID IN (
				SELECT ELECT_NAME FROM ELECTIVES
			)
			AND p.DEG_ID = ?;";
	$queryElectives = sqlsrv_query( $conn, $sql, array(&$degreeID));
	if($queryElectives === false){
		die( print_r( sqlsrv_errors(), true));
	}

	// Create courses array and populate it with data
	$electives = array();
	while($queryRow = sqlsrv_fetch_array($queryElectives, SQLSRV_FETCH_NUMERIC)){
		array_push($electives, $queryRow[0]);
	}

	sqlsrv_close($conn);
	return $electives;
}

// Trenton
// 4/24/23
// Checks if a given elective number is null for the elective and user
function checkElectiveNull($course, $user){
	$checkNull = false;

	// Connect to the database
    $conn = sqlsrv_connect(SERVER_NAME, CONNECTION_INFO);
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

	$num = $course->getNumber();
	$name = $user->getUsername();

	// Execute the SQL statement
	$sql = "SELECT UE_COURSE
			FROM   USER_ELECTIVES
			WHERE  UE_ELECT = ?
			AND    UE_USER  = ?;";

	$queryElectives = sqlsrv_query( $conn, $sql, array(&$num, $name));
	if($queryElectives === false){
		die( print_r( sqlsrv_errors(), true));
	}

	// Create courses array and populate it with data
	while($queryRow = sqlsrv_fetch_array($queryElectives, SQLSRV_FETCH_NUMERIC)){
		if(is_null($queryRow[0])){
			$checkNull = true;
		}
	}

	sqlsrv_close($conn);
	return $checkNull;
}

// Trenton
// 2/24/23
// Pull course data into an array
function courseDataWSemester($UserMajor) {
	include_once('./model/Course.php');
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Get the degree ID from the db
	$degreeId = degreeID($UserMajor);

	// Execute the SQL statement
	$sql = "SELECT    C.CRS_ID, CRS_NAME, CRS_CREDITS_COUNT, CRS_FALL, CRS_SPRING, POS_EXPECTED_SEMESTER
			FROM      COURSE AS C
			JOIN      [PLAN OF STUDY] AS P ON C.CRS_ID = P.CRS_ID
			WHERE     P.DEG_ID = ?
			ORDER BY  POS_EXPECTED_SEMESTER";
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
													 null,
													 null,
													 $queryRow['POS_EXPECTED_SEMESTER']));
	}

	sqlsrv_close($conn);
	return $coursesData;
}

function getUserSubmission(){
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Execute SQL Statement
	$sql = "SELECT ";
}

?>