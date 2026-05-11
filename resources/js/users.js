"use strict";

import { Modal } from "bootstrap";

document.addEventListener("DOMContentLoaded", function () {
  const datatableUsers = $(".datatables-users");

  const userModalElement = document.getElementById("userModal");

  const userModal = Modal.getOrCreateInstance(userModalElement);

  const modalTitle = $("#userModal .modal-title");

  let dt_users;

  if (datatableUsers.length) {
    dt_users = new DataTable(datatableUsers, {
      processing: true,
      serverSide: true,
      ajax: {
        url: "/users/datatable",
      },
      columns: [
        { data: "fake_id" },
        { data: "name" },
        { data: "email" },
        { data: "created_at" },
        { data: "updated_at" },
        { data: "id" },
      ],
      columnDefs: [
        {
          orderable: false,
          targets: [0, 1, 2, 3, 4, -1],
        },
        {
          targets: 3,
          render: function (data, type, row) {
            const options = {
              day: "2-digit",
              month: "short",
              year: "numeric",
              hour: "2-digit",
              minute: "2-digit",
            };

            return `
              <div class="d-flex flex-column">
                <span class="text-muted">${row.creator}</span>
                <span class="fw-medium">${new Date(data).toLocaleString("en-GB", options)}</span>
              </div>
            `;
          },
        },
        {
          targets: 4,
          render: function (data, type, row) {
            const options = {
              day: "2-digit",
              month: "short",
              year: "numeric",
              hour: "2-digit",
              minute: "2-digit",
            };

            if (row.deleted_at !== null) {
              return `
                <div class="d-flex flex-column">
                  <span class="text-muted">${row.deleter}</span>
                  <span class="fw-medium">${new Date(row.deleted_at).toLocaleString("en-GB", options)}</span>
                </div>
              `;
            } else {
              return `
                <div class="d-flex flex-column">
                  <span class="text-muted">${row.editor}</span>
                  <span class="fw-medium">${new Date(data).toLocaleString("en-GB", options)}</span>
                </div>
              `;
            }
          },
        },
        {
          targets: -1,
          title: "Actions",
          render: function (data, type, full, meta) {
            if (full.deleted_at !== null) {
              return `
                <span class="text-nowrap">
                  <button class="btn btn-warning me-2 restore-record" data-id="${data}">
                    <i class="bx bx-recycle"></i>
                  </button>
                  <button class="btn btn-danger force-record" data-id="${data}">
                    <i class="bx bx-trash"></i>
                  </button>
                </span>
              `;
            }

            return `
              <span class="text-nowrap">
                <button class="btn btn-warning me-2 edit-record" data-id="${data}" data-bs-target="#userModal" data-bs-toggle="modal" data-bs-dismiss="modal">
                  <i class="bx bx-edit"></i>
                </button>
                <button class="btn btn-danger delete-record" data-id="${data}">
                  <i class="bx bx-trash-alt"></i>
                </button>
              </span>
            `;
          },
        },
      ],
      order: [[]],
      layout: {
        topStart: {
          features: [
            {
              pageLength: {
                menu: [10, 25, 50, 100],
                text: "Show _MENU_ entries",
              },
            },
          ],
        },
        topEnd: {
          features: [
            {
              search: {
                placeholder: "Search",
                text: "_INPUT_",
              },
            },
            {
              buttons: [
                {
                  text: "Create New",
                  className: "add-new mb-3 mb-md-0",
                  attr: {
                    "data-bs-toggle": "modal",
                    "data-bs-target": "#userModal",
                  },
                },
              ],
            },
          ],
        },
        bottomStart: {
          rowClass: "row mx-3 justify-content-between",
          features: ["info"],
        },
        bottomEnd: "paging",
      },
      createdRow: function (row, data) {
        if (data.deleted_at !== null) {
          $(row).addClass("table-deleted");
        }
      },
    });
  }

  const userForm = document.getElementById("userForm"),
    fillName = userForm.querySelector("#name"),
    fillEmail = userForm.querySelector("#email"),
    fillPassword = userForm.querySelector("#password"),
    btnSubmit = userForm.querySelector('button[type="submit"]');

  let editingId = null;

  $(".add-new").on("click", function () {
    modalTitle.html("Create New User");
    editingId = null;
    $(btnSubmit).html("Submit");
  });

  $(document).on("click", ".edit-record", function (e) {
    const id = $(this).data("id"),
      dtrModal = $(".dtr-bs-modal.show");

    if (dtrModal.length) {
      dtrModal.modal("hide");
    }

    modalTitle.html("Edit Existing User");
    $(btnSubmit).html("Save");

    // get data
    $.get(`/users/${id}`, function (data) {
      editingId = id;

      fillName.value = data.name || "";
      fillEmail.value = data.email || "";
    });
  });

  $(userForm).on("submit", function (e) {
    e.preventDefault();

    $(".form-control").removeClass("is-invalid");

    $(".invalid-feedback").text("");

    let valid = true;

    const name = fillName.value.trim();
    const email = fillEmail.value.trim();
    const password = fillPassword.value.trim();

    if (!name) {
      fillName.classList.add("is-invalid");

      $(".error-name").text("Name is required");

      valid = false;
    } else if (name.length < 4) {
      fillName.classList.add("is-invalid");

      $(".error-name").text("The name must be at least 4 characters long");

      valid = false;
    }

    if (!email) {
      fillEmail.classList.add("is-invalid");

      $(".error-email").text("Email is required");

      valid = false;
    } else {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!emailPattern.test(email)) {
        fillEmail.classList.add("is-invalid");

        $(".error-email").text("The email address is not valid");

        valid = false;
      }
    }

    if (!editingId) {
      if (!password) {
        fillPassword.classList.add("is-invalid");

        $(".error-password").text("Password is required");

        valid = false;
      } else if (password.length < 8) {
        fillPassword.classList.add("is-invalid");

        $(".error-password").text(
          "The password must be at least 8 characters long",
        );

        valid = false;
      } else {
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z]).+$/;

        if (!passwordPattern.test(password)) {
          fillPassword.classList.add("is-invalid");

          $(".error-password").text(
            "Password must contain uppercase and lowercase letters",
          );

          valid = false;
        }
      }
    }

    if (!valid) {
      return;
    }

    btnSubmit.disabled = true;

    btnSubmit.innerHTML = `
        <span
            class="spinner-border spinner-border-sm me-2">
        </span>

        Processing...
    `;

    let url = editingId ? `/users/update/${editingId}` : `/users/store`;

    $.ajax({
      data: $(userForm).serialize(),

      url: url,

      type: "POST",

      success: function (res) {
        btnSubmit.disabled = false;

        dt_users.draw(false);

        userModal.hide();

        new Notyf({
          duration: 3000,
          position: {
            x: "right",
            y: "top",
          },
        }).success(res.message);
      },

      error: function (xhr) {
        btnSubmit.disabled = false;

        btnSubmit.innerHTML = editingId ? "Save" : "Submit";

        let res = xhr.responseJSON;

        if (res?.errors) {
          Object.keys(res.errors).forEach((field) => {
            $(`#${field}`).addClass("is-invalid");

            $(`.error-${field}`).text(res.errors[field]);
          });
        }

        new Notyf({
          duration: 3000,
          position: {
            x: "right",
            y: "top",
          },
        }).error(res?.message || "Something went wrong");
      },
    });
  });

  userModalElement.addEventListener("hidden.bs.modal", function () {
    userForm.reset();
    editingId = null;
  });

  $(document).on("click", ".delete-record", function () {
    const id = $(this).data("id");

    Swal.fire({
      title: "Are you sure?",

      text: "This user will be moved to trash",

      icon: "warning",

      showCancelButton: true,

      confirmButtonText: "Yes, delete it!",

      customClass: {
        confirmButton: "btn btn-danger me-2",

        cancelButton: "btn btn-light",
      },

      buttonsStyling: false,
    }).then((result) => {
      if (!result.isConfirmed) {
        return;
      }

      $.ajax({
        url: `/users/delete/${id}`,

        type: "DELETE",

        success: function (res) {
          dt_users.draw(false);

          new Notyf({
            duration: 3000,
            position: {
              x: "right",
              y: "top",
            },
          }).success(res.message);
        },

        error: function (xhr) {
          new Notyf({
            duration: 3000,
            position: {
              x: "right",
              y: "top",
            },
          }).error(xhr.responseJSON?.message || "Something went wrong");
        },
      });
    });
  });

  $(document).on("click", ".restore-record", function () {
    const id = $(this).data("id");

    Swal.fire({
      title: "Restore user?",

      icon: "question",

      showCancelButton: true,

      confirmButtonText: "Restore",

      customClass: {
        confirmButton: "btn btn-success me-2",

        cancelButton: "btn btn-light",
      },

      buttonsStyling: false,
    }).then((result) => {
      if (!result.isConfirmed) {
        return;
      }

      $.ajax({
        url: `/users/restore/${id}`,

        type: "POST",

        success: function (res) {
          dt_users.draw(false);

          new Notyf({
            duration: 3000,
            position: {
              x: "right",
              y: "top",
            },
          }).success(res.message);
        },

        error: function (xhr) {
          new Notyf({
            duration: 3000,
            position: {
              x: "right",
              y: "top",
            },
          }).error(xhr.responseJSON?.message || "Something went wrong");
        },
      });
    });
  });

  $(document).on("click", ".force-record", function () {
    const id = $(this).data("id");

    Swal.fire({
      title: "Permanent delete?",

      text: "This action cannot be undone",

      icon: "warning",

      showCancelButton: true,

      confirmButtonText: "Permanent Delete",

      customClass: {
        confirmButton: "btn btn-danger me-2",

        cancelButton: "btn btn-light",
      },

      buttonsStyling: false,
    }).then((result) => {
      if (!result.isConfirmed) {
        return;
      }

      $.ajax({
        url: `/users/force/${id}`,

        type: "DELETE",

        success: function (res) {
          dt_users.draw(false);

          new Notyf({
            duration: 3000,
            position: {
              x: "right",
              y: "top",
            },
          }).success(res.message);
        },

        error: function (xhr) {
          new Notyf({
            duration: 3000,
            position: {
              x: "right",
              y: "top",
            },
          }).error(xhr.responseJSON?.message || "Something went wrong");
        },
      });
    });
  });
});
