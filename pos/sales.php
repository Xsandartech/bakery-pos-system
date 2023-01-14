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

    <title>Bread Factory - Ventas</title>

    <style>
        .table>tbody>tr>td {
            vertical-align: middle;
        }

        .table>tbody>tr>th {
            vertical-align: middle;
        }

        .btn-see-ticket {
            margin-bottom: 0px;
        }

        .ticket-logo {
            width: 150px;
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
                        <a class="nav-link" aria-current="page" href="pos.php">POS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="sales.php">Ventas</a>
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

    <!-- Modal Sales Report -->
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Reporte de ventas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col">
                            <h5 class="text-green-money" id="labelSold">Vendido: $0.00</h5>
                            <table class="table table-bordered table-sm" id="posTable">
                            <thead>
                                <tr>
                                    <th scope="col">Descripción</th>
                                    <th scope="col">Cant.</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody id="report">
                                <!-- cart products here -->
                            </tbody>
                        </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">

                <button type="button" data-placement="top" class="btn btn-labeled btn-secondary" id="btnPrintReport">
                <span class="btn-label"><i class="fas fa-print"></i></span>Imprimir reporte</button>

                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

        <!-- Modal Tickett -->
        <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketModalLabel">Ticket de venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col">

                            <!-- <img src="images/ticket_logo.png" class="rounded mx-auto d-block ticket-logo" alt="..."> -->

                            <div class="text-center">
                                <h6 class="fw-bold">BREAD FACTORY</h6>
                                <span>Ticket de venta<br></span>
                                <span id="ticketDatetime"></span>
                            </div>

                            
                            
                            
                            <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Descripción</th>
                                    <th scope="col">$</th>
                                </tr>
                            </thead>
                            <tbody id="ticketTable">
                                <!-- ticket products here -->
                            </tbody>

                            <tfoot class="fw-bold">
                                <td>Total:</td>
                                <td id="totalTicketSale">$242.00</td>
                            </tfoot>
                        </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                <button type="button" data-placement="top" class="btn btn-labeled btn-secondary" id="btnReprint">
                <span class="btn-label"><i class="fas fa-print"></i></span>Reimprimir</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="container py-3">
            <h1>Ventas</h1>
            <input class="form-control" id="search" type="text" placeholder="Buscar..." autocomplete="off">
        </div>

    </div>

    <section class="pb-5 header">
        <div class="container">
            
            <button type="button" data-placement="top" class="btn btn-labeled btn-primary" id="btnReport">
                <span class="btn-label"><i class="fas fa-file-invoice-dollar"></i></span>Ver reporte de ventas</button>

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
                                            <th scope="col">Responsable</th>
                                            <th scope="col">Hora</th>
                                            <th scope="col">Método de pago</th>
                                            <th scope="col">Total de la venta</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="sales">
                                        <!-- Sales here -->
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
    <script src="js/sales.js"></script>

</body>

</html>