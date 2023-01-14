getSettings();

$("input[type=radio][name=printerMode]").change(function () {
  if (this.value == "0") {
    $("#lanSettings").addClass("d-none");
    $("#usbSettings").removeClass("d-none");
  } else if (this.value == "1") {
    $("#usbSettings").addClass("d-none");
    $("#lanSettings").removeClass("d-none");
  }
});

$("#btnSaveMode").click(() => {
  let printerMode = $('input[name="printerMode"]:checked').val();
  let printerName = $("#printerName").val();
  let prinetrIP = $("#printerIP").val();
  let printerPort = $("#printerPort").val();

  //console.log(printerName);

  updateMode(printerMode, printerName, prinetrIP, printerPort);
});

$("#btnSaveTicket").click(() => {
  let printerCols = $("#printerCols").val();
  let ticketTitle = $("#ticketTitle").val();
  let ticketSubtitle = $("#ticketSubtitle").val();
  let ticketFooter = $("#ticketFooter").val();

  updateTicket(printerCols, ticketTitle, ticketSubtitle, ticketFooter);
});

$("#btnPrintTest").click(() => {
  printTest();
});

function getSettings() {
  Swal.fire({
    title: "Cargando",
    text: "Espera mientras se obtiene la información.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });

  fetch(
    "/bread_factory/pos/controllers/printer_settings/get_printer_settings.php"
  )
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      Swal.close();

      let printerMode = parseInt(data.printer_mode);

      $("[name=printerMode]").val([printerMode]);

      let printerName = data.printer_usb_name;
      $("#printerName").val(printerName);

      let printerIP = data.printer_ip;
      $("#printerIP").val(printerIP);

      let printerPort = data.printer_port;
      $("#printerPort").val(printerPort);

      if (printerMode === 0) $("#lanSettings").addClass("d-none");
      else if (printerMode === 1) $("#usbSettings").addClass("d-none");

      let printerCols = data.printer_cols;
      let ticketTitle = data.ticket_title;
      let ticketSubtitle = data.ticket_subtitle;
      let ticketFooter = data.ticket_footer;

      $("#printerCols").val(printerCols);
      $("#ticketTitle").val(ticketTitle);
      $("#ticketSubtitle").val(ticketSubtitle);
      $("#ticketFooter").val(ticketFooter);
    })
    .catch((error) => {
      console.log("Error: ", error);
    });
}

function updateMode(printerMode, printerName, prinetrIP, printerPort) {
  fetch(
    "/bread_factory/pos/controllers/printer_settings/update_printer_mode.php",
    {
      method: "POST",
      body: JSON.stringify({
        printer_mode: printerMode,
        printer_usb_name: printerName,
        printer_ip: prinetrIP,
        printer_port: printerPort,
      }),
    }
  )
    .then((response) => response.json())
    .then((data) => {
      switch (data.status) {
        case "ok":
          Swal.close();

          //getMovementsHistory();
          Swal.fire({
            icon: "success",
            title: "Se guardaron los cambios correctamente.",
            showConfirmButton: false,
            timer: 1500,
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

function updateTicket(printerCols, ticketTitle, ticketSubtitle, ticketFooter) {
  fetch(
    "/bread_factory/pos/controllers/printer_settings/update_printer_ticket.php",
    {
      method: "POST",
      body: JSON.stringify({
        printer_cols: printerCols,
        ticket_title: ticketTitle,
        ticket_subtitle: ticketSubtitle,
        ticket_footer: ticketFooter,
      }),
    }
  )
    .then((response) => response.json())
    .then((data) => {
      switch (data.status) {
        case "ok":
          Swal.close();

          //getMovementsHistory();
          Swal.fire({
            icon: "success",
            title: "Se guardaron los cambios correctamente.",
            showConfirmButton: false,
            timer: 1500,
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

function printTest() {
  fetch("/bread_factory/pos/controllers/ticket/test_printer.php")
    .then((response) => response.json())
    .then((data) => {
      let status = data.status;

      if (status === "ok") {
        Swal.fire({
          icon: "success",
          title: "Prueba de impresión con éxito.",
          showConfirmButton: false,
          timer: 1500,
        });
      }
    })
    .catch((error) => {
      Swal.fire({
        icon: "error",
        title: "Error de impresión",
        text: "Verifica que la impresora térmica esté encendida y conectada correctamente.",
      });
    });
}
