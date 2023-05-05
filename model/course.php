<?php
    // Trenton Winkler
    // 2/18/2023
    // Holds the course data to be displayed.
    
    class Course{
        protected $number;
        protected $name;
        protected $creditHours;
        protected $fallAvailability;
        protected $springAvailability;

        // Constructor
        public function __construct(string $number, 
                                    string $name, 
                                    string $creditHours, 
                                    string $fallAvailability, 
                                    string $springAvailability) {
            $this->number = $number;
            $this->name = $name;
            $this->creditHours = $creditHours;
            $this->fallAvailability = $fallAvailability;
            $this->springAvailability = $springAvailability;
        }

        // Get methods
        function getNumber(){
            return $this->number;
        }

        function getName(){
            return $this->name;
        }

        function getCreditHours(){
            return $this->creditHours;
        }

        function getFallAvailability(){
            return $this->fallAvailability;
        }

        function getSpringAvailability(){
            return $this->springAvailability;
        }
    }

?>