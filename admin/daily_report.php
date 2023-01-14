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

    <title>Bread Factory - Reporte Diario</title>

    <style>
        .btn {
            margin-bottom: 0px;
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
                        <a class="nav-link active" href="daily_report.php">Reporte diario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="weekly_report.php">Reporte semanal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="crud_users.php">Usuarios</a>
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
            <h1>Reporte diario</h1>

            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="text-info" for="datepicker" class="col-form-label">Fecha: </label>
                </div>
                <div class="col-auto">
                    <input type="date" id="datepicker" class="form-control">
                </div>

                <div class="col-auto">
                <select class="form-select" aria-label="Default select example" id="workShift">
                    <option value="0">Turno Matutino</option>
                    <option value="1">Turno Vespertino</option>
                </select>
                </div>

                <div class="col-auto">
                <button type="button" class="btn btn-labeled btn-primary" id="btnUpdate">
                                <span class="btn-label"><i class="fas fa-redo"></i></span>Actualizar</button>
                </div>
            </div>
            
        </div>

    </div>


    <section class="pb-5 header">
        <div class="container">

            <div class="row">
                <div class="col-lg-12 mx-auto">

                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <h4 class="text-danger">Gastos</h4>

                            <div class="table-responsive">

                                <table class="table table-sm">
                                    <thead class="table-danger">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Hora</th>
                                            <th scope="col">Responsable</th>
                                            <th scope="col">Concepto</th>
                                            <th scope="col">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="expenses">

                                    </tbody>
                                </table>

                            </div>

                            <h4 class="text-success">Retiros</h4>
                            <div class="table-responsive">

                                <table class="table table-sm">
                                    <thead class="table-success">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Hora</th>
                                            <th scope="col">Responsable</th>
                                            <th scope="col">Concepto</th>
                                            <th scope="col">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="withdrawals">

                                    </tbody>
                                </table>

                            </div>

                            <div class="text-end">
                            <h6 id="total-incomes">Total de ingresos (Venta real): $0.00</h6>
                            <h6 id="total-withdrawals">Retiros: $0.00</h6>
                            <h6 id="total-expenses">Gastos: $0.00</h4>
                            <h6 id="final-money">Efectivo final: $0.00</h6>
                            <br>
                            <h6 id="remaining-money">Efectivo sobrante: $0.00</h6>
                            <h6 id="missing-money">Efectivo faltante: $0.00</h6>
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
    <script src="js/daily_report.js"></script>

</body>

</html>