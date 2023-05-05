<?php
    @include_once('ModifyData.php'); 
    // Jacob George
    if(isset($_GET["clicked"]))
    {
        @$courseNumber = $_GET["clicked"];
        @$username = $_GET["username"];
        @$value = $_GET["value"];
        $isChecked = "";
        
        if($value === "true")
            $isChecked = "Y";
        else
            $isChecked = "N";
        
        updateEnrolledClass($courseNumber, $username, $isChecked);
    }
?>