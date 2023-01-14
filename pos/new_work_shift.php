<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');

session_start();
if(!isset($_SESSION['user_name'])) {
    header('Location: /bread_factory/pos/login.php');
} else {
    include (MODELS_PATH."WorkShift.php");
    $work_shift = new WorkShift();
    $current_work_shift = $work_shift->check_work_shift();

    if ($current_work_shift === "empty") {
        //open new work shift
        header('Location: new_work_shift.php');
    } else {
        $is_finished = $current_work_shift->is_finished;
        if (!$is_finished) {
            //work shift opened
            header('Location: pos.php');
        }
    }
}
?>

<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" sizes="16x16 24x24 32x32 48x48 64x64" href="images/favicon.ico">

    <!-- jQuery -->
    <script type="text/javascript" src="../libs/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Styles-->
    <link rel="stylesheet" href="../css/custom.css">

    <!-- Numpad Js-->
    <link rel="stylesheet" href="../libs/numpad-js/numpad-light.css" />
    <script src="../libs/numpad-js/numpad.js"></script>

    <!-- Sweet Alert 2 -->
    <link rel="stylesheet" href="../libs/sweetalert2/css/sweetalert2.min.css" />
    <script src="../libs/sweetalert2/sweetalert2.min.js"></script>

    <!-- Font Awesome -->
    <script src="../libs/font-awesome/all.js"></script>

    <title>Bread Factory - Iniciar sesión</title>

    <style>
        a {
            text-decoration: none;
        }

        .login-page {
            width: 100%;
            height: calc(100vh - 56px - 56px);
            display: inline-block;
            display: flex;
            align-items: center;
        }

        .form-right i {
            font-size: 100px;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #d32f2f;">    
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Bread Factory</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">

                    <li class="nav-item">
                        <a class="nav-link active" href="new_work_shift.php">Nuevo Turno</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="login-page bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <h3 class="mb-3">Nuevo turno</h3>
                    <div class="bg-white shadow rounded">
                        <div class="row">
                            <div class="col-md-12 pe-0">
                                <div class="form-left h-100 py-5 px-5">
                                    <form action="" class="row g-4" id="startForm" autocomplete="off">
                                        <div class="col-12">
                                            <label>Fondo de caja<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-text"><i class="fas fa-money-bill"></i></div>
                                                <input type="text" class="form-control"
                                                    placeholder="Digite el fondo inicial" id="startingMoney">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary px-4 float-end mt-4" id="btnStart">Abrir
                                                turno</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer mt-auto py-3 bg-dark">
        <div class="container">
            <span class="text-muted">Developed & Designed by Xsandartech</span>
        </div>
    </footer>

    <script src="js/new_work_shift.js"></script>

</body>

</html>