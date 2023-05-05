<?php
    // Joey Foucha Jr
    // 3/6/2023
    // Holds the username and desired major for a requested major change.
    
    class MajorChangeRequest {
        protected $username;
        protected $desiredMajor;
    
        // Constructor
        public function __construct($username, $major) 
        {
            $this->username     = $username;
            $this->desiredMajor = $major;
        }
    
        // Get methods
        public function getUsername() 
        {
            return $this->username;
        }
    
        public function getDesiredMajor() 
        {
            return $this->desiredMajor;
        } 
    }

?>