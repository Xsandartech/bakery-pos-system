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

    <!-- Main Page Style-->
    <link href="css/pos.css" rel="stylesheet">

    <!-- Numpad Js-->
    <link rel="stylesheet" href="../libs/numpad-js/numpad-light.css" />
    <script src="../libs/numpad-js/numpad.js"></script>

    <!-- Sweet Alert 2 -->
    <link rel="stylesheet" href="../libs/sweetalert2/css/sweetalert2.min.css" />
    <script src="../libs/sweetalert2/sweetalert2.min.js"></script>

    <!-- Font Awesome -->
    <script src="../libs/font-awesome/all.js"></script>

    <title>Bread Factory - POS</title>
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
                        <a class="nav-link active" aria-current="page" href="pos.php">POS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sales.php">Ventas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cash_drawer.php">Caja</a>
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

    <!-- Modal Product Options -->
    <div class="modal fade" id="optionsModal" tabindex="-1" aria-labelledby="optionsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="optionsModalLabel">Product Description</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-2 text-center"><button class="btn btn-secondary" id="btnRemoveOne"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                        <div class="col-md-3 text-center text-dark" id="quantityLabel">1</div>
                        <div class="col-md-2 text-center"><button class="btn btn-secondary" id="btnAddOne"><i
                                    class="fas fa-plus"></i></button>
                        </div>

                        <div class="col text-center">
                            <button type="button" class="btn btn-labeled btn-danger" id="btnRemove">
                                <span class="btn-label"><i class="fas fa-trash-alt"></i></span>Remover</button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnOptionsContinue">Continuar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid h-100">
        <div class="row h-100">
            <div class="col-md-4 items-pos">
                <!-- col-xs-12 col-md-5 col-lg-4 col-xl-3 -->
                <div class="navy">
                    <div class="content">

                        <div class="col-12 d-none">
                            <div class="input-group">
                                <input type="number" id="inputQuantity">
                            </div>
                        </div>

                        <button type="button" class="d-none btn btn-labeled btn-danger" data-bs-toggle="modal"
                            data-bs-target="#itemModal">
                            <span class="btn-label"><i class="fas fa-coins"></i></span>Open Modal</button>

                        <h1 class="text-green-money" id="textTotalSale">Total: $0.00</h1>

                        <button type="button" class="btn btn-labeled btn-primary" id="btnCheckOut">
                            <span class="btn-label"><i class="fas fa-check"></i></span>Terminar</button>

                        <button type="button" class="btn btn-labeled btn-danger" id="btnCancelSale">
                            <span class="btn-label"><i class="fas fa-trash-alt"></i></span>Cancelar venta</button>

                        <table class="table table-hover table-sm" id="posTable">
                            <thead>
                                <tr>
                                    <th scope="col">Descripción</th>
                                    <th scope="col">Cant.</th>
                                    <th scope="col">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="posCartTable">
                                <!-- cart products here -->
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- col-xs-12 col-md-7 col-lg-8 col-xl-9 -->
                <div class="light">
                    <div class="content">
                        <h2>Productos</h2>

                        <div class="container">
                            <div class="row" id="posProducts">

                                <!-- <div class="col-lg-2 col-md-3 col-6">
                                    <div class="box bg-orange"></div>
                                    <div class="product-desc">Dona & Roll</div>
                                </div> -->

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

    <script src="js/money_formatter.js"></script>
    <script src="js/pos.js"></script>

</body>

</html>