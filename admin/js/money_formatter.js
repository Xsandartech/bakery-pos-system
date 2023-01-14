//Money formatter
var formatter = new Intl.NumberFormat("es-MX", {
  style: "currency",
  currency: "MXN",
});
/*
  Example:
  let total = 1288
  console.log(formtatter.format(total));
  // $1,288.00
  */
