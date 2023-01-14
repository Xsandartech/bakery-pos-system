window.addEventListener("load", () => {
  //init numpad js
  numpad.attach({ target: document.getElementById("finalMoney") });
});

const finishForm = document.getElementById("finishForm");

finishForm.addEventListener("submit", (e) => {
  e.preventDefault();

  let finalMoney = $("#finalMoney").val();

  if (finalMoney === "") {
    Swal.fire({
      icon: "warning",
      text: "Ingresa una cantidad válida.",
    });
    return;
  }

  $("#btnFinish").addClass("disabled");

  finishWorkShift(finalMoney);
});

function finishWorkShift(finalMoney) {
  Swal.fire({
    title: "Cargando",
    text: "Espera mientras se cierra el turno.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });

  fetch("/bread_factory/pos/controllers/finish_work_shift.php", {
    method: "POST",
    body: JSON.stringify({ final_money: finalMoney }),
  })
    .then((response) => response.json())
    .then((data) => {
      switch (data.result) {
        case "ok":
          printTicketReport(data.report);

          break;

        case "error":
        case "isset":
          Swal.close();
          break;

        default:
          Swal.fire(
            "Error de servidor",
            "Concata a tu administrador e indica el codigo de error: SERVER_ERR",
            "warning"
          );
          $("#btnFinish").removeClass("disabled");
          break;
      }
    })
    .catch((error) => {
      console.log("Request failed:", error);
      $("#btnFinish").removeClass("disabled");
      Swal.close();
    });
}

function printTicketReport(workShiftReport) {
  fetch("/bread_factory/pos/controllers/ticket/print_work_shift_report.php", {
    method: "POST",
    body: JSON.stringify({ work_shift_report: workShiftReport }),
  }).then((response) => {
    Swal.fire({
      icon: "success",
      title: "Turno cerrado",
      text: "Serás redireccionado en un momento.",
      timer: 2000,
      timerProgressBar: true,
      showConfirmButton: false,
      allowEscapeKey: false,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
      willClose: () => {
        window.location.replace("logout.php");
      },
    });
  });
}
