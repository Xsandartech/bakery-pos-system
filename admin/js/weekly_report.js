let result = getWeekNumber(new Date());
let week = result[1];
let year = result[0];

$("#weekNumber").val(week);

get_weekly_report(week, year);

function getWeekNumber(d) {
  // Copy date so don't modify original
  d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
  // Set to nearest Thursday: current date + 4 - current day number
  // Make Sunday's day number 7
  d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
  // Get first day of year
  var yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
  // Calculate full weeks to nearest Thursday
  var weekNo = Math.ceil(((d - yearStart) / 86400000 + 1) / 7);
  // Return array of year and week number
  return [d.getUTCFullYear(), weekNo];
}

$("#btnUpdate").click(() => {
  get_weekly_report($("#weekNumber").val(), year);
});

function get_weekly_report(week, year) {
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

  fetch("/bread_factory/admin/controllers/get_weekly_report.php", {
    method: "POST",
    body: JSON.stringify({
      week: week,
      year: year,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);

      if (data.result === "ok") {
        Swal.close();

        $("#datesWeek").text(
          data.first_date_week + " - " + data.last_date_week
        );

        let totalIncomesMorning = 0;
        let totalIncomesEvening = 0;
        let totalIncomes = 0;

        let totalExpensesMorning = 0;
        let totalExpensesEvening = 0;
        let totalExpenses = 0;

        for (let i = 0; i < 7; i++) {
          let totalIncomeMorning = Number(data.incomes_1[i]);
          let totalIncomeEvening = Number(data.incomes_2[i]);
          let totalIncomeDay = totalIncomeMorning + totalIncomeEvening;
          totalIncomesMorning += totalIncomeMorning;
          totalIncomesEvening += totalIncomeEvening;
          totalIncomes += totalIncomeDay;

          let totalExpenseMorning = Number(data.expenses_1[i]);
          let totalExpenseEvening = Number(data.expenses_2[i]);
          let totalExpenseDay = totalExpenseMorning + totalExpenseEvening;
          totalExpensesMorning += totalExpenseMorning;
          totalExpensesEvening += totalExpenseEvening;
          totalExpenses += totalExpenseDay;

          $("#in-m-" + i).text(formatter.format(totalIncomeMorning));
          $("#in-e-" + i).text(formatter.format(totalIncomeEvening));
          $("#total-in-" + i).text(formatter.format(totalIncomeDay));

          $("#ex-m-" + i).text(formatter.format(totalExpenseMorning));
          $("#ex-e-" + i).text(formatter.format(totalExpenseEvening));
          $("#total-ex-" + i).text(formatter.format(totalExpenseDay));
        }

        $("#total-in-m").text(formatter.format(totalIncomesMorning));
        $("#total-in-e").text(formatter.format(totalIncomesEvening));
        $("#total-incomes").text(formatter.format(totalIncomes));

        $("#total-ex-m").text(formatter.format(totalExpensesMorning));
        $("#total-ex-e").text(formatter.format(totalExpensesEvening));
        $("#total-expenses").text(formatter.format(totalExpenses));
      }
    })
    .catch((error) => {
      console.log("Error: ", error);
    });
}
