let movementModal;

$(document).ready(function () {
  $("#search").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    $("#sales tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  movementModal = new bootstrap.Modal(
    document.getElementById("movementModal"),
    {}
  );
});

get_movements_history();

$("#btnContinue").click(() => {
  let description = $("#description").val();
  let amount = $("#amount").val();
  $("#btnContinue").addClass("disabled");
  registerMovement(description, amount);
});

function registerMovement(description, amount) {
  Swal.fire({
    title: "Cargando",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });

  fetch("/bread_factory/pos/controllers/register_safe_box_movement.php", {
    method: "POST",
    body: JSON.stringify({
      description: description,
      amount: amount,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      //console.log(data);
      $("#btnContinue").removeClass("disabled");
      switch (data.result) {
        case "ok":
          Swal.close();
          movementModal.hide();

          $("#movementForm").trigger("reset");

          Swal.fire({
            icon: "success",
            title: "Movimiento registrado",
            showConfirmButton: false,
            timer: 1500,
            didClose: () => {
              get_movements_history();
            },
          });

          break;

        case "insufficient_money":
          Swal.fire({
            title: "Dinero insuficiente",
            text: "No se registró el movimiento. ¡Estás tratando de retirar más de lo que hay disponible!.",
            icon: "warning",
            confirmButtonText: "Aceptar",
          });

          break;

        case "isset":
        case "error":
          Swal.fire({
            title: "Error",
            text: "No se registró el movimiento. Inténtalo de nuevo y si el error persiste, contacta a tu administrador.",
            icon: "error",
            confirmButtonText: "Aceptar",
          });
          break;
      }
    })
    .catch((error) => {
      Swal.fire(
        "Error de servidor",
        "Concata a tu administrador e indica el codigo de error: SERVER_ERR",
        "warning"
      );
      console.log("Request failed:", error);
    });
}

function get_movements_history() {
  Swal.fire({
    title: "Cargando",
    text: "Espera mientras se obtiene el historial.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });

  fetch("/bread_factory/pos/controllers/get_safe_box_history.php")
    .then((response) => response.json())
    .then((data) => {
      Swal.close();
      //console.log(data);

      $("#movements").html("");
      let index = Object.keys(data).length;

      data.forEach((movement) => {
        let type = movement.type;
        let typeName, txtColor;
        if (type === "0") {
          typeName = `<i class="fas fa-piggy-bank"></i> Ingreso`;
          txtColor = "text-success";
        } else {
          typeName = `<i class="fas fa-coins"></i> Egreso`;
          txtColor = "text-danger";
        }

        let trElem = document.createElement("tr");
        trElem.innerHTML = `
          <tr>
              <th scope="row">${index--}</th>
              <td>${movement.datetime}</td>
              <td>${movement.resp}</td>
              <td class="${txtColor}">${typeName}</td>
              <td>${movement.description}</td>
              <td>${formatter.format(movement.amount)}</td>
          </tr>`;

        $("#movements").append(trElem);
      });
    })
    .catch((error) => {
      console.log("Error: ", error);
    });
}
