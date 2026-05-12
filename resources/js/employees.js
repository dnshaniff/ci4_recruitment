"use strict";

import { Modal } from "bootstrap";

document.addEventListener("DOMContentLoaded", function () {
  const datatableEmployees = $(".datatables-employees"),
    employeeModalElement = document.getElementById("employeeModal"),
    employeeModal = Modal.getOrCreateInstance(employeeModalElement),
    modalTitle = $("#employeeModal .modal-title");

  let dt_employees;

  if (datatableEmployees.length) {
    dt_employees = new DataTable(datatableEmployees, {
      processing: true,
      serverSide: true,
      ajax: {
        url: "/employees/datatable",
      },
      columns: [
        { data: "fake_id" },
        { data: "name" },
        { data: "email" },
        { data: "position" },
        { data: "gender" },
        { data: "created_at" },
        { data: "updated_at" },
        { data: "id" },
      ],
      columnDefs: [
        {
          orderable: false,
          targets: [0, 1, 2, 3, 4, 5, 6, -1],
        },
        {
          targets: 5,
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
          targets: 6,
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
                <button class="btn btn-warning me-2 edit-record" data-id="${data}" data-bs-target="#employeeModal" data-bs-toggle="modal" data-bs-dismiss="modal">
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
                    "data-bs-target": "#employeeModal",
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

  const employeeForm = document.getElementById("employeeForm"),
    employeeCode = employeeForm.querySelector("#employee_code"),
    fillName = employeeForm.querySelector("#name"),
    fillEmail = employeeForm.querySelector("#email"),
    fillPosition = employeeForm.querySelector("#position"),
    selectGender = employeeForm.querySelector("#gender"),
    uploadPhoto = employeeForm.querySelector("#photo"),
    photoPreviewWrapper = employeeForm.querySelector("#photoPreviewWrapper"),
    photoPreview = employeeForm.querySelector("#photoPreview"),
    btnSubmit = employeeForm.querySelector('button[type="submit"]');

  let editingId = null,
    originalPhoto = "https://placehold.co/600x400?text=No+Image";

  uploadPhoto.addEventListener("change", function (e) {
    const file = e.target.files[0];

    if (!file) {
      photoPreview.src = originalPhoto;

      uploadPhoto.classList.remove("is-invalid");

      $(".error-photo").text("");

      return;
    }

    const allowedTypes = ["image/jpeg", "image/jpg"];

    if (!allowedTypes.includes(file.type)) {
      uploadPhoto.classList.add("is-invalid");

      $(".error-photo").text("Photo must be JPG/JPEG");

      uploadPhoto.value = "";

      photoPreview.src = originalPhoto;

      return;
    }

    if (file.size > 307200) {
      uploadPhoto.classList.add("is-invalid");

      $(".error-photo").text("Photo maximum 300KB");

      uploadPhoto.value = "";

      photoPreview.src = originalPhoto;

      return;
    }

    uploadPhoto.classList.remove("is-invalid");

    $(".error-photo").text("");

    const reader = new FileReader();

    reader.onload = function (e) {
      photoPreview.src = e.target.result;
    };

    reader.readAsDataURL(file);
  });

  $(".add-new").on("click", function () {
    modalTitle.html("Create New Employee");
    editingId = null;
    $(btnSubmit).html("Submit");
  });

  $(document).on("click", ".edit-record", function () {
    const id = $(this).data("id");

    modalTitle.html("Edit Existing Employee");

    btnSubmit.innerHTML = "Save Employee";

    $.get(`/employees/${id}`, function (data) {
      editingId = id;

      employeeCode.value = data.employee_code || "";
      fillName.value = data.name || "";
      fillEmail.value = data.email || "";
      fillPosition.value = data.position || "";
      selectGender.value = data.gender || "";

      if (data.photo_path) {
        originalPhoto = `/${data.photo_path}`;
      } else {
        originalPhoto = "https://placehold.co/600x400?text=No+Image";
      }

      photoPreview.src = originalPhoto;
    });
  });

  $(employeeForm).on("submit", function (e) {
    e.preventDefault();

    $(".form-control").removeClass("is-invalid");

    $(".form-select").removeClass("is-invalid");

    $(".invalid-feedback").text("");

    let valid = true;

    const employee_code = employeeCode.value.trim();

    const name = fillName.value.trim();

    const email = fillEmail.value.trim();

    const position = fillPosition.value.trim();

    const gender = selectGender.value;

    const photo = uploadPhoto.files[0];

    if (!employee_code) {
      employeeCode.classList.add("is-invalid");

      $(".error-employee_code").text("Employee code is required");

      valid = false;
    }

    if (!name) {
      fillName.classList.add("is-invalid");

      $(".error-name").text("Name is required");

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

        $(".error-email").text("Invalid email address");

        valid = false;
      }
    }

    if (!position) {
      fillPosition.classList.add("is-invalid");

      $(".error-position").text("Position is required");

      valid = false;
    }

    if (!gender) {
      selectGender.classList.add("is-invalid");

      $(".error-gender").text("Gender is required");

      valid = false;
    }

    if (!editingId && !photo) {
      uploadPhoto.classList.add("is-invalid");

      $(".error-photo").text("Photo is required");

      valid = false;
    }

    if (!valid) {
      return;
    }

    btnSubmit.disabled = true;

    btnSubmit.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2"></span>
        Processing...
    `;

    const formData = new FormData(employeeForm);

    let url = editingId ? `/employees/update/${editingId}` : `/employees/store`;

    $.ajax({
      url: url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (res) {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = editingId ? "Save Employee" : "Submit";
        employeeModal.hide();
        dt_employees.draw(false);

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
        btnSubmit.innerHTML = editingId ? "Save Employee" : "Submit";

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

  employeeModalElement.addEventListener("hidden.bs.modal", function () {
    employeeForm.reset();
    editingId = null;

    $(".form-control").removeClass("is-invalid");
    $(".form-select").removeClass("is-invalid");
    $(".invalid-feedback").text("");

    photoPreview.src = "https://placehold.co/600x400?text=No+Image";
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
        url: `/employees/delete/${id}`,
        type: "DELETE",
        success: function (res) {
          dt_employees.draw(false);

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
        url: `/employees/restore/${id}`,
        type: "POST",
        success: function (res) {
          dt_employees.draw(false);

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
        url: `/employees/force/${id}`,
        type: "DELETE",
        success: function (res) {
          dt_employees.draw(false);

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
