<?php
    session_start();

    if (!isset($_SESSION["system"]["username"])) {
        header("Location: /Login");
        exit();
    }

    require_once "Define.php";
    require_once "Config\JRequest.php";
    require_once "Config\JRouter.php";
    require_once "Config\AutoLoad.php";

    // 1. Obtener la ruta de la solicitud actual
    $path = $_SERVER['REQUEST_URI'] ?? '/'; 

    // Limpiar la ruta de posibles parámetros de consulta (si existen)
    $path = strtok($path, '?');

    // Detectar si la ruta debe emitir PDF (evita cargar la plantilla HTML)
    $is_pdf_report = (preg_match('#^/Report(/|$)#i', $path) === 1);

    if(!isset($_SESSION["system"]["username"]) && $path !== '/Login'){
        header("Location: /Login");
        exit();
    }

    Config\AutoLoad::run();
    //Config\AutoLoad::run();

    // Incluir la plantilla solo si NO es una ruta que genera PDF
    if (!$is_pdf_report) {
        include_once "Template\index.php";
    }

    Config\JRouter::run(new Config\JRequest());
?>