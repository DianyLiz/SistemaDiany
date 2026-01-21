<?php 
    session_start();
    require_once '../Config/Conexion.php';
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: POST');

    if(!isset($_SESSION["system"]["username"])) {
        $user = "consultasAPI";
        $pass = "API*Data123*";

        $getUser=isset($_POST['uid'])?$_POST['uid']:'';
        $getPass=isset($_POST['pw'])?$_POST['pw']:'';

        if($getUser!=$user || $getPass!=$pass) {
            $json = array(
                'message'=>'No Autenticado'
            );

            echo json_encode($json);
            http_response_code(401);
            exit;
        }
    }

    $method = isset($_GET['method']) ? $_GET['method'] : '';

    if(empty($method)) {
        $json = array(
            'message' => 'Método no proporcionado.',
            'success' => false
        );
        echo json_encode($json);
        http_response_code(400);
        exit;
    }

    require_once "Method/$method.php"
?>