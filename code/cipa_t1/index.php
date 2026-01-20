<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    //importa os models
    require_once __DIR__ . "/models/Funcionario.php";
    require_once __DIR__ . "/models/BrancoOuNulo.php";
    require_once __DIR__ . "/models/Candidato.php";

    // Se não estiver logado e não estiver na página de login, redirecionar
    $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    if (!isset($_SESSION['funcionario_logado']) && $url !== '/code/cipa_t1/login') {
        header("Location: /code/cipa_t1/login");
        exit;
    }

    include "./routes.php";    
?>

