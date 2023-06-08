
var baseUrl = function(url) {
    return base + url;
  }
  
  var theFullDate = function(date) {
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    let d = new Date(date);
    let day = d.getDate();
    let month = d.getMonth() + 1;
    let year = d.getFullYear();
    let theDate = day + '/' + ('0'+month).slice(-2)  + '/' + year;
  
    return theDate;
  }

  var buttonActionIndex = 7;
  
  var columnsDataTable = [
    { data: 'name' },
    { data: 'name' },
    { data: 'username' },
    { data: 'password' },
    { data: 'role', render: function(data, type, row) {
      if(row.role == 'Admin'){
        var role = 'Admin';
      }else if(row.role == 'Analis'){
        var role = 'Analyst';
      }else if(row.role == 'Dokter'){
        var role = 'Doctor';
      }else if(row.role == 'Viewer'){
        var role = 'Viewer';
      }else{
        var role = 'Admin';
      }

      return role;
    }
  },
    { data: 'created_at', render: function(data, type, row) {
        return theFullDate(data);
      }
    },
    { data: 'updated_at', render: function(data, type, row) {
      return theFullDate(data);
    }
  },
  ];
  
  // Datatable Component
  var DatatablesServerSide = function () {
    // Shared variables
    var table;
    var dt;
   
    // Private functions
    var initDatatable = function () {
        dt = $(".user-management-datatable-ajax").DataTable({
            paging: false,
            scrollY: '600px',
            scrollX: '100%',
            order: [[0, 'desc']],
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            stateSave: false,
            ajax: {
                url: baseUrl('user-management/datatable/')
            },
            columns: columnsDataTable,
            order: [[1, 'asc']],
            columnDefs: [
                {
                    responsivePriority: 1,
                    targets: buttonActionIndex,
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                      return `
                          <a class="btn btn-light-primary btn-sm" onClick="editData(`+row.id+`)" data-toggle="tooltip" data-placement="top" title="Edit Data">
                          <i class="fas fa-pencil-alt"></i>
                          </a>
                          <a class="btn btn-light-danger btn-sm" onClick="deleteData(`+row.id+`)" data-toggle="tooltip" data-placement="top" title="Delete Data">
                          <i class="fas fa-trash"></i>
                          </a>
                      `;
                    },
                },
            ],
        });
  
        table = dt.$;

        dt.on('order.dt search.dt', function () {
          let i = 1;
   
          dt.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
              this.data(i++);
          });
      }).draw();
  
    }
  
    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-user-management"]');
        filterSearch.addEventListener('keyup', function (e) {
            dt.search(e.target.value).draw();
        });
    }
  
    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
        },
        refreshTable: function() {
            dt.ajax.reload();
        },
        refreshTableAjax: function(url) {
            dt.ajax.url(url).load();
        }
    }
  }();
  
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var addData = function () {
    let theForm = $("#form-create");

    $('#button_add_data').on('click', function() {
        var name = $("#name").val();
        var username = $("#username").val();
        var password = $("#password").val();
        var role = $("#role").val();
  
      //   alert(name + ' ' + username + ' ' + password)
      if(name != '' && username != '' && password != '' && role != ''){
        $.ajax({
          url: baseUrl('user-management/add'),
          data: {
            'name': name,
            'username': username,
            'password': password,
            'role': role
          },
          method: 'POST',
          success: function(res) {
              if(res.message == "Username is already exist"){
                toastr.error(res.message, "Create Failed!");
              }else{
                theForm.trigger('reset');
                DatatablesServerSide.refreshTable();
                toastr.success(res.message, "Create Success!");
              }
          },
          error: function(request, status, error) {
              toastr.error(request.responseJSON.message);
          }
        });
      }else{
        toastr.error('Please complete the form to continue submission form');
      }
          
      });
  };

  var editData = function (id) {
      $.ajax({
          url: baseUrl('user-management/edit/'+id),
          method: 'GET',
          success: function(res){
              setValueModalEditForm(res);
          },
          error: function(res) {

          }
      })
  }

  var setValueModalEditForm = function(data)
  {
      $("#modal_form_edit").modal('show');
      $("#modal_form_edit input[name='id']").val(data.id);
      $("#modal_form_edit input[name='edit_name']").val(data.name);
      $("#modal_form_edit input[name='edit_username']").val(data.username);
      $("#modal_form_edit input[name='edit_password']").val(data.password);
  }

  var updateData = function () {
 
    let theForm = $("#form-edit");
    
    $('#button_update_data').on('click', function() {
      var id = $("#id").val();
      var name = $("#edit_name").val();
      var username = $("#edit_username").val();
      var password = $("#edit_password").val();
      var role = $("#edit_role").val();

    //   alert(name + ' ' + username + ' ' + password)

    if(name != '' && username != '' && password != '' && role != ''){
      $.ajax({
        url: baseUrl('user-management/update'),
        data: {
          'id': id,
          'name': name,
          'username': username,
          'password': password,
          'role': role
        },
        method: 'PUT',
        success: function(res) {
            $("#modal_form_edit").modal('hide');
            theForm.trigger('reset');
            DatatablesServerSide.refreshTable();
            toastr.success(res.message, "Update Success!");
        },
        error: function(request, status, error) {
            toastr.error(request.responseJSON.message);
        }
      });
    }else{
      toastr.error('Please complete the form to continue submission form');
    }
  
    });
  }

  var deleteData = function (id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this data!',
        // type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
            confirmButton: "btn btn-danger",
            cancelButton: 'btn btn-secondary'
        }
    }).then(function(isConfirm){
        if(isConfirm.value) {
            $.ajax({
                url: baseUrl('user-management/delete/'+id),
                method: 'DELETE',
                success: function(res) {
                    DatatablesServerSide.refreshTable();
                    toastr.success(res.message, "Delete Success!");
                },
                error: function(request, status, error){
                    toastr.error(request.responseJSON.message);
                }
            })
        }
    });
}
  
  // On document ready
  document.addEventListener('DOMContentLoaded', function () {
    DatatablesServerSide.init();
    
    updateData(); 
    addData(); 
    // deleteData();
  });