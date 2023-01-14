<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include (CONTROLLERS_PATH."check_access.php");
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

    <title>Bread Factory - Caja</title>
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
                        <a class="nav-link" aria-current="page" href="pos.php">POS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sales.php">Ventas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cash_drawer.php">Caja</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Productos
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="crud_products.php">Gestión de productos</a></li>
                            <li><a class="dropdown-item" href="crud_promos.php">Gestión de promociones</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="finish_work_shift.php">Terminar turno</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="printer_settings.php">Impresora</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar sesión</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <!-- Modal Cash Movement -->
    <div class="modal fade" id="movementModal" tabindex="-1" aria-labelledby="movementModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="movementModalLabel">Registrar gasto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="movementForm" autocomplete="off">
                        <div class="col-12">
                            <label>Concepto<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-info-circle"></i></div>
                                <input type="text" class="form-control" placeholder="Descripción del movimiento"
                                    id="description">
                            </div>
                        </div>

                        <br>

                        <div class="col-12">
                            <label>Monto<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-dollar-sign"></i></div>
                                <input type="number" class="form-control" placeholder="Monto total del movimiento"
                                    id="amount">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnContinue">Continuar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="container py-3">
            <h1>Caja</h1>

            <button type="button" class="btn btn-labeled btn-danger" id="btnExpense">
                <span class="btn-label"><i class="fas fa-coins"></i></span>Registrar gasto</button>

            <button type="button" class="btn btn-labeled btn-success" id="btnWithdrawal">
                <span class="btn-label"><i class="fas fa-piggy-bank"></i></span>Registrar retiro</button>

            <br>

            <input class="form-control" id="search" type="text" placeholder="Buscar..." autocomplete="off">
        </div>

    </div>

    <section class="pb-5 header">
        <div class="container text-white">

            <div class="row">
                <div class="col-lg-12 mx-auto">

                    <div class="card border-0 shadow">
                        <div class="card-body p-4">

                            <!-- Responsive table -->
                            <div class="table-responsive">
                                <table class="table m-0 table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Hora</th>
                                            <th scope="col">Responsable</th>
                                            <th scope="col">Tipo</th>
                                            <th scope="col">Concepto</th>
                                            <th scope="col">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="movements">
                                        <!-- Movements Here -->

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

    <script src="js/money_formatter.js"></script>
    <script src="js/cash_drawer.js"></script>

</body>

</html>