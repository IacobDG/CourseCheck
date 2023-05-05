<?php
    // Trenton Winkler
    // 2/18/2023
    // Extends the User class to also hold the password for when the user registers.

    // Includes
    include_once('User.php');

    class UserRegistration extends User {
        private $password;

        // Constructor
        public function __construct($username, $password, $classification, $major) {
            $this->username = $username;
            $this->password = $password;
            $this->classification = $classification;
            $this->major = $major;
        }

        // Get methods
        public function getPassword() {
            return $this->password;
        }
    }

?>