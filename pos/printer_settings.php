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

    <title>Bread Factory - Configuración de impresora</title>
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
                        <a class="nav-link active" href="printer_settings.php">Impresora</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar sesión</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <div class="section">
        <div class="container py-3">
            <h1>Configuración de impresora</h1>

            <button type="button" class="btn btn-labeled btn-primary" id="btnPrintTest">
                <span class=" btn-label"><i class="fas fa-print"></i></span>Prueba de impresión</button>
        </div>
    </div>

    <section class="pb-5 header">
        <div class="container">

            <div class="row">


                <div class="col-lg-12 mx-auto">

                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <h4>Tipo de conexión</h4>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="printerMode" id="printerMode1"
                                    value="0" checked>
                                <label class="form-check-label" for="printerMode1">
                                    USB
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="printerMode" id="printerMode2"
                                    value="1">
                                <label class="form-check-label" for="printerMode2">
                                    LAN (IP)
                                </label>
                            </div>

                            <section id="usbSettings">
                                <label for="printerName" class="form-label">Nombre de la impresora</label>
                                <input type="text" id="printerName" class="form-control"
                                    aria-describedby="passwordHelpBlock" placeholder="Epson-123X">
                                <div id="passwordHelpBlock" class="form-text">
                                    Escribe el nombre de tu impresora térmica exactamente como aparece en el panel de
                                    control.
                                </div>
                            </section>

                            <section id="lanSettings">
                                <label for="printerIP" class="form-label">Dirección IP</label>
                                <input type="text" id="printerIP" class="form-control"
                                    aria-describedby="passwordHelpBlock">
                                <div id="passwordHelpBlock" class="form-text">
                                    Escribe la dirección IP de tu impresora. Ej. 192.168.1.78
                                </div>

                                <label for="printerPort" class="form-label">Puerto</label>
                                <input type="text" id="printerPort" class="form-control"
                                    aria-describedby="passwordHelpBlock">
                                <div id="passwordHelpBlock" class="form-text">
                                    Escribe el puerto de tu impresora.
                                </div>
                            </section>
                            <br>

                            <button type="button" class="btn btn-labeled btn-secondary" id="btnSaveMode">
                                <span class=" btn-label"><i class="fas fa-save"></i></span>Guardar cambios</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </section>

    <section class="pb-5 header">
        <div class="container">

            <div class="row">


                <div class="col-lg-12 mx-auto">

                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <h4>Personalizar ticket</h4>

                            <label for="printerCols" class="form-label">Columnas</label>
                            <input type="text" id="printerCols" class="form-control"
                                aria-describedby="passwordHelpBlock" placeholder="48">
                            <br>

                            <label for="ticketTitle" class="form-label">Título</label>
                            <input type="text" id="ticketTitle" class="form-control"
                                aria-describedby="passwordHelpBlock" placeholder="BREAD FACTORY">
                            <br>

                            <label for="ticketSubtitle" class="form-label">Subtítulo</label>
                            <input type="text" id="ticketSubtitle" class="form-control"
                                aria-describedby="passwordHelpBlock" placeholder="Ticket de venta">

                            <br>
                            <label for="ticketFooter" class="form-label">Pié del ticket</label>
                            <input type="text" id="ticketFooter" class="form-control"
                                aria-describedby="passwordHelpBlock" placeholder="***¡Gracias por tu compra!***">

                            <br>

                            <button type="button" class="btn btn-labeled btn-secondary" id="btnSaveTicket">
                                <span class=" btn-label"><i class="fas fa-save"></i></span>Guardar cambios</button>

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

    <script src="js/printer_settings.js"></script>

</body>

</html>