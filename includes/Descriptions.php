<?php
    include_once('../DatabaseConnection.php');

    class Descriptions {

        // Jacob George
        // 3/11/2023
        // Parse the prereqs of a class into a sentance.
        function parseDescripts($courseNumber) 
        {

            $description = '';

            // Get the array of prereqs from the database
            $dbConnection = new DatabaseConnection();
            $prereqs = $dbConnection->getClassPrereqs($courseNumber);

            // Check if this class has prereqs
            if(count($prereqs) === 0)
            {
                $prereqsString = "this class has no prerequisites";
            }
            else
            {
                // Loop through all the prereqs
                $lastOptionNumber = 0;
                foreach($prereqs as $prereq)
                {
                    $prereqString = '';

                    // Determin if this is a new option or the same option
                    if($lastOptionNumber != 0)
                    {
                        if($lastOptionNumber == $prereq['CR_OPTION_NUMBER'])
                        {
                            $prereqString = " and ";
                            $lastOptionNumber = $prereq['CR_OPTION_NUMBER'];
                        }
                        elseif($lastOptionNumber < $prereq['CR_OPTION_NUMBER'])
                        {
                            $prereqString = " or ";
                            $lastOptionNumber = $prereq['CR_OPTION_NUMBER'];
                        }
                    }
                    else
                    {
                        $lastOptionNumber = $prereq['CR_OPTION_NUMBER'];
                    }

                    // Parse the prereq
                    if($prereq['CR_PREREQUICITE'] != null)
                    {
                        if($prereq['CR_CURRENT_ENROLLMENT'] === 'Y')
                        {
                            $prereqString .= "current enrollment or completion of " . $prereq['CR_PREREQUICITE'];
                        }
                        else
                        {
                            $prereqString .= $prereq['CR_PREREQUICITE'];
                        }

                        if($prereq['CR_COURSE_GRADE'] != null)
                        {
                            $prereqString .= " with a passing grade of " . $prereq['CR_COURSE_GRADE'];
                        }
                    }
                    elseif($prereq['CR_YEAR'] != null)
                    {
                        $prereqString .= "must be classified as a " . $prereq['CR_YEAR'] . " or above";
                    }
                    else
                    {
                        $prereqString .= 'EMPTY';
                    }
                    
                    // Append this prereq to the rest
                    $prereqString .= "<br>";
                    $prereqsString .= $prereqString;
                }
            }

            return ucfirst($prereqsString);
        }



    }

?>