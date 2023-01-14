let date = new Date().toISOString().split("T")[0];

console.log(date.toLocaleString());
$("#datepicker").val(date);

$("#btnUpdate").click(() => {
  get_daily_report($("#datepicker").val(), parseInt($("#workShift").val()));
});

get_daily_report(date, 0);

function get_daily_report(date, workShift) {
  Swal.fire({
    title: "Cargando",
    text: "Espera mientras se obtiene el reporte.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });

  fetch("/bread_factory/admin/controllers/get_daily_report.php", {
    method: "POST",
    body: JSON.stringify({
      date: date,
      work_shift: workShift,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);

      let index;

      index = Object.keys(data.expenses).length;
      $("#expenses").html("");
      let totalExpenses = 0;

      data.expenses.forEach((expenses) => {
        if (Object.keys(expenses).length > 0) {
          expenses.forEach((expense) => {
            totalExpenses += parseFloat(expense.amount);

            let trElem = document.createElement("tr");
            trElem.innerHTML = `
                    <tr>
                        <th scope="row">${index--}</th>
                        <td>${expense.datetime}</td>
                        <td>${expense.resp}</td>
                        <td>${expense.description}</td>
                        <td>${formatter.format(parseFloat(expense.amount))}</td>
                    </tr>`;

            $("#expenses").append(trElem);
          });
        }
      });

      $("#total-expenses").text("Gastos: " + formatter.format(totalExpenses));

      index = Object.keys(data.withdrawals).length;
      $("#withdrawals").html("");
      let totalWithdrawals = 0;

      data.withdrawals.forEach((withdrawals) => {
        if (Object.keys(withdrawals).length > 0) {
          withdrawals.forEach((withdrawal) => {
            totalWithdrawals += parseFloat(withdrawal.amount);
            let trElem = document.createElement("tr");
            trElem.innerHTML = `
                      <tr>
                          <th scope="row">${index--}</th>
                          <td>${withdrawal.datetime}</td>
                          <td>${withdrawal.resp}</td>
                          <td>${withdrawal.description}</td>
                          <td>${formatter.format(
                            parseFloat(withdrawal.amount)
                          )}</td>
                      </tr>`;

            $("#withdrawals").append(trElem);
          });
        }
      });

      $("#total-withdrawals").text(
        "Retiros: " + formatter.format(totalWithdrawals)
      );

      $("#total-incomes").text(
        "Total de ingresos (Venta real): " + formatter.format(data.incomes)
      );

      let finalMoney = data.incomes - totalExpenses - totalWithdrawals;
      $("#final-money").text("Efectivo final: " + formatter.format(finalMoney));

      $("#remaining-money").text(
        "Efectivo sobrante: " + formatter.format(data.remaining_money)
      );

      $("#missing-money").text(
        "Efectivo faltante: " + formatter.format(data.missing_money)
      );

      Swal.close();
    })
    .catch((error) => {
      console.log("Error: ", error);
    });
}
