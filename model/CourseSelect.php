<?php
    // Trenton Winkler
    // 3/3/2023
    // Course data for selection page.

    // Includes
    include_once('course.php');
    include_once('./includes/ModifyData.php');
    
    class CourseSelection extends Course
    {        
        var $completed;
        var $grade;
        var $priority;

        public function __construct(string $number, 
                                    string $name, 
                                    string $creditHours, 
                                    string $fallAvailability, 
                                    string $springAvailability,
                                    ?string $completed,
                                    ?string $grade,
                                    ?int    $priority) 
        {
            $this->number = $number;
            $this->name = $name;
            $this->creditHours = $creditHours;
            $this->fallAvailability = $fallAvailability;
            $this->springAvailability = $springAvailability;
            $this->completed = $completed;
            $this->grade = $grade;
            $this->priority = $priority;
        }

        // Get methods
        function getCompleted($user)
        {
            $isComplete = false;

            if($this->completed === 'Y')
            {
                $isComplete = true;
            }
            else if (autoCheck($user, $this->number))
            {
                $isComplete = true;
            }
            
            return $isComplete;
        }

        function getGrade(){
            return $this->grade;
        }

        function getPriority(){
            return $this->priority;
        }

        // Set methods
        function setCompleted(string $completed){
            $this->completed = $completed;
        }

        function setGrade(String $grade){
            $this->grade = $grade;
        }

        function setPriority(int $priority){
            $this->priority = $priority;
        }
    }
?>