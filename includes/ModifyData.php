<?php
// Jacob George
// 2/18/2023
// Every function that will create, update, or delete 
// data in the database will be held in this file.

// Includes
spl_autoload('autoLoader');

include_once('DatabaseConstants.php');
include_once('RetrieveData.php');
// include_once('./model/MajorChangeRequest.php');
// include_once('./model/UpdatedCourse.php');

// Lujia
// 2/24/23
// Inserting username, password, major, and classification
function userRegistrationInsert($registeredUser){
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
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

// Add a new user to the database
function addNewUser($userName, $password, $degreeID, $year, $semester, $IsAdminValue = 'N') 
{
	// echo '<script>console.log("2. add new user has been called")</script>';
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);   

	// Execute the SQL statement
	$sql = "INSERT INTO [USER](USER_ID, USER_PASSWORD, DEG_ID, USER_YEAR, USER_SEMESTER, USER_IsAdminister)
			VALUES (?, ?, ?, ?, ?, ?);";
	$stmt = sqlsrv_prepare($conn, $sql, array(&$userName, &$password, $degreeID, $year, $semester, $IsAdminValue));

	if (sqlsrv_execute( $stmt ) === false) 
	{
		echo '<script>console.log("Row insertion failed")</script>'; 
		die(print_r(sqlsrv_errors(), true)); 
	} else
		echo '<script>console.log("Row successfully inserted")</script>';

	sqlsrv_free_stmt( $stmt);
	sqlsrv_close($conn);
	
}


// Trenton Winkler
// 3/6/23
// Insert user and desired major into the database
function submitMajor($majorChangeRequest){	
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Execute the SQL statement
	$sql = "DELETE FROM [DEGREE CHANGE REQUEST]
			WHERE USER_ID = ?;
			INSERT INTO  [DEGREE CHANGE REQUEST](USER_ID, DEG_ID, DCR_RESOLVED)
			VALUES (?, (select DEG_ID
						from DEGREE
						where DEG_NAME = ?), 'N')";
	$insertion = sqlsrv_query( $conn, $sql, array($majorChangeRequest->getUsername(),
												  $majorChangeRequest->getUsername(),
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

// Jacob George
// 4/28/23
// Update class enrollment
function updateEnrolledClass($courseNumber, $username, $isChecked)
{	
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Execute the SQL statement
	$sql = "UPDATE [STUDENT ENROLLMENT]
			   SET SE_COMPLETED = ?
			 WHERE USER_ID = ?
			   AND CRS_ID = ?;";
	$stmt = sqlsrv_query($conn, $sql, array($isChecked, $username, $courseNumber));

	if($stmt === false){
		die( print_r( sqlsrv_errors(), true));
	}
	sqlsrv_close($conn);
}

// Joey Foucha Jr
// 3/8/23
// Code to create entries into the student_enrollement table based on user's degree/POS
function enrollStudent(User $user)
{
	echo '<script>console.log("11. enrollStudent has been called")</script>';    
	// Connect to the database
	$conn = sqlsrv_connect(SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) 
	{
		die( print_r( sqlsrv_errors(), true));
	}

	$Course_IDs = getDegreeCourses($user->getMajor());
	$USER_ID = $user->getUsername();

	if($user->getClassification() === "freshman")
	{
		$yearIndex = 1;
	}
	else if ($user->getClassification() === "sophomore")
	{
		$yearIndex = 3;
	}
	else if ($user->getClassification() === "junior")
	{
		$yearIndex = 5;
	}
	else
	{
		$yearIndex = 7;
	}
	// Loop over the array and insert each value into the database    
	foreach ($Course_IDs as $CRS_ID) 
	{
		// Automatically enroll the student in classes of their semester and before
		$courseSQL = "SELECT POS_EXPECTED_SEMESTER
						FROM [PLAN OF STUDY] 
					   WHERE CRS_ID = ?
						 AND DEG_ID = (SELECT DEG_ID 
										 FROM DEGREE 
										WHERE DEG_NAME = ?)";
		$courseStmt = sqlsrv_query($conn, $courseSQL, array($CRS_ID, $user->getMajor()));
		sqlsrv_fetch($courseStmt);
		$expectedSemester = sqlsrv_get_field($courseStmt, 0);
		if ($expectedSemester <= $yearIndex)
		{
			$completedFlag = 'Y';
		}
		else
		{
			$completedFlag = 'N';
		}

		// Prepare the statement
		$sql = ("INSERT INTO [STUDENT ENROLLMENT] (USER_ID, CRS_ID, SE_COMPLETED, SE_GRADE) 
				 VALUES      (?, ?, ?, ?);");
		$stmt = sqlsrv_prepare($conn, $sql, array($USER_ID, $CRS_ID, $completedFlag, '-'));

		if (sqlsrv_execute($stmt) === false) 
		{
			echo '<script>console.log("Row insertion failed")</script>'; 
			die(print_r(sqlsrv_errors(), true)); 
		} 
		else
		{
			// echo '<script>console.log("Row successfully inserted")</script>';
		}
	}
	
	// Close the statement and the connection
	sqlsrv_free_stmt($stmt);
	sqlsrv_close($conn);
}

// Joey Foucha Jr
// 3/19/23
// Code to update the user's course information
function updateCourseSelection(string $userID, array $updatedCourses)
{
	// Connect to the database
	$conn = sqlsrv_connect(SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) 
	{
		die( print_r( sqlsrv_errors(), true));
	}

	// For each course checkbox, update the grade and completion status
	foreach ($updatedCourses as $updatedCourse)
	{
		$grade = $updatedCourse->getGrade();
		$crsNumber = $updatedCourse->getNumber();
		$completed = $updatedCourse->getCompleted();

		// Execute the SQL statement
		$sql = "UPDATE [STUDENT ENROLLMENT]
				SET    SE_COMPLETED = ?, SE_GRADE = ?
				WHERE  USER_ID = ? AND CRS_ID = ?;";
		
		$courseStatus = sqlsrv_query($conn, $sql, array(&$completed, $grade, $userID, $crsNumber));
		if($courseStatus === false)
		{
			die(print_r(sqlsrv_errors(), true));
		}
	}

	sqlsrv_close($conn);
}

// Trenton Winkler
// 4/14/23
// Adds an elective to the elective option table
function AddElective($elective, $course){
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Execute the SQL statement
	$sql = "INSERT INTO ELECTIVE_OPTION(OPTION_NAME, OPTION_CRS)
			VALUES (?, ?);";
	$insertion = sqlsrv_query( $conn, $sql, array($elective, $course));

	if($insertion === false){
		$successMsg = "Failed to insert";
		die( print_r( sqlsrv_errors(), true));
	} else {
		$successMsg = "Successfully inserted";
	}
   
	sqlsrv_close($conn);
	return $successMsg;
}

// Trenton
// 4/17/23
// Removes an elective from the elective option table
function RemoveElective($elective, $course){
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// Execute the SQL statement
	$sql = "DELETE FROM ELECTIVE_OPTION
			WHERE       OPTION_NAME = ?
			AND         OPTION_CRS  = ?";
	$deletion = sqlsrv_query( $conn, $sql, array($elective, $course));

	if($deletion === false){
		$successMsg = "Failed to remove";
		die( print_r( sqlsrv_errors(), true));
	} else {
		$successMsg = "Successfully removed";
	}
	
	sqlsrv_close($conn);
	return $successMsg;
}

// Trenton
// 4/17/23
// Adds a course to a major
function addCourse($degree, $course, $expectedSemester){
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	$degreeID = degreeID($degree);

	// Execute the SQL statement
	$sql = "INSERT INTO [PLAN OF STUDY](DEG_ID, CRS_ID, POS_EXPECTED_SEMESTER)
			VALUES (?, ?, ?);";
	$insertion = sqlsrv_query( $conn, $sql, array($degreeID, $course, $expectedSemester));

	if($insertion === false){
		$successMsg = "Failed to insert";
		die( print_r( sqlsrv_errors(), true));
	} else {
		$successMsg = "Successfully inserted";
	}
   
	sqlsrv_close($conn);
	return $successMsg;
}

// Trenton
// 4/17/23
// Removes a course from a major
function removeCourse($degree, $course){
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	$degreeID = degreeID($degree);

	// Execute the SQL statement
	$sql = "DELETE FROM [PLAN OF STUDY]
			WHERE       DEG_ID = ?
			AND         CRS_ID = ?";
	$deletion = sqlsrv_query( $conn, $sql, array($degreeID, $course));

	if($deletion === false){
		$successMsg = "Failed to remove";
		die( print_r( sqlsrv_errors(), true));
	} else {
		$successMsg = "Successfully removed";
	}
	
	sqlsrv_close($conn);
	return $successMsg;
}

// Trenton
// 4/24/23
// Enrolls the student with the electives from the major chosen
// Course value set to null until the user selects different
function enrollElectives($user){
	// Connect to the database
	$conn = sqlsrv_connect( SERVER_NAME, CONNECTION_INFO);
	if( $conn === false) {
		die( print_r( sqlsrv_errors(), true));
	}

	// SQL statement
	$sql = "INSERT INTO USER_ELECTIVES(UE_USER, UE_ELECT, UE_COURSE) VALUES (?, ?, null);";

	// Execute the SQL statement
	$electives = getMajorElectives($user->getMajor());
	foreach($electives as $elective){
		$insertion = sqlsrv_query( $conn, $sql, array($user->getUsername(), $elective));
	}

	if($insertion === false){
		$successMsg = "Failed to insert";
		die( print_r( sqlsrv_errors(), true));
	} else {
		$successMsg = "Successfully insert";
	}

	sqlsrv_close($conn);
	return $successMsg;
}

// James Jing
// 4/26/23
// update or delete the major change request
function processDegreeChangeRequest($userId, $action) {
    // Connect to the database
    $con = sqlsrv_connect(SERVER_NAME, CONNECTION_INFO);
    if ($con === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Execute the SQL statement based on the user's action
    if ($action == 'accept') {
		// echo '<script>console.log("   This has been called by: '.$userId.'")</script>';
        $sql = "UPDATE [DEGREE CHANGE REQUEST]
                SET DCR_RESOLVED = 'Y'
                WHERE USER_ID = ?;
				
				UPDATE [USER]
				SET DEG_ID = (SELECT DISTINCT DEG_ID
							  FROM [DEGREE CHANGE REQUEST]
							  WHERE USER_ID = ?)
				WHERE USER_ID = ?;
				";
    } else if ($action == 'reject') {
        $sql = "DELETE FROM [DEGREE CHANGE REQUEST]
                WHERE USER_ID = ?;";
    } else {
        // If an invalid action is provided, return an error message
        return "Invalid action";
    }

	$update = sqlsrv_query( $con, $sql, array(&$userId, $userId, $userId));

	if($update === false){
		// echo '<script>console.log("Failed to update")</script>';
		$successMsg = "Update to insert";
		die( print_r( sqlsrv_errors(), true));
	} else {
		// echo '<script>console.log("Success")</script>';
		$successMsg = "Successfully updated";
	}

    // Close the database connection and return a success message
    sqlsrv_close($con);
    return "Success";
}

?>