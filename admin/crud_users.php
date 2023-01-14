<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/admin/dirs.php');
include (CONTROLLERS_PATH."check_access.php");
?>

<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" sizes="16x16 24x24 32x32 48x48 64x64" href="../pos/images/favicon.ico">

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

    <title>Bread Factory - Gestión de usuarios</title>
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
                        <a class="nav-link" href="daily_report.php">Reporte diario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="weekly_report.php">Reporte semanal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="crud_users.php">Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Modal Product -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Nuevo usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form action="" id="userForm" autocomplete="off">
                        <div class="col-12">
                            <label>Nombre<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-info-circle"></i></div>
                                <input type="text" class="form-control" placeholder="Nombre y apellido"
                                    id="displayName" minlength="5" maxlength="20">
                            </div>
                        </div>

                        <br>

                        <div class="col-12">
                            <label>Usuario<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-user"></i></div>
                                <input type="text" class="form-control" pattern="[a-z0-9]+"  minlength="5" placeholder="Nombre de usuario" id="userName">
                            </div>
                        </div>

                        <br>
                        <div class="col-12">
                            <label>Contraseña<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-key"></i></div>
                                <input type="password" class="form-control" minlength="8" placeholder="Contraseña" id="password">
                            </div>
                        </div>

                        <br>
                        <div class="col-12">
                            <label>Confirmar contraseña<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-key"></i></div>
                                <input type="password" class="form-control" minlength="8" placeholder="Contraseña" id="confirmPassword">
                            </div>
                        </div>

                        <br>
                        <label>Tipo de cuenta<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="Default select example" id="isAdmin">
                            <option value="0">Empleado</option>
                            <option value="1">Administrador</option>
                        </select>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        id="btnCancel">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnContinue">Continuar</button>
                </div>

            </div>
        </div>
    </div>

    <div class="section">
        <div class="container py-3">
            <h1>Gestión de usuarios</h1>
            <button type="button" class="btn btn-labeled btn-primary" id="btnNewUser">
                <span class=" btn-label"><i class="fas fa-plus-square"></i></span>Nuevo usuario</button>
            <br>
            <input class="form-control" id="search" type="text" placeholder="Buscar..." autocomplete="off">
        </div>

    </div>

    <section class="pb-5 header">
        <div class="container text-white">

            <div class="row">
                <div class="col-lg-12 mx-auto">

                    <div class="card border-0 shadow">
                        <div class="card-body p-3">

                            <!-- Responsive table -->
                            <div class="table-responsive">
                                <table class="table m-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Nombre</th>
                                            <th scope="col">Usuario</th>
                                            <th scope="col">Tipo de cuenta</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="users">
                                        <!-- Users Here -->
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer mt-auto py-3 bg-dark">
        <div class="container">
            <span class="text-muted">Developed & Designed by Xsandartech</span>
        </div>
    </footer>

    <script src="js/crud_users.js"></script>

</body>

</html>