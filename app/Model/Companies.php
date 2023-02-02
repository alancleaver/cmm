<?php

    namespace App\Model;

    class Companies implements \Iterator, \Countable {

        protected $position = 0;
        protected $ids = [];
        protected $db;
    
        private $randomize = false;
        private $postcodes = [];
        private $limit = 0;
        private $bedrooms = [];
        private $active_only = true;
        private $types = [];
        private $in_credit_only = 1;

        public function __construct(\PDO $db = null)
        {
            $this->db = $db;
        }

        //Call this is we change the filtering, etc
        private function rePopulate() : void 
        {
            $this->position = 0;
            $this->ids = [];
        }

        protected function populate() 
        {
            if (empty($this->ids)) {

                //Create SQL from filters extra
                $sql = 'SELECT id FROM companies';

                $where_clauses = [];
                if (!empty($this->postcodes)) {
                    $where_clauses[] = ' postcode IN ("'.implode('", "', $this->postcodes).'")';
                }
                if (!empty($this->bedrooms)) {
                    $where_clauses[] = ' bedroom IN ('.implode(', ', $this->bedrooms).')';
                }
                if (!empty($this->types)) {
                    $where_clauses[] = ' type IN ('.implode(', ', $this->types).')';
                }

                if (!empty($where_clauses)) {

                    $sql .= ' INNER JOIN company_survey_types ON companies.id = company_survey_types.company_id_fk 
                                WHERE ('.implode(' AND ', $where_clauses).') ';

                    if ($this->in_credit_only) {
                        $sql .= ' AND credits > 0 ';
                    }
                } else {
                    if ($this->in_credit_only) {
                        $sql .= ' WHERE credits > 0 ';
                    }
                }

                if ($this->randomize) {
                    $sql .= ' ORDER BY RAND()';
                }

                if ($this->limit > 0) {
                    $sql .= ' LIMIT '.$this->limit;
                }
                $stmt = $this->db->prepare($sql);
                $stmt->execute();

                $this->ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            }
        }
  
        public function inActiveOnly()
        {
            $this->$active_only = false;
            $this->rePopulate();
            return $this;
        }

        public function limit(int $limit) 
        {
            $this->limit = $limit;
            $this->rePopulate();
            return $this;
        }

        public function randomize() 
        {
            $this->randomize = true;
            $this->rePopulate();
            return $this;
        }

        public function filterInCreditOnly() 
        {
            $this->in_credit_only = 1;
            $this->rePopulate();
            return $this;
        }

        public function filterByPostcode(array $outward_postcodes) 
        {
            $this->postcodes = $outward_postcodes;
            $this->rePopulate();
            return $this;
        }

        public function filterByBedrooms(array $bedrooms) 
        {
            $this->bedrooms = $bedrooms;
            $this->rePopulate();
            return $this;
        }

        public function filterByTypes(array $types)
        {
            $this->types = $types;
            $this->rePopulate();
            return $this;
        }


        public function rewind(): void
        {
            $this->populate();
            $this->position = 0;
        }
    
        public function key(): int
        {
            $this->populate();
            return $this->position;
        }
    
        public function next(): void
        {
            $this->populate();
            ++$this->position;
        }
    
        public function valid(): bool
        {
            $this->populate();
            return isset($this->ids[$this->position]);
        }  
    
        public function current(): Company
        {
            $this->populate();
            return new Company($this->db, $this->ids[$this->position]);
        }
    
        public function count(): int
        {
            $this->populate();
            return count($this->ids);
        }
  
    }