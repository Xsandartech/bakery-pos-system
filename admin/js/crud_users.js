let userModal;
let task;
let userIdClicked;

$(document).ready(function () {
  $("#search").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    $("#products tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  userModal = new bootstrap.Modal(document.getElementById("userModal"), {});
});

$("input#userName").on({
  keydown: function (e) {
    if (e.which === 32) return false;
  },
  change: function () {
    this.value = this.value.replace(/\s/g, "");
  },
});

$("input#userName").keyup(function () {
  this.value = this.value.toLowerCase();
});

getUsers();

function getUsers() {
  Swal.fire({
    title: "Cargando",
    text: "Espera mientras se obtienen los usuarios.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });

  fetch("/bread_factory/admin/controllers/get_users.php")
    .then((response) => response.json())
    .then((data) => {
      Swal.close();

      console.log(data);

      $("#users").html("");

      let index = 1;

      data.forEach((user) => {
        let userId = user.id;
        let displayName = user.display_name;
        let userName = user.user_name;
        //let password = user.password;
        let isAdmin = user.is_admin;
        let accountType;

        switch (isAdmin) {
          case "0":
            accountType = `<i class="fas fa-user"></i> Empleado`;
            txtColor = "text-secondary";
            break;

          case "1":
            accountType = `<i class="fas fa-user-cog"></i> Administrador`;
            txtColor = "text-success";
            break;
        }

        let trUser = document.createElement("tr");
        trUser.innerHTML = `
        <th scope="row">${index++}</th>
        <td>${displayName}</td>
        <td>${userName}</td>
        <td class="${txtColor}">${accountType}</td>
        <td>
            <ul class="list-inline m-0">
                <li class="list-inline-item">
                    <button id="edit${userId}" class="btn btn-success btn-sm rounded-0" type="button"
                        data-toggle="tooltip" data-placement="top" title="Edit"
                        id-user="${userId}"><i
                            class="fa fa-edit"></i></button>
                </li>
                <li class="list-inline-item">
                    <button id="delete${userId}" class="btn btn-danger btn-sm rounded-0" type="button"
                        data-toggle="tooltip" data-placement="top" title="Delete"
                        id-user="${userId}"><i
                            class="fa fa-trash"></i></button>
                </li>
            </ul>
        </td>`;

        $("#users").append(trUser);

        document
          .getElementById(`delete${userId}`)
          .addEventListener("click", deleteUser);

        document
          .getElementById(`edit${userId}`)
          .addEventListener("click", editUserClicked);
      });
    })
    .catch((error) => {
      console.log("Error: ", error);
    });
}

$("#btnNewUser").click(() => {
  task = "add";
  $("#userModalLabel").text("Nuevo usuario");
  userModal.show();
});

function editUserClicked(e) {
  let id = e.currentTarget.getAttribute("id-user");
  userIdClicked = id;
  task = "edit";

  //get data product before show modal
  getUserData(userIdClicked);
}

$("#btnContinue").click(() => {
  let displayName = $("#displayName").val();
  let userName = $("#userName").val();
  let password = $("#password").val();
  let confirmPassword = $("#confirmPassword").val();
  let isAdmin = $("#isAdmin").val();

  if (
    displayName === "" ||
    userName === "" ||
    password === "" ||
    isAdmin === ""
  ) {
    Swal.fire({
      icon: "warning",
      title: "Oops...",
      text: "Debes de completar todos los campos.",
    });
    return;
  }

  if (password != confirmPassword) {
    Swal.fire({
      icon: "warning",
      title: "Oops...",
      text: "Las contraseñas no coinciden.",
    });
    return;
  }

  switch (task) {
    case "add":
      insertUser(displayName, userName, password, isAdmin);
      break;
    case "edit":
      editUser(userIdClicked, displayName, userName, password, isAdmin);
      break;
  }
});

$("#btnCancel").click(() => {
  $("#userForm").trigger("reset");
});

function deleteUser(e) {
  let id = e.currentTarget.getAttribute("id-user");

  Swal.fire({
    title: "¿Estás seguro?",
    text: "Se eliminará el usuario seleccionado.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Eliminar",
    cancelButtonText: "Cancelar",
    showLoaderOnConfirm: true,
    backdrop: true,
    preConfirm: () => {
      return fetch("/bread_factory/admin/controllers/delete_user.php", {
        method: "POST",
        body: JSON.stringify({
          id: id,
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
          Swal.fire({
            icon: "success",
            title: "Se eliminó el usuario correctamente.",
            showConfirmButton: false,
            timer: 1500,
            didClose: () => {
              getUsers();
            },
          });
          break;

        case "isset":
        case "error":
          Swal.fire({
            title: "Error",
            text: "No se eliminó el usuario. Inténtalo de nuevo y si el error persiste, contacta a tu administrador.",
            icon: "error",
            confirmButtonText: "Aceptar",
          });
          break;
      }
    }
  });
}

function editUser(id, displayName, userName, password, isAdmin) {
  fetch("/bread_factory/admin/controllers/edit_user.php", {
    method: "POST",
    body: JSON.stringify({
      id: id,
      display_name: displayName,
      user_name: userName,
      password: password,
      is_admin: isAdmin,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      switch (data.status) {
        case "ok":
          Swal.close();
          userModal.hide();

          $("#userForm").trigger("reset");

          //getMovementsHistory();
          Swal.fire({
            icon: "success",
            title: "Se editó el usuario correctamente.",
            showConfirmButton: false,
            timer: 1500,
            didClose: () => {
              getUsers();
            },
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

function getUserData(id) {
  Swal.fire({
    title: "Cargando",
    text: "Espera mientras se obtienen la información.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
    allowEscapeKey: false,
    allowOutsideClick: false,
  });

  fetch("/bread_factory/admin/controllers/get_user_data.php", {
    method: "POST",
    body: JSON.stringify({
      id: id,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      $("#displayName").val(data.display_name);
      $("#userName").val(data.user_name);
      $("#password").val(data.password);
      $("#confirmPassword").val(data.password);
      $("#isAdmin").val(data.is_admin);

      $("#userModalLabel").text("Editar usuario");

      Swal.close();
      userModal.show();
    })
    .catch((error) => {
      console.log("Request failed:", error);
    });
}

function insertUser(displayName, userName, password, isAdmin) {
  fetch("/bread_factory/admin/controllers/insert_user.php", {
    method: "POST",
    body: JSON.stringify({
      display_name: displayName,
      user_name: userName,
      password: password,
      is_admin: isAdmin,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      switch (data.status) {
        case "ok":
          Swal.close();
          userModal.hide();

          $("#userForm").trigger("reset");

          //getMovementsHistory();
          Swal.fire({
            icon: "success",
            title: "Se registró el usuario correctamente.",
            showConfirmButton: false,
            timer: 1500,
            didClose: () => {
              getUsers();
            },
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
