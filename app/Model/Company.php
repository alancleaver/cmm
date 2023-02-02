<?php

    namespace App\Model;

    class Company {

        private $db;
        private $id;

        private $active = 0;
        private $name = '';
        private $credits = 0;
        private $description = '';
        private $email = '';
        private $phone = '';
        private $website = '';

        function __construct(\PDO $db, int $company_id = 0) {

            $this->db = $db;

            if ($company_id) {
                $this->id = $company_id;
                $this->populate();
            }
        }

        public function getName() {
            return $this->name;
        }
        public function getActive() {
            return $this->active;
        }
        public function getCredits() {
            return $this->credits;
        }
        public function getDescription() {
            return $this->description;
        }
        public function getEmail() {
            return $this->email;
        }
        public function getPhone() {
            return $this->phone;
        }
        public function getWebsite() {
            return $this->website;
        }

        private function populate() {

            $stmt = $this->db->prepare('SELECT * FROM companies WHERE id=?');
            $stmt->execute([$this->id]);
            $row = $stmt->fetch();

            //Hopefully return an array or boolean false 
            if (!empty($row)) {

                $this->active = $row['active'];
                $this->name = $row['name'];
                $this->credits = $row['credits'];
                $this->description = $row['description'];
                $this->email = $row['email'];
                $this->phone = $row['phone'];
                $this->website = $row['website'];

            } else {
                //Company without an ID, log this? raise error maybe?
                //echo ('No company with this ID');
            }

        }


        public function decrementCredits(int $how_many = 1) {

            if ($how_many > 0) {
                
                //This could be considered overkill, but to make sure we aren't trying to decrement the credits 
                //in two threads at the same time, I'm going to do this using the database.

                $stmt = $this->db->prepare('UPDATE companies SET credits = credits - ? WHERE credits > 0 AND id = ?');
                $stmt->execute([$how_many, $this->id]);

                $this->populate();

            } else {
                //This should probably be flagged as a big error here, maybe fatal
                //A developer shouldn't allow negatives passed in here, that must be a mistake.
            }
        }

    }