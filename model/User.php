<?php
    // Trenton Winkler
    // 2/18/2023
    // Holds the user that will be carried throughout the application.

    class User {
        protected $username;
        protected $classification;
        protected $major;
        protected $semester;

        protected $isAdminister;
    
        // Constructor
        public function __construct($username, $classification, $major, $semester, $isAdminister = 'N') {
            $this->username = $username;
            $this->classification = $classification;
            $this->major = $major;
            $this->semester = $semester;
            $this->isAdminister = $isAdminister;
        }
    
        // Get methods
        public function getUsername() {
            return $this->username;
        }
    
        public function getClassification() {
            return $this->classification;
        }
    
        public function getMajor() {
            return $this->major;
        } 

        public function getSemester() {
            return $this->semester;
        }

        public function getAdminValue(){
            return $this->isAdminister;
        }
    }

?>