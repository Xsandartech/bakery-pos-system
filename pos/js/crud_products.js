let productModal;
let task;
let productIdClicked;

$(document).ready(function () {
  $("#search").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    $("#products tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  productModal = new bootstrap.Modal(
    document.getElementById("productModal"),
    {}
  );
});

getPosProducts();

function getPosProducts() {
  Swal.fire({
    title: "Cargando",
    text: "Espera mientras se obtienen los productos.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });

  fetch("/bread_factory/pos/controllers/get_products.php")
    .then((response) => response.json())
    .then((data) => {
      Swal.close();

      //console.log(data);

      $("#products").html("");

      let index = 1;

      data.forEach((product) => {
        let productId = product.id;
        let productDescription = product.description;
        let productPrice = product.price;
        let productCost = product.cost;
        let color = product.color;

        let trProduct = document.createElement("tr");
        trProduct.innerHTML = `
        <th scope="row">${index++}</th>
        <td>${productDescription}</td>
        <td>${formatter.format(productCost)}</td>
        <td>${formatter.format(productPrice)}</td>
        <td>
            <ul class="list-inline m-0">
                <li class="list-inline-item">
                    <button id="edit${productId}" class="btn btn-success btn-sm rounded-0" type="button"
                        data-toggle="tooltip" data-placement="top" title="Edit"
                        id-product="${productId}"><i
                            class="fa fa-edit"></i></button>
                </li>
                <li class="list-inline-item">
                    <button id="delete${productId}" class="btn btn-danger btn-sm rounded-0" type="button"
                        data-toggle="tooltip" data-placement="top" title="Delete"
                        id-product="${productId}"><i
                            class="fa fa-trash"></i></button>
                </li>
            </ul>
        </td>`;

        $("#products").append(trProduct);

        document
          .getElementById(`delete${productId}`)
          .addEventListener("click", deleteProduct);

        document
          .getElementById(`edit${productId}`)
          .addEventListener("click", editProductClicked);
      });
    })
    .catch((error) => {
      console.log("Error: ", error);
    });
}

$("#btnNewProduct").click(() => {
  task = "add";
  $("#productModalLabel").text("Nuevo producto");
  productModal.show();
});

function editProductClicked(e) {
  let id = e.currentTarget.getAttribute("id-product");
  productIdClicked = id;
  task = "edit";

  //get data product before show modal
  getProductData(productIdClicked);
}

$("#btnContinue").click(() => {
  let description = $("#description").val();
  let cost = $("#cost").val();
  let price = $("#price").val();
  let color = $("#color").val();

  if (description === "" || cost === "" || price === "" || color === "") {
    Swal.fire({
      icon: "warning",
      title: "Oops...",
      text: "Debes de completar todos los campos.",
    });
    return;
  }

  switch (task) {
    case "add":
      insertProduct(description, cost, price, color);
      break;
    case "edit":
      editProduct(productIdClicked, description, cost, price, color);
      break;
  }
});

$("#btnCancel").click(() => {
  $("#productForm").trigger("reset");
});

function deleteProduct(e) {
  let id = e.currentTarget.getAttribute("id-product");

  Swal.fire({
    title: "¿Estás seguro?",
    text: "Se eliminará el producto seleccionado.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Eliminar",
    cancelButtonText: "Cancelar",
    showLoaderOnConfirm: true,
    backdrop: true,
    preConfirm: () => {
      return fetch("/bread_factory/pos/controllers/delete_product.php", {
        method: "POST",
        body: JSON.stringify({
          id: id,
        }),
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error(response.statusText);
          }
          return response.json();
        })
        .catch((error) => {
          Swal.showValidationMessage(`Request failed: ${error}`);
        });
    },
    allowOutsideClick: () => !Swal.isLoading(),
  }).then((result) => {
    if (result.isConfirmed) {
      switch (result.value.status) {
        case "ok":
          Swal.fire({
            icon: "success",
            title: "Se eliminó el producto correctamente.",
            showConfirmButton: false,
            timer: 1500,
            didClose: () => {
              getPosProducts();
            },
          });
          break;

        case "isset":
        case "error":
          Swal.fire({
            title: "Error",
            text: "No se eliminó el producto. Inténtalo de nuevo y si el error persiste, contacta a tu administrador.",
            icon: "error",
            confirmButtonText: "Aceptar",
          });
          break;
      }
    }
  });
}

function editProduct(id, description, cost, price, color) {
  fetch("/bread_factory/pos/controllers/edit_product.php", {
    method: "POST",
    body: JSON.stringify({
      id: id,
      description: description,
      cost: cost,
      price: price,
      color: color,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      switch (data.status) {
        case "ok":
          Swal.close();
          productModal.hide();

          $("#productForm").trigger("reset");

          //getMovementsHistory();
          Swal.fire({
            icon: "success",
            title: "Se editó el producto correctamente.",
            showConfirmButton: false,
            timer: 1500,
            didClose: () => {
              getPosProducts();
            },
          });
          break;

        default:
          Swal.fire(
            "Error de servidor",
            "Concata a tu administrador e indica el codigo de error: SERVER_ERR",
            "warning"
          );
          break;
      }
    })
    .catch((error) => {
      console.log("Request failed:", error);
    });
}

function getProductData(id) {
  Swal.fire({
    title: "Cargando",
    text: "Espera mientras se obtienen la información.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });

  fetch("/bread_factory/pos/controllers/get_product_data.php", {
    method: "POST",
    body: JSON.stringify({
      id: id,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      $("#description").val(data.description);
      $("#cost").val(data.cost);
      $("#price").val(data.price);
      $("#color").val(data.color);

      $("#productModalLabel").text("Editar producto");

      Swal.close();
      productModal.show();
    })
    .catch((error) => {
      console.log("Request failed:", error);
    });
}

function insertProduct(description, cost, price, color) {
  fetch("/bread_factory/pos/controllers/insert_product.php", {
    method: "POST",
    body: JSON.stringify({
      description: description,
      cost: cost,
      price: price,
      color: color,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      switch (data.status) {
        case "ok":
          Swal.close();
          productModal.hide();

          $("#productForm").trigger("reset");

          //getMovementsHistory();
          Swal.fire({
            icon: "success",
            title: "Se añadió el producto correctamente.",
            showConfirmButton: false,
            timer: 1500,
            didClose: () => {
              getPosProducts();
            },
          });
          break;

        default:
          Swal.fire(
            "Error de servidor",
            "Concata a tu administrador e indica el codigo de error: SERVER_ERR",
            "warning"
          );
          break;
      }
    })
    .catch((error) => {
      console.log("Request failed:", error);
    });
}
