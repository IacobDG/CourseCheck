<?php
    // Start the session
    session_start();

    // Includes
    include_once('includes/RetrieveData.php');
    include_once('includes/ModifyData.php');
    include_once('model/CourseIDandNumber.php');

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
    <title>Admin Form</title>
    <link rel="stylesheet" type="text/css" href="css/admin.css">
</head>
<body>
    <div class="wrapper">
        <img src="img/Course_Check_Logov22-3-03.png" class="logo">
        <h1>Admin Portal</h1>
        <!-- <a href="changeMajorForm.php"><input  class="btn-major-change solid" value="Go to Major Change"/></a> -->
        <a href="logout.php"><input type="submit" value="Logout" onclick=""  class="btn-logout solid"></a>
    </div>
    <?php
    // James Jing
    //4/26/23
    // Include the file containing the function definition
    require_once 'includes/RetrieveData.php';
     // Notification message variables
     $acceptMsg = "";
     $rejectMsg = "";

    // Call the function to retrieve degree change request information
    $results = getDegreeChangeRequests();

    // Display the results in a table format
    echo "<table>";
    echo "<tr>
         <caption> Student Major Change Request</caption>
          <th>User ID</th>
          <th>Current Major</th>
          <th>New Major</th>
          <th>Accept/Reject</th></tr>";
    
    // Check if there is any data to display
    if (!empty($results)) {
        foreach ($results as $result) {
            echo "<tr>";
            echo "<td>" . $result['USER_ID'] . "</td>";
            echo "<td>" . $result['currMajor'] . "</td>";
            echo "<td>" . $result['newMajor'] . "</td>";
            echo "<td> 
                    <form method='POST'> 
                         <button name='accept-request' value='".$result['USER_ID']."-accept'>Accept</button>
                        <button name='reject-request' value='".$result['USER_ID']."-reject'>Reject</button>
                    </form></td>";       
            echo "</tr>";
        }
    } else {
        // Show an empty row if there is no data
        echo "<tr><td colspan='4'>No data to display, because students did not submit any major change request</td></tr>";
    }

    echo "</table>";
    // Check if the user has clicked on the accept or reject button
    if (isset($_POST['accept-request']) || isset($_POST['reject-request'])) {
        // Extract the user ID and action from the button's value attribute
        $buttonValue = explode("-", $_POST['accept-request'] ?? $_POST['reject-request']);
        $userId = $buttonValue[0];
        $action = $buttonValue[1];
        // Call the function to process the request
        if ($action == "accept") {
            if (processDegreeChangeRequest($userId, $action)) {
                $_SESSION['acceptMsg'] = "Accepted successfully.";
            } else {
                $_SESSION['rejectMsg'] = "Error accepting the request.";
            }
        } elseif ($action == "reject") {
            if (processDegreeChangeRequest($userId, $action)) {
                $_SESSION['rejectMsg'] = "Rejected successfully.";
            } else {
                $_SESSION['rejectMsg'] = "Error rejecting the request.";
            }
        }
        echo '<meta http-equiv="refresh" content="0.5; url=admin.php?refresh=true">';
    }     
?>

<?php
    // Display the notification messages
    if (!empty($_SESSION['acceptMsg'])) {
        echo "<div id='notification-container' class='success show'>{$_SESSION['acceptMsg']}</div>";
        unset($_SESSION['acceptMsg']);
    } elseif (!empty($_SESSION['rejectMsg'])) {
        echo "<div id='notification-container' class='error show'>{$_SESSION['rejectMsg']}</div>";
        unset($_SESSION['rejectMsg']);
    }
?>
   
    <div class="toolbar">
                    <button onclick = "window.location.href='electiveManagement.php';">Elective-Manage</button>
                    <button onclick = "window.location.href='courseManagement.php';">Course-Manage</button>
                </div>
    
               
</body>
</html>