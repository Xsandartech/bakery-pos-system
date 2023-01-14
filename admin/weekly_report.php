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

    <title>Bread Factory - Reporte semanal</title>

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
                        <a class="nav-link" href="daily_report.php">Reporte diario</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="weekly_report.php">Reporte semanal</a>
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
            <h1>Reporte semanal</h1>

            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="text-info" for="inputPassword6" class="col-form-label">Semana #</label>
                </div>
                <div class="col-auto">
                    <input type="number" id="weekNumber" class="form-control" min="1" max="52">
                    
                </div>
                <div class="col-auto">
                <button type="button" class="btn btn-labeled btn-primary" id="btnUpdate">
                                <span class="btn-label"><i class="fas fa-redo"></i></span>Actualizar</button>
                </div>
            </div>
            <br>
            <h5 id="datesWeek" class="text-secondary"></h5>
        </div>

    </div>

    <section class="pb-5 header">
        <div class="container">

            <div class="row">
                <div class="col-lg-12 mx-auto">

                    <div class="card border-0 shadow">
                        <div class="card-body p-4">

                            <!-- Responsive table -->
                            <h3>Ventas</h3>
                            <div class="table-responsive">

                                <table class="table">
                                    <thead class="table-success">
                                        <tr>
                                            <th scope="col">Turno</th>
                                            <th scope="col">Lunes</th>
                                            <th scope="col">Martes</th>
                                            <th scope="col">Miércoles</th>
                                            <th scope="col">Jueves</th>
                                            <th scope="col">Viernes</th>
                                            <th scope="col">Sábado</th>
                                            <th scope="col">Domingo</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="table-success">Matutino</th>
                                            <td id="in-m-0"></td>
                                            <td id="in-m-1"></td>
                                            <td id="in-m-2"></td>
                                            <td id="in-m-3"></td>
                                            <td id="in-m-4"></td>
                                            <td id="in-m-5"></td>
                                            <td id="in-m-6"></td>
                                            <td id="total-in-m"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="table-success">Vespertino</th>
                                            <td id="in-e-0"></td>
                                            <td id="in-e-1"></td>
                                            <td id="in-e-2"></td>
                                            <td id="in-e-3"></td>
                                            <td id="in-e-4"></td>
                                            <td id="in-e-5"></td>
                                            <td id="in-e-6"></td>
                                            <td id="total-in-e"></td>
                                        </tr>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <td id="total-in-0"></td>
                                            <td id="total-in-1"></td>
                                            <td id="total-in-2"></td>
                                            <td id="total-in-3"></td>
                                            <td id="total-in-4"></td>
                                            <td id="total-in-5"></td>
                                            <td id="total-in-6"></td>
                                            <td id="total-incomes"></td>
                                        </tr>
                                    </tfoot>
                                </table>

                            </div>


                            <h3>Gastos</h3>
                            <div class="table-responsive">

                                <table class="table">
                                    <thead class="table-danger">
                                        <tr>
                                            <th scope="col">Turno</th>
                                            <th scope="col">Lunes</th>
                                            <th scope="col">Martes</th>
                                            <th scope="col">Miércoles</th>
                                            <th scope="col">Jueves</th>
                                            <th scope="col">Viernes</th>
                                            <th scope="col">Sábado</th>
                                            <th scope="col">Domingo</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="table-danger">Matutino</th>
                                            <td id="ex-m-0">$0.00</td>
                                            <td id="ex-m-1">$0.00</td>
                                            <td id="ex-m-2">$0.00</td>
                                            <td id="ex-m-3">$0.00</td>
                                            <td id="ex-m-4">$0.00</td>
                                            <td id="ex-m-5">$0.00</td>
                                            <td id="ex-m-6">$0.00</td>
                                            <td id="total-ex-m">$0.00</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="table-danger">Vespertino</th>
                                            <td id="ex-e-0">$0.00</td>
                                            <td id="ex-e-1">$0.00</td>
                                            <td id="ex-e-2">$0.00</td>
                                            <td id="ex-e-3">$0.00</td>
                                            <td id="ex-e-4">$0.00</td>
                                            <td id="ex-e-5">$0.00</td>
                                            <td id="ex-e-6">$0.00</td>
                                            <td id="total-ex-e">$0.00</td>
                                        </tr>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <td id="total-ex-0">$0.00</td>
                                            <td id="total-ex-1">$0.00</td>
                                            <td id="total-ex-2">$0.00</td>
                                            <td id="total-ex-3">$0.00</td>
                                            <td id="total-ex-4">$0.00</td>
                                            <td id="total-ex-5">$0.00</td>
                                            <td id="total-ex-6">$0.00</td>
                                            <td id="total-expenses">$0.00</td>
                                        </tr>
                                    </tfoot>
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
    <script src="js/weekly_report.js"></script>

</body>

</html>