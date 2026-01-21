<?php namespace Config;

    class JRequest{
        private $Controller,
                $Method,
                $Argument;

        public function __construct(){
            if (isset($_GET["url"])){
                $Path = filter_input(INPUT_GET,"url", FILTER_SANITIZE_URL);
                $Path = explode("/",$Path);
                $Path = array_filter($Path);

                if($Path[0]=="index.php"){
                    $this->Controller="Home";
                }else{
                    $this->Controller=array_shift($Path);
                }

                $this->Method = array_shift ($Path);
                if(!$this->Method){
                    $this->Method="index";
                }

                $this->Argument = ($Path);
                if(!$this->Argument){
                    $this->Argument=array("");
                    //echo json_encode($Path);
                }
            } else {
                $this->Controller="Home";
                $this->Method="index";
            }
        }

        public function getController(){
            return $this->Controller;
        }

        public function getMethod(){
            return $this->Method;
        }
        
        public function getArgument(){
            return $this->Argument;

        }
    }
?>