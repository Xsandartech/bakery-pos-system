let movementModal;

$(document).ready(function () {
  $("#search").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    $("#movements tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  movementModal = new bootstrap.Modal(
    document.getElementById("movementModal"),
    {}
  );
});

let movementType;

$("#btnExpense").click(() => {
  movementType = "expense";
  $("#movementModalLabel").text("Registrar gasto");
  movementModal.show();
});

$("#btnWithdrawal").click(() => {
  movementType = "withdrawal";
  $("#movementModalLabel").text("Registrar retiro");
  movementModal.show();
});

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

  fetch("/bread_factory/pos/controllers/register_cash_drawer_movement.php", {
    method: "POST",
    body: JSON.stringify({
      description: description,
      amount: amount,
      movement_type: movementType,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      //console.log(data);
      $("#btnContinue").removeClass("disabled");
      switch (data) {
        case "ok":
          Swal.close();
          movementModal.hide();

          $("#movementForm").trigger("reset");

          //getMovementsHistory();
          Swal.fire({
            icon: "success",
            title: "Movimiento registrado",
            showConfirmButton: false,
            timer: 1500,
            didClose: () => {
              getMovementsHistory();
            },
          });

          break;

        case "insufficient_money":
          Swal.fire({
            title: "Dinero en caja insuficiente",
            text: "No se registró el movimiento. ¡Estás tratando de gastar/retirar más de lo que hay en caja!.",
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

getMovementsHistory();

function getMovementsHistory() {
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

  fetch("/bread_factory/pos/controllers/get_cash_drawer_history.php")
    .then((response) => response.json())
    .then((data) => {
      Swal.close();
      //console.log(data);

      $("#movements").html("");
      let index = Object.keys(data).length;

      data.forEach((movement) => {
        let type = movement.type;
        let typeName, txtColor;

        switch (type) {
          case "0":
            typeName = `<i class="fas fa-coins"></i> Gasto`;
            txtColor = "text-danger";
            break;

          case "1":
            typeName = `<i class="fas fa-piggy-bank"></i> Retiro`;
            txtColor = "text-success";
            break;

          default:
            typeName = `<i class="fas fa-cog"></i> Ajuste`;
            txtColor = "text-secondary";
            break;
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
