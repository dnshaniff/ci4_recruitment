import "../css/app.css";
import "boxicons/css/boxicons.min.css";

import "bootstrap";

import $ from "jquery";
window.$ = window.jQuery = $;

import DataTable from "datatables.net-bs5";
window.DataTable = DataTable;
import "datatables.net-buttons-bs5";

import Swal from "sweetalert2";
window.Swal = Swal;

import { Notyf } from "notyf";
window.Notyf = Notyf;

document.addEventListener("DOMContentLoaded", function () {
  const togglePassword = document.querySelector("#togglePassword");

  const password = document.querySelector("#password");

  if (togglePassword && password) {
    togglePassword.addEventListener("click", function () {
      const type =
        password.getAttribute("type") === "password" ? "text" : "password";

      password.setAttribute("type", type);

      this.innerHTML =
        type === "password"
          ? '<i class="bx bx-show"></i>'
          : '<i class="bx bx-hide"></i>';
    });
  }
});
