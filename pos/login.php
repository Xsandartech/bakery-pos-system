<?php

session_start();
if(isset($_SESSION['user_id'])) { //session already exists
    header('Location: pos.php');
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
                        <a class="nav-link active" href="../pos/login.php">POS</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="../admin/login.php">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="login-page bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <h3 class="mb-3">Iniciar sesión (POS)</h3>
                    <div class="bg-white shadow rounded">
                        <div class="row">
                            <div class="col-md-7 pe-0">
                                <div class="form-left h-100 py-5 px-5">
                                    <form action="" class="row g-4" id="loginForm" autocomplete="off">
                                        <div class="col-12">
                                            <label>Usuario<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-text"><i class="fas fa-user"></i></div>
                                                <input type="text" class="form-control" placeholder="Enter Username" id="userName" required>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label>Contraseña<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-text"><i class="fas fa-key"></i></div>
                                                <input type="password" class="form-control"
                                                    placeholder="Enter Password" id="password" required>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary px-4 float-end mt-4" id="btnLogin">Iniciar
                                                sesión</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-5 ps-0 d-none d-md-block">
                                <div class="form-right h-100 bg-primary text-white text-center pt-5">
                                    <img src="images/company_logo.png" style="width: 150px;" class="img-fluid" />
                                    <h2 class="fs-1">¡Hola!</h2>
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

    <script src="js/login.js"></script>

</body>

</html>