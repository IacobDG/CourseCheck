<?php
    // Trenton Winkler
    // 3/7/23
    // Sorts the courses for how they will be displayed

    // Includes
    include_once('./model/CourseSelect.php');
    include_once('RetrieveData.php');

    // Constants
    define("DEFAULT_CREDIT_LIMIT", 17);

    class OrganizeCourses{
        // Trenton Winkler
        // 3/11/23
        // Oranizes the courses for the course selection page
        function organizeSelectionCourses(array $courses){
            $courses = $this->priorityLevel($courses);
            $courses = $this->splitByExpectedSemester($courses);
            // $courses = $this->splitByCredit($courses);
            $courses = $this->sortCourseNumber($courses);

            return $courses;
        }

        // Trenton Winkler
        // 3/11/23
        // Oranizes the courses for the desired POS page
        function organizeDesiredPOSCourses(array $courses, string $year, string $startingSemester){
            //$courses = $this->priorityLevel($courses);
            $courses = $this->splitByCredit($courses);
            //$courses = $this->sortByPrereqs($courses, $year, $startingSemester);

            return $courses;
        }

        // Trenton Winkler
        // 3/14/23
        // Sorts an array of courses by priority level
        private function priorityLevel(array $courses){
            usort($courses, function ($course1, $course2){
                $validator = 0;

                if     ($course1->getPriority() > $course2->getPriority()){
                    $validator =  1;
                }elseif($course1->getPriority() < $course2->getPriority()){
                    $validator = -1;
                }

                return $validator;
            });
            
            return $courses;
        }

        // Trenton Winkler
        // 3/14/23
        // Adds the credit hours of an array of courses
        private function sumCredits($courses){
            $creditTotal = 0;

            if(!is_null($courses)){
                foreach($courses as $course){
                    $creditTotal += $course->getCreditHours();
                }
            }

            return $creditTotal;
        }

        // Trenton Winkler
        // 3/18/23
        // Breaks the array of courses to the max credit count
        private function splitByCredit(array $courses, int $maxCredit = DEFAULT_CREDIT_LIMIT){
            $semesters       = array(); // Array of arrays that holds every semester of courses
            $currentSemester = 1;       // Index and current semester being pushed to
                                        // And of course it starts at 1 because Humans Never Count From Zero

            // Adds a new semester every time the credit limit is reached
            $semesters[$currentSemester - 1] = array();
            foreach($courses as $course){
                if($this->sumCredits($semesters[$currentSemester - 1]) + $course->getCreditHours() <= $maxCredit){
                    array_push($semesters[$currentSemester - 1], $course);
                }else{
                    $semesters[$currentSemester++] = array();
                    array_push($semesters[$currentSemester - 1], $course);
                }
            }

            return $semesters;
        }

        // Jacob George
        // 4/25/23
        // Breaks the array of courses to the semesters
        public function splitByExpectedSemester(array $courses){
            $semesters       = array(); // Array of arrays that holds every semester of courses
            $currentSemester = 1;       // Index and current semester being pushed to
                                        // And of course it starts at 1 because Humans Never Count From Zero

            // Adds a new semester every time the credit limit is reached
            $semesters[$currentSemester - 1] = array();
            $lastIndex = $courses[0]->getPriority();
            foreach($courses as $course){
                if($lastIndex == $course->getPriority()){
                    $lastIndex = $course->getPriority();
                    array_push($semesters[$currentSemester - 1], $course);
                }else{
                    $lastIndex = $course->getPriority();
                    $semesters[$currentSemester++] = array();
                    array_push($semesters[$currentSemester - 1], $course);
                }
            }

            return $semesters;
        }


        // Trenton Winkler
        // 3/14/23
        // Sorts the courses alphebetically by the course number
        private function sortCourseNumber(array $courses){
            $currentSemester = 1; // Index and current semester being pushed to
                                  // And of course it starts at 1 because Humans Never Count From Zero

            foreach($courses as $semester){
                usort($semester, function($course1, $course2){
                    return strcmp($course1->getNumber(), $course2->getNumber());
                });
            }

            return $courses;
        }

        // Trenton Winkler
        // 3/18/23
        // Breaks the array of courses by the priority level
        private function splitByPriority(array $courses){
            $semesters       = array(); // Array of arrays that holds every semester of courses
            $currentSemester = 1;       // Index and current semester being pushed to
                                        // And of course it starts at 1 because Humans Never Count From Zero

            // Adds a new semester every time the credit limit is reached
            // $semesters[$currentSemester - 1] = array();
            // foreach($courses as $course){
            //     // $currentSemester = $course->getPriority();
            //     echo '<br>Current Semester: '.$currentSemester; 
            //     if($course->getPriority() === $currentSemester){
            //         echo 'if';
            //         array_push($semesters[$currentSemester - 1], $course);
            //     }else{
            //         echo 'else';
            //         $semesters[$currentSemester++ - 1] = array();
            //         array_push($semesters[$currentSemester - 1], $course);
            //     }
            // }

            $semesters[0] = array();
            foreach($courses as $course){
                if(!is_null($semesters[$course->getPriority() - 1]))
                    array_push($semesters[$course->getPriority() - 1], $course);
                else{
                    $semesters[$course->getPriority() - 1] = array();
                    array_push($semesters[$course->getPriority() - 1], $course);
                }
            }
            return $semesters;
        }

        // Jacob George
        // 3/25/23
        // Sorts courses in the semesters by their prereqs
        private function sortByPrereqs($semesters, $userYear, $userJoiningSemester)
        {

            $userSemesterNumber = 0;
            switch($userYear)
            {
                case 'senior':
                    if($this->getFallOrSpring() === $userJoiningSemester)
                    {
                        $userSemesterNumber = 6;
                    }
                    else
                    {
                        $userSemesterNumber = 7;
                    }
                    break;
                case 'junior':
                    if($this->getFallOrSpring() === $userJoiningSemester)
                    {
                        $userSemesterNumber = 4;
                    }
                    else
                    {
                        $userSemesterNumber = 5;
                    }
                    break;
                case 'sophomore':
                    if($this->getFallOrSpring() === $userJoiningSemester)
                    {
                        $userSemesterNumber = 2;
                    }
                    else
                    {
                        $userSemesterNumber = 3;
                    }
                    break;
                case 'freshman':
                    if($this->getFallOrSpring() === $userJoiningSemester)
                    {
                        $userSemesterNumber = 0;
                    }
                    else
                    {
                        $userSemesterNumber = 1;
                    }
                    break;
            }
            
               
            for($semester = 0; $semester < sizeof($semesters); $semester++)
            {
                $coursesToRemove = array();
                for($course = 0; $course < sizeof($semesters[$semester]); $course++)
                {
                
                    // Check if this class has a prereq
                    $classPrereqs = getClassPrereqs($semesters[$semester][$course]->getNumber());
                    if($classPrereqs != null)
                    {
                        
                        foreach($classPrereqs as $prereq)
                        {
                            if($prereq['CR_PREREQUICITE'] != null)
                            {
                                
                                // Find the index of the prereq in the array of semesters
                                for($req_semester = 0; $req_semester < sizeof($semesters); $req_semester++)
                                {
                                    for($req_course = 0; $req_course < sizeof($semesters[$req_semester]); $req_course++)
                                    {
                                        if($semesters[$req_semester][$req_course]->getNumber() === $prereq['CR_PREREQUICITE'])
                                        {
                                           
                                            if($semester <= $req_semester)
                                            {

                                                if($semester == 0)
                                                {
                                                    $newArray = array();
                                                    array_unshift($semesters, $newArray);

                                                    array_unshift($semesters[$semester], $semesters[$req_semester + 1][$req_course]);
                                                    array_splice($semesters[$req_semester + 1], $req_course, 1);
                                                }
                                                else{
                                                    // Move the prereq from its position to the semeter before the course.
                                                    array_unshift($semesters[$semester - 1], $semesters[$req_semester][$req_course]);
                                                    array_splice($semesters[$req_semester], $req_course, 1);
                                                }
                                               
                                               
                                            }
                                            
                                        }
                                    }
                                } 
                            } 
                            elseif($prereq['CR_YEAR'] != null)
                            {
                                

                                // This doesnt work right
                                switch($prereq['CR_YEAR'])
                                {
                                    case 'senior':
                                        $semesterOffset = 6 - $userSemesterNumber - $semester;
                                        break;
                                    case 'junior':
                                        $semesterOffset = 4 - $userSemesterNumber - $semester;
                                        break;
                                    case 'sophomore':
                                        $semesterOffset = 2 - $userSemesterNumber - $semester;
                                        break;
                                    case 'freshman':
                                        $semesterOffset = 0 - $userSemesterNumber - $semester;
                                        break;
                                }
                                //echo $semesters[$semester][$course]->getName() . "<br>";
                                //echo '$prereq["CR_YEAR"]: ' . $prereq['CR_YEAR'] . '<br>';
                                //echo '$semester: ' . $semester . '<br>';
                                //echo '$userSemesterNumber: ' . $userSemesterNumber . '<br>';
                                //echo '$semesterOffset: '. $semesterOffset . '<br><br>'; 
                                
                                
                                // move the $semesters[$semester][$course] to the appropriate $semesters[$semester]
                                // Move the prereq from its position to the semeter before the course.
                                if($semesterOffset > 0)
                                {
                                    //echo "entered<br>";
                                    if(sizeof($semesters) <= $semesterOffset + $semester)
                                    {
                                        for($i=sizeof($semesters) ;$i <= $semesterOffset + $semester; $i++)
                                        {
                                            $semesters[$i] = array();
                                        }
                                        
                                        //echo "Added Semester of intex " . $semesterOffset + $semester . "<br>";
                                    }

                                    
                                   
                                    array_unshift($coursesToRemove, $course);
                                    //$coursesToRemove[$index] += $course;
                                    
                                    array_unshift($semesters[$semesterOffset + $semester], $semesters[$semester][$course]);
                                    //array_splice($semesters[$semester], $course, 1);
                                    
                                    //unset($semesters[$semester][$course]);
                                    //array_diff($semesters[$semester], [$course]);
                                }
                                
                                
                            } 
                        }
 
                        //echo '<pre>';
                        //print_r($semesters);
                        //echo '</pre>';
                    }
                   

                }
                //echo '<pre>';
                //print_r($coursesToRemove);
                //echo '</pre>';
                for($dcourse = 0; $dcourse < sizeof($coursesToRemove); $dcourse++)
                {
                    //echo $coursesToRemove[$dcourse] . "<br>";
                    array_splice($semesters[$semester], $coursesToRemove[$dcourse], 1);
                }
                
            
            } 


            return $semesters;
        }

        private function getFallOrSpring()
        {
            $month = (int)date('m');
            if($month > 5)
                return 'Fall';
            else
                return 'Spring';
        }
    }

?>