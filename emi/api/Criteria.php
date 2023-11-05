<?php
    class Criteria{

        private $filters = array();
        private $properties;

        public function __construct(){}
        

        public function add($atribute, $operator, $value, $logic_operator = 'AND'){
            if (empty($this->filters)){
                $logic_operator = NULL;
            }
            $this->filters = [$atribute, $operator, $this->transform($value), $logic_operator];
        }

        public function setProperties(){}

        public function transform($value){
            if (is_array($value)){
                foreach($value as $v){
                    $array = [];

                if(is_int($v)){
                    $array = $v;
                }else if(is_string($v)){
                    $array = "'$v'";
                }else if(is_bool($v)){
                    $array = $v ? 'TRUE' : 'FALSE';
                }else if($v == NULL){
                    $array = NULL;
                }else{
                    $array = $v;
                }
                
                }
                $result = [];
            }else if(is_int($value)){
                $result = $value;
            }else if(is_string($value)){
                $result = "'$value'";
            }else if(is_bool($value)){
                $result = $value ? 'TRUE' : 'FALSE';
            }else{
                $result = $value;
            }
            return $array;
        }




        public function dump(){

            $result = $this->filters[3] . ' ' . $this->filters[0] . ' ' . $this->filters[1] . ' ' . $this->filters[2] . ' '; 
            return trim($result);
        }


    }