let promoModal;
let task;
let promoIdClicked;

$(document).ready(function () {
  $("#search").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    $("#promos tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  promoModal = new bootstrap.Modal(document.getElementById("promoModal"), {});
});

$("#btnNewPromo").click(() => {
  task = "add";
  $("#promoModalLabel").text("Nueva promoción");
  promoModal.show();
});

$("#btnCancel").click(() => {
  $("#promoForm").trigger("reset");
});

$("#btnContinue").click(() => {
  let idPosProduct = $("#idPosProduct").val();
  let description = $("#description").val();
  let pieces = $("#pieces").val();
  let price = $("#price").val();

  if (description === "" || pieces === "" || price === "") {
    Swal.fire({
      icon: "warning",
      title: "Oops...",
      text: "Debes de completar todos los campos.",
    });
    return;
  }

  switch (task) {
    case "add":
      insertPromo(idPosProduct, description, pieces, price);
      break;
    case "edit":
      editPromo(idPosProduct, description, pieces, price);
      break;
  }
});

getPosPromos();

function getPosProducts() {
  Swal.fire({
    title: "Cargando",
    text: "Espera mientras se obtienen los productos.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
  });

  fetch("/bread_factory/pos/controllers/get_products.php")
    .then((response) => response.json())
    .then((data) => {
      Swal.close();

      //console.log(data);

      $("#idPosProduct").html("");

      data.forEach((product) => {
        let productId = product.id;
        let productDescription = product.description;

        $("#idPosProduct").append(
          `<option value="${productId}">${productDescription}</option>`
        );
      });
    })
    .catch((error) => {
      console.log("Error: ", error);
    });
}

function getPosPromos() {
  Swal.fire({
    title: "Cargando",
    text: "Espera mientras se obtienen las promociones.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    didClose: () => {
      getPosProducts();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });

  fetch("/bread_factory/pos/controllers/get_promos.php")
    .then((response) => response.json())
    .then((data) => {
      Swal.close();
      //console.log(data);

      $("#promos").html("");

      let index = 1;

      data.forEach((promo) => {
        let promoId = promo.id;
        let promoProduct = promo.product;
        let promoDescription = promo.description;
        let promoPieces = promo.pieces;
        let promoPrice = promo.price;

        let trPromo = document.createElement("tr");
        trPromo.innerHTML = `
          <th scope="row">${index++}</th>
          <td class="text-secondary"><i class="fa fa-star"></i> ${promoProduct}</td>
          <td class="text-success"><i class="fa fa-box"></i> ${promoDescription}</td>
          <td>${promoPieces}</td>
          <td>${formatter.format(promoPrice)}</td>
          <td>${formatter.format(promoPrice * promoPieces)}</td>
          <td>
              <ul class="list-inline m-0">
                  <li class="list-inline-item">
                      <button id="edit${promoId}" class="btn btn-success btn-sm rounded-0" type="button"
                          data-toggle="tooltip" data-placement="top" title="Edit"
                          id-promo="${promoId}"><i
                              class="fa fa-edit"></i></button>
                  </li>
                  <li class="list-inline-item">
                      <button id="delete${promoId}" class="btn btn-danger btn-sm rounded-0" type="button"
                          data-toggle="tooltip" data-placement="top" title="Delete"
                          id-promo="${promoId}"><i
                              class="fa fa-trash"></i></button>
                  </li>
              </ul>
          </td>`;

        $("#promos").append(trPromo);

        document
          .getElementById(`delete${promoId}`)
          .addEventListener("click", deletePromo);

        document
          .getElementById(`edit${promoId}`)
          .addEventListener("click", editPromoClicked);
      });
    })
    .catch((error) => {
      console.log("Error: ", error);
    });
}

function insertPromo(idPosProduct, description, pieces, price) {
  fetch("/bread_factory/pos/controllers/insert_promo.php", {
    method: "POST",
    body: JSON.stringify({
      id_pos_product: idPosProduct,
      description: description,
      pieces: pieces,
      price: price,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      switch (data.status) {
        case "ok":
          Swal.close();
          promoModal.hide();

          $("#promoForm").trigger("reset");

          //getMovementsHistory();
          Swal.fire({
            icon: "success",
            title: "Se creó la promoción correctamente.",
            showConfirmButton: false,
            timer: 1500,
            didClose: () => {
              getPosPromos();
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

function deletePromo(e) {
  let id = e.currentTarget.getAttribute("id-promo");

  Swal.fire({
    title: "¿Estás seguro?",
    text: "Se eliminará la promoción seleccionada.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Eliminar",
    cancelButtonText: "Cancelar",
    showLoaderOnConfirm: true,
    backdrop: true,
    preConfirm: () => {
      return fetch("/bread_factory/pos/controllers/delete_promo.php", {
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
            title: "Se eliminó la promoción correctamente.",
            showConfirmButton: false,
            timer: 1500,
            didClose: () => {
              getPosPromos();
            },
          });
          break;

        case "isset":
        case "error":
          Swal.fire({
            title: "Error",
            text: "No se eliminó la promoción. Inténtalo de nuevo y si el error persiste, contacta a tu administrador.",
            icon: "error",
            confirmButtonText: "Aceptar",
          });
          break;
      }
    }
  });
}

function editPromoClicked(e) {
  let id = e.currentTarget.getAttribute("id-promo");
  promoIdClicked = id;
  task = "edit";

  //get data product before show modal
  getPromoData(promoIdClicked);
}

function editPromo(idPosProduct, description, pieces, price) {
  fetch("/bread_factory/pos/controllers/edit_promo.php", {
    method: "POST",
    body: JSON.stringify({
      id: promoIdClicked,
      id_pos_product: idPosProduct,
      description: description,
      pieces: pieces,
      price: price,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      switch (data.status) {
        case "ok":
          Swal.close();
          promoModal.hide();

          $("#promoForm").trigger("reset");

          //getMovementsHistory();
          Swal.fire({
            icon: "success",
            title: "Se editó la promoción correctamente.",
            showConfirmButton: false,
            timer: 1500,
            didClose: () => {
              getPosPromos();
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

function getPromoData(id) {
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

  fetch("/bread_factory/pos/controllers/get_promo_data.php", {
    method: "POST",
    body: JSON.stringify({
      id: id,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      $("#idPosProduct").val(data.id_pos_product);
      $("#description").val(data.description);
      $("#pieces").val(data.pieces);
      $("#price").val(data.price);

      $("#promoModalLabel").text("Editar producto");

      Swal.close();
      promoModal.show();
    })
    .catch((error) => {
      console.log("Request failed:", error);
    });
}
