<?php
    // Trenton Winkler
    // 3/22/2023
    // Holds the course data to be displayed.
    
    class CourseIDandNumber{
        protected $number;
        protected $name;

        // Constructor
        public function __construct(string $number, 
                                    string $name) {
            $this->number = $number;
            $this->name = $name;
        }

        // Get methods
        function getNumber(){
            return $this->number;
        }

        function getName(){
            return $this->name;
        }
    }

?>