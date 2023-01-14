let ticketModal, reportModal, idSaleClicked, salesReport;

$(document).ready(function () {
  $("#search").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    $("#sales tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  ticketModal = new bootstrap.Modal(document.getElementById("ticketModal"), {});
  reportModal = new bootstrap.Modal(document.getElementById("reportModal"), {});
});

getSales();
function getSales() {
  Swal.fire({
    title: "Cargando",
    text: "Espera mientras se obtienen las ventas.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });

  fetch("/bread_factory/pos/controllers/get_work_shift_sales.php")
    .then((response) => response.json())
    .then((data) => {
      Swal.close();

      let index = Object.keys(data).length;

      data.forEach((sale) => {
        let paymentMethod = sale.payment_method;

        let labelPayment;
        if (paymentMethod === "0")
          labelPayment = `<i class="fas fa-coins"></i> Efectivo`;
        else {
          labelPayment = `<i class="fas fa-credit-card"></i> Tarjeta`;
        }

        let trElem = document.createElement("tr");
        trElem.innerHTML = `
        <th scope="row">${index}</th>
        <td>${sale.resp}</td>
        <td>${sale.datetime}</td>
        <td class="text-success">${labelPayment}</td>
        <td>${formatter.format(sale.total)}</td>`;

        let tdBtn = document.createElement("td");
        let btnViewTicket = document.createElement("button");
        btnViewTicket.addEventListener("click", onViewTicket);
        $(btnViewTicket).addClass("btn");
        $(btnViewTicket).addClass("btn-labeled");
        $(btnViewTicket).addClass("btn-secondary");
        $(btnViewTicket).addClass("btn-reprint");
        $(btnViewTicket).attr("index", index--);
        $(btnViewTicket).attr("idSale", sale.id);
        $(btnViewTicket).html(`<span class="btn-label"><i
        class="fas fa-receipt"></i></span>Ver ticket`);

        $(tdBtn).append(btnViewTicket);
        $(trElem).append(tdBtn);

        $("#sales").append(trElem);
      });
    })
    .catch((error) => {
      console.log("Error: ", error);
    });
}

function onViewTicket(e) {
  let index = e.currentTarget.getAttribute("index");
  idSaleClicked = e.currentTarget.getAttribute("idSale");

  fetch("/bread_factory/pos/controllers/ticket/get_ticket_data.php", {
    method: "POST",
    body: JSON.stringify({ id_sale: idSaleClicked }),
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      $("#ticketTable").html("");
      $("#totalTicketSale").text(formatter.format(data.sale_data[0].total));
      $("#ticketDatetime").text(data.sale_data[0].datetime);

      data.items.forEach((item) => {
        let trItem = document.createElement("tr");
        trItem.innerHTML = `
        <td>${item.quantity}x ${item.description}</td>
        <td>${formatter.format(item.subtotal)}</td>`;

        $("#ticketTable").append(trItem);
      });
    });

  $("#ticketModalLabel").text("Ticket de venta");

  ticketModal.show();
}

$("#btnReprint").click(() => {
  reprintTicket();
});

$("#btnPrintReport").click(() => {
  printTicketSaleReport();
});

$("#btnReport").click(() => {
  /*Swal.fire({
    title: "Cargando",
    text: "Espera mientras se obtienen las ventas.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });*/

  fetch("/bread_factory/pos/controllers/get_work_shift_sales_report.php")
    .then((response) => response.json())
    .then((data) => {
      //console.log(data);

      //Swal.close();
      let products = {};
      let promos = {};

      let report = {};

      let sold = 0;

      $("#report").html("");

      data.forEach((items) => {
        items["products"].forEach((product) => {
          let id = product.id;
          let description = product.description;
          let quantity = parseInt(product.quantity);
          let price = parseFloat(product.price);
          let subtotal = quantity * price;
          sold += subtotal;

          if (!report.hasOwnProperty("product_" + id)) {
            report["product_" + id] = {
              description: description,
              quantity: quantity,
              subtotal: subtotal,
            };
          } else {
            report["product_" + id].quantity += quantity;
            report["product_" + id].subtotal += subtotal;
          }
        });

        items["promos"].forEach((promo) => {
          let id = promo.id;
          let description = promo.description;
          let quantity = parseInt(promo.quantity);
          let pieces = parseInt(promo.pieces);
          let price = parseFloat(promo.price);
          let finalPrice = pieces * price * quantity;
          let subtotal = finalPrice;
          sold += subtotal;

          if (!report.hasOwnProperty("promo" + id)) {
            report["promo" + id] = {
              description: description,
              quantity: quantity,
              subtotal: subtotal,
            };
          } else {
            report["promo" + id].quantity += quantity;
            report["promo" + id].subtotal += subtotal;
          }
        });
      });

      salesReport = {
        items: report,
        total_sold: parseFloat(sold).toFixed(2),
      };

      //console.log(salesReport);

      Object.entries(report).forEach(([key, product]) => {
        Object.entries(product).forEach(([key]) => {
          //console.log(key);
        });

        //console.log(product.description);
        let item = {
          description: product.quantity + "x " + product.description,
          subtotal: product.subtotal,
        };

        let trReport = document.createElement("tr");
        trReport.innerHTML = `
        <td>${product.description}</td>
        <td>${product.quantity}</td>
        <td>${formatter.format(product.subtotal)}</td>
        `;

        $("#report").append(trReport);
      });

      $("#labelSold").text("Vendido: " + formatter.format(sold));

      reportModal.show();
      //console.log(products);
    })
    .catch((error) => {
      console.log("Error: ", error);
    });
});

function reprintTicket() {
  fetch("/bread_factory/pos/controllers/ticket/print_sale.php", {
    method: "POST",
    body: JSON.stringify({ id_sale: idSaleClicked }),
  })
    .then((response) => response.json())
    .then((data) => {
      let status = data.status;

      if (status === "ok") {
        Swal.fire({
          icon: "success",
          title: "Se reimprimió el ticket de venta.",
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

function printTicketSaleReport() {
  fetch("/bread_factory/pos/controllers/ticket/print_sales_report.php", {
    method: "POST",
    body: JSON.stringify({ sales_report: salesReport }),
  })
    .then((response) => response.json())
    .then((data) => {
      let status = data.status;

      if (status === "ok") {
        Swal.fire({
          icon: "success",
          title: "Se reimprimió el reporte de venta.",
          showConfirmButton: false,
          timer: 1500,
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error de impresión",
          text: "Hello",
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
