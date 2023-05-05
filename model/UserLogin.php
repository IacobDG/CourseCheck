<?php
    // Trenton Winkler
    // 2/18/2023
    // Holds the username and password to be given to the database at login.

    class UserLogin {
        private $username;
        private $password;

        // Constructor
        public function __construct($username, $password) {
            $this->username = $username;
            $this->password = $password;
        }

        // Get methods
        public function getUsername() {
            return $this->username;
        }
        
        public function getPassword() {
            return $this->password;
        }
    }

?>