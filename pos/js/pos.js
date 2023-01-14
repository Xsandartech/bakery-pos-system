let indexClicked;

class POS {
  constructor() {
    this.items = {};
    this.total = {};
  }

  addItem(quantity) {
    //if the product does not exist
    if (!this.items.hasOwnProperty(indexClicked)) {
      let id = listItems[indexClicked].id;
      let type = listItems[indexClicked].type;

      let subTotal = quantity * listItems[indexClicked].price;

      this.items[indexClicked] = {
        id: id,
        type: type,
        quantity: quantity,
        subTotal: subTotal,
      };

      //display item :)
      let trItem = document.createElement("tr");
      trItem.setAttribute("id", indexClicked);

      trItem.innerHTML = `
          <td>${listItems[indexClicked].description}</td>
          <td id="quantity_${indexClicked}">${quantity}</td>
          <td id="subTotal_${indexClicked}">${formatter.format(subTotal)}</td>`;

      $("#posCartTable").append(trItem);

      this.calcTotal();
    } else {
      //update quantity and subtotal
      let newQuantity = this.items[indexClicked].quantity + quantity;
      this.updateQuantity(newQuantity);
    }
  }

  calcTotal() {
    let totalSale = 0;
    Object.entries(this.items).forEach(([key, value]) => {
      totalSale += value.subTotal;
    });

    $("#textTotalSale").text("Total: " + formatter.format(totalSale));
  }

  updateQuantity(quantity) {
    let newQuantity, newSubTotal;

    newQuantity = this.items[indexClicked].quantity = quantity;
    newSubTotal = newQuantity * listItems[indexClicked].price;

    this.items[indexClicked].quantity = newQuantity;
    this.items[indexClicked].subTotal = newSubTotal;

    $("#quantity_" + indexClicked).text(newQuantity);
    $("#subTotal_" + indexClicked).text(formatter.format(newSubTotal));

    this.calcTotal();
  }

  removeItem() {
    delete this.items[indexClicked];
    $(rowSelected).remove();
    this.calcTotal();
  }

  emptyPOS() {
    this.items = {};
    $("#posCartTable tr").remove();
    this.calcTotal();
  }
}

let pos = new POS();

let optionsModal;
window.addEventListener("load", () => {
  //init numpad js
  numpad.attach({
    target: document.getElementById("inputQuantity"),
    decimal: false,
    onselect: () => {
      //add item to cart after selecting a number
      let quantity = parseInt($("#inputQuantity").val());

      if (quantity > 0) pos.addItem(quantity);

      $("#inputQuantity").val(0);
    },
  });

  optionsModal = new bootstrap.Modal(
    document.getElementById("optionsModal"),
    {}
  );
});

//contains products and promos created by the user
let listItems = {};

getPosProducts();

//Open options modal when click on a product/promo row
let rowSelected;
$("#posTable").on("click", "tr", function (e) {
  rowSelected = $(this);
  let id = $(this).attr("id");

  if (id === undefined) return;

  indexClicked = id;
  console.log(id);

  $("#optionsModalLabel").text(listItems[id].description);
  $("#quantityLabel").text(pos.items[id].quantity);

  optionsModal.show();
});

//buttons for remove or add quantity
$("#btnRemoveOne").click(() => {
  let currentQuantity = parseInt($("#quantityLabel").text());
  currentQuantity--;
  if (currentQuantity < 1) currentQuantity = 1;
  $("#quantityLabel").text(currentQuantity);
});
$("#btnAddOne").click(() => {
  let currentQuantity = parseInt($("#quantityLabel").text());
  currentQuantity++;
  $("#quantityLabel").text(currentQuantity++);
});

$("#btnOptionsContinue").click(() => {
  let newQuantity = parseInt($("#quantityLabel").text());
  pos.updateQuantity(newQuantity);
  optionsModal.hide();
});

$("#btnRemove").click(() => {
  optionsModal.hide();

  Swal.fire({
    title: "¿Estás seguro?",
    text: "Se removerá el producto para la venta.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Confirmar",
    cancelButtonText: "Cancelar",
    focusConfirm: false,
    focusCancel: true,
  }).then((result) => {
    if (result.isConfirmed) {
      pos.removeItem();
    }
  });
});

$("#btnCancelSale").click(() => {
  if (Object.keys(pos.items).length !== 0) {
    Swal.fire({
      title: "¿Estás seguro?",
      text: "Se vaciará la lista de productos para la venta actual.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Confirmar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        pos.emptyPOS();
      }
    });
  }
});

let paymentMethod;

$("#btnCheckOut").click(() => {
  if (Object.keys(pos.items).length !== 0) {
    Swal.fire({
      title: "Método de pago",
      icon: "question",
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: "Efectivo",
      confirmButtonColor: "#00B74A",
      denyButtonText: "Tarjeta",
      denyButtonColor: "#1266F1",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        //cash payment
        paymentMethod = 0;
        showConfirmSale();
      } else if (result.isDenied) {
        //card payment
        paymentMethod = 1;
        showConfirmSale();
      }
    });
  }
});

function showConfirmSale() {
  Swal.fire({
    title: "¿Terminar venta?",
    text: "La venta quedará guardada.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Confirmar",
    cancelButtonText: "Cancelar",
    showLoaderOnConfirm: true,
    backdrop: true,
    preConfirm: () => {
      return fetch("/bread_factory/pos/controllers/finish_sale.php", {
        method: "POST",
        body: JSON.stringify({
          cart: pos.items,
          payment_method: paymentMethod,
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
          //print ticket if the payment method was cash
          if (paymentMethod === 0) {
            let idSale = result.value.id_sale;
            printTicket(idSale);
            console.log("Printing ticket (id: " + idSale + ")");
          }

          pos.emptyPOS();

          Swal.fire({
            icon: "success",
            title: "Venta completada",
            showConfirmButton: false,
            timer: 1500,
          });

          break;
      }
    }
  });
}
function getPosProducts() {
  Swal.fire({
    title: "Cargando",
    text: "Espera mientras se obtienen los productos.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });

  fetch("/bread_factory/pos/controllers/get_pos_products.php")
    .then((response) => response.json())
    .then((data) => {
      Swal.close();

      let index = 0;

      //First add products
      data.products.forEach((product) => {
        let productId = product.id;
        let productDescription = product.description;
        let productPrice = product.price;
        let productCost = product.cost;
        let color = product.color;

        //save product to list
        let temp_product = {
          id: productId,
          description: productDescription,
          price: parseFloat(productPrice),
          type: "product",
        };

        listItems[index] = temp_product;

        createBoxProduct(index++, productDescription, color);

        //And then, check if the current product has promos
        data.promos.forEach((promo) => {
          let promoId = promo.id;
          let promoDescription = promo.description;
          let productIdPromo = promo.id_pos_product;
          let promoPieces = parseFloat(promo.pieces).toFixed(2);
          let promoUnitPrice = parseFloat(promo.price).toFixed(2);
          let promoPrice = parseFloat(promoPieces * promoUnitPrice).toFixed(2); //calc the real price

          //there is a product promo!
          if (productId === productIdPromo) {
            //save promo to list
            let temp_promo = {
              id: promoId,
              description: promoDescription,
              price: parseFloat(promoPrice),
              type: "promo",
            };

            listItems[index] = temp_promo;

            createBoxProduct(index++, promoDescription, color);
          }
        });
      });

      console.log(listItems);
    })
    .catch((error) => {
      console.log("Error: ", error);
    });
}

function createBoxProduct(id, description, color) {
  let itemElement = document.createElement("div");
  //itemElement.classList.add("col-lg-2");
  itemElement.classList.add("col-md-3");
  itemElement.classList.add("col-6");

  let itemBox = document.createElement("div");

  itemBox.classList.add("box");
  itemBox.classList.add(color);
  $(itemElement).append(itemBox);

  let itemDescription = document.createElement("div");
  itemDescription.classList.add("product-desc");
  itemDescription.innerText = description;
  itemDescription.setAttribute("index", id);
  itemDescription.addEventListener("click", inputQuantity);
  $(itemElement).append(itemDescription);

  $("#posProducts").append(itemElement);
}

function inputQuantity(e) {
  indexClicked = e.currentTarget.getAttribute("index");
  $("#inputQuantity").click();
}

function printTicket(idSale) {
  fetch("/bread_factory/pos/controllers/ticket/print_sale.php", {
    method: "POST",
    body: JSON.stringify({ id_sale: idSale }),
  });
}
