<?php
    // Trenton Winkler
    // 2/18/2023
    // Holds the course data to be displayed.
    
    class UpdatedCourse{
        protected $number;
        protected $grade;
        protected $completed;

        // Constructor
        public function __construct(string $number, string $grade, string $completed) 
        {
            $this->number = $number;
            $this->grade = $grade;
            $this->completed = $completed;
        }

        // Get methods
        function getNumber()
        {
            return $this->number;
        }

        function getGrade()
        {
            return $this->grade;
        }
        function getCompleted()
        {
            return $this->completed;
        }
    }

?>