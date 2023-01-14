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

    <title>Bread Factory - Promociones</title>
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
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdownMenuLink" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Productos
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="crud_products.php">Gestión de productos</a></li>
                            <li><a class="dropdown-item active" href="crud_promos.php">Gestión de promociones</a></li>
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

    <!-- Modal Product -->
    <div class="modal fade" id="promoModal" tabindex="-1" aria-labelledby="promoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="promoModalLabel">Nueva promoción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="promoForm" autocomplete="off">
                        <label>Producto<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="Default select example" id="idPosProduct">
                            <!-- Products Here -->
                        </select>
                        <br>

                        <div class="col-12">
                            <label>Promoción<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-info-circle"></i></div>
                                <input type="text" class="form-control" placeholder="Descripción de la promoción
                            " id="description" maxlength="20">
                            </div>
                        </div>

                        <br>

                        <div class="col-12">
                            <label>Piezas<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-bread-slice"></i></div>
                                <input type="number" class="form-control" placeholder="Número de piezas contenidas"
                                    id="pieces">
                            </div>
                        </div>

                        <br>
                        <div class="col-12">
                            <label>Precio<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-dollar-sign"></i></div>
                                <input type="number" class="form-control" placeholder="Nuevo precio de venta por pieza"
                                    id="price">
                            </div>
                        </div>
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
            <h1>Promociones</h1>
            <button type="button" class="btn btn-labeled btn-primary" id="btnNewPromo">
                <span class=" btn-label"><i class="fas fa-plus-square"></i></span>Nueva promoción</button>

            <br>
            <input class="form-control" id="search" type="text" placeholder="Buscar..." autocomplete="off">
        </div>
    </div>

    <section class="pb-5 header">
        <div class="container text-white">

            <div class="row">
                <div class="col-lg-12 mx-auto">

                    <div class="card border-0 shadow">
                        <div class="card-body p-5">

                            <!-- Responsive table -->
                            <div class="table-responsive">
                                <table class="table m-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Producto</th>
                                            <th scope="col">Promoción</th>
                                            <th scope="col">Piezas</th>
                                            <th scope="col">Precio p/pieza</th>
                                            <th scope="col">Precio venta</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="promos">
                                        <tr>
                                            <th scope="row">1</th>
                                            <td>Dona & Roll</td>
                                            <td>Six</td>
                                            <td>6</td>
                                            <td>$10.00</td>
                                            <td>
                                                <!-- Call to action buttons -->
                                                <ul class="list-inline m-0">
                                                    <li class="list-inline-item">
                                                        <button class="btn btn-success btn-sm rounded-0" type="button"
                                                            data-toggle="tooltip" data-placement="top" title="Edit"><i
                                                                class="fa fa-edit"></i></button>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <button class="btn btn-danger btn-sm rounded-0" type="button"
                                                            data-toggle="tooltip" data-placement="top" title="Delete"><i
                                                                class="fa fa-trash"></i></button>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th scope="row">2</th>
                                            <td>Bolillo Simba</td>
                                            <td>Mayoreo</td>
                                            <td>1</td>
                                            <td>$1.50</td>
                                            <td>
                                                <!-- Call to action buttons -->
                                                <ul class="list-inline m-0">
                                                    <li class="list-inline-item">
                                                        <button class="btn btn-success btn-sm rounded-0" type="button"
                                                            data-toggle="tooltip" data-placement="top" title="Edit"><i
                                                                class="fa fa-edit"></i></button>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <button class="btn btn-danger btn-sm rounded-0" type="button"
                                                            data-toggle="tooltip" data-placement="top" title="Delete"><i
                                                                class="fa fa-trash"></i></button>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>

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
    <script src="js/crud_promos.js"></script>

</body>

</html>