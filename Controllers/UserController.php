<?php
    namespace Controllers;
    use Models\User as User;
    use Config\JInput as JInput;

    use Entity\eUser as eUser;

    Class UserController{
        private $userModel;

        // Autenticación de usuario (LOGIN)
            public function login()
            {
                if(isset($_POST['email']) && isset($_POST['password'])){

                    $email = $_POST['email'];
                    $password = $_POST['password'];

                    // Llamamos al método seguro del modelo
                    $user = $this->userModel->authenticateByEmail($email, $password);

                    if($user){
                        session_start();
                        $_SESSION['user_id'] = $user->id;
                        $_SESSION['user_email'] = $user->email;

                        header("Location: index.php");
                        exit;
                    } else {
                        return "Credenciales incorrectas";
                    }
                }
            }


        public function __construct()
        {
            $this->userModel = new User();
        }

        public function index()
        {
            return $this->userModel->toList();  
        }

        public function Registry($id)
        {
            $success = true;
            if(isset($_POST) && isset($_POST['Registrar'])){
                $user = new eUser();
                // sanitize all POST fields before assigning
                $clean = JInput::sanitizeArray($_POST);
                foreach($clean as $key => $value) {
                    $user->$key = $value;
                }

                //echo json_encode($user);
                $this->userModel->save($user);
                return $user;
            }

            $data = $this->userModel->getForId($id);

            if (!$data) {
                $data = new eUser();
                $data->id = $this->userModel->getNewId();
            }

            return $data;
        }

        // Vista solo lectura de un usuario
        public function Detalle($id)
        {
            $data = $this->userModel->getForId($id);
            if(!$data){
                $data = new eUser();
            }
            return $data;
        }
    }
?>