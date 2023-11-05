<?php // PHP 8.2
    abstract class Record{ // Transaction,Criteria,Logger // Zend Framework Laravel Codeigniter

        protected $data; // array (atributos)
        // arrays_keys / array values
        // $vetor = array(id => 1, 'nome'=> 'Neto');


        public function __construct($id = NULL){
            if ($id)
                $object = $this->load($id);


                // $this->get_class() :: TABLENAME;
        }


        public function __get($atr){
            if (isset($this->data[$atr]))
                return $this->data[$atr];
        }

        public function __set($atr, $value){
            if ($value == NULL)
                unset($this->data[$atr]);
            else
                $this->data[$atr] = $value;
        }

        public function __isset($atr){
            return isset($this->data[$atr]);
        }


        public function formArray(){

        }

        public function toArray(){

        }


        public function load($id){
            // carregar uma ou mais tabelas

            // 'carregar tudo' if id id === null 
            // 'carregar um só' if else

            // return fetchObject;
        }

        public function store(){
            // inserir ou atualizar dados em uma tabela

            // se tiver id -> update
            // Verificação a mais (objeto criado)
            // implode / array_keys / array_values
            
            // se não tiver id -> insert
        }

        public function delete($id){
            // excluir dados em uma tabela
        }

        public function prepare(){ 
            // a função prepare do PDO
        }

        public function escape(){ 
            // SQL Injection
        }

        public function clone(){

        } 


    }
