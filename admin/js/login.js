const loginForm = document.getElementById("loginForm");

loginForm.addEventListener("submit", (e) => {
  e.preventDefault();

  let userName = $("#userName").val();
  let password = $("#password").val();

  $("#btnLogin").addClass("disabled");

  logIn(userName, password);
});

function logIn(userName, password) {
  fetch("/bread_factory/admin/controllers/login.php", {
    method: "POST",
    body: JSON.stringify({ user_name: userName, password: password }),
  })
    .then((response) => response.json())
    .then((data) => {
      switch (data.status) {
        case "logged":
          window.location.replace("daily_report.php");
          break;

        case "invalid_credentials":
          Swal.fire(
            "Vuelve a intentarlo",
            "Las credenciales que estás usando no son válidas.",
            "error"
          );
          $("#btnLogin").removeClass("disabled");
          break;

        case "is_not_admin":
          Swal.fire(
            "Acceso denegado",
            "Solamente usuarios con permiso de administrador pueden entrar.",
            "warning"
          );
          $("#btnLogin").removeClass("disabled");
          break;

        default:
          Swal.fire(
            "Error de servidor",
            "Concata a tu administrador e indica el codigo de error: SERVER_ERR",
            "warning"
          );
          $("#btnLogin").removeClass("disabled");
          break;
      }
    })
    .catch((error) => {
      console.log("Request failed:", error);
      $("#btnLogin").removeClass("disabled");
    });
}
