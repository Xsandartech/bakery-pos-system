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

    <title>Bread Factory - Gestión de productos</title>
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
                            <li><a class="dropdown-item active" href="crud_products.php">Gestión de productos</a></li>
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

    <!-- Modal Product -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Nuevo producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form action="" id="productForm" autocomplete="off">
                        <div class="col-12">
                            <label>Descripción<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-info-circle"></i></div>
                                <input type="text" class="form-control" placeholder="Nombre del producto"
                                    id="description" maxlength="20">
                            </div>
                        </div>

                        <br>

                        <div class="col-12">
                            <label>Costo<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-dollar-sign"></i></div>
                                <input type="number" class="form-control" placeholder="Costo de producción" id="cost">
                            </div>
                        </div>

                        <br>
                        <div class="col-12">
                            <label>Precio<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-dollar-sign"></i></div>
                                <input type="number" class="form-control" placeholder="Venta al público" id="price">
                            </div>
                        </div>

                        <br>
                        <label>Color<span class="text-danger">*</span></label>
                        <select class="form-select" aria-label="Default select example" id="color">
                            <option value="bg-blue">Azul</option>
                            <option value="bg-cyan">Cian</option>
                            <option value="bg-blue-grey">Gris azulado</option>
                            <option value="bg-brown">Marrón</option>
                            <option value="bg-orange">Naranja</option>
                            <option value="bg-purple">Púrpura</option>
                            <option value="bg-red">Rojo</option>
                            <option value="bg-pink">Rosa</option>
                            <option value="bg-green">Verde</option>
                            <option value="bg-teal">Verde azulado</option>
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
            <h1>Gestión de productos</h1>
            <button type="button" class="btn btn-labeled btn-primary" id="btnNewProduct">
                <span class=" btn-label"><i class="fas fa-plus-square"></i></span>Nuevo producto</button>
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
                                            <th scope="col">Descripción</th>
                                            <th scope="col">Precio costo</th>
                                            <th scope="col">Precio venta</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="products">
                                        <!-- Products Here -->
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
    <script src="js/crud_products.js"></script>

</body>

</html>