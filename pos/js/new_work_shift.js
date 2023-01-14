window.addEventListener("load", () => {
  //init numpad js
  numpad.attach({ target: document.getElementById("startingMoney") });
});

const startForm = document.getElementById("startForm");

startForm.addEventListener("submit", (e) => {
  e.preventDefault();

  let startingMoney = $("#startingMoney").val();

  if (startingMoney === "") {
    Swal.fire({
      icon: "warning",
      text: "Ingresa una cantidad válida.",
    });
    return;
  }

  $("#btnStart").addClass("disabled");

  startWorkShift(startingMoney);
});

function startWorkShift(startingMoney) {
  Swal.fire({
    title: "Cargando",
    text: "Espera mientras se abre el nuevo turno.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });

  fetch("/bread_factory/pos/controllers/start_work_shift.php", {
    method: "POST",
    body: JSON.stringify({ starting_money: startingMoney }),
  })
    .then((response) => response.json())
    .then((data) => {
      switch (data.result) {
        case "ok":
          window.location.replace("pos.php");
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
          $("#btnStart").removeClass("disabled");
          break;
      }
    })
    .catch((error) => {
      console.log("Request failed:", error);
      $("#btnStart").removeClass("disabled");
      Swal.close();
    });
}
