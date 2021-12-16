@extends('layouts.app')
@push('style')
<link rel="stylesheet" type="text/css" href="{{asset('css/datatables.bundle7.0.8.css')}}">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" type="text/css" href="{{asset('css/dropify.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
<style type="text/css">
    .required label:first-child::after{
    content: "* ";
    color: red;
    font-weight: bold;
}
 .dropify-message .file-icon p {
        font-size: 14px !important;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">User List</div>
                        <div class="col-md-6">
                            <button class="btn btn-primary btn-sm float-right" onclick="showModal('Add New User', 'Save')">Add New</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                        <table class="table table-border" id="dataTable">
                        <thead>
                            <th>SL</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>District</th>
                            <th>Upazila</th>
                            <th>Postal Code</th>
                            <th>Email Varivied</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                    </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('modal.modal-xl')
@include('modal.modal-view')
@endsection

@push('script')
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="{{asset('js/datatables.bundle7.0.8.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script type="text/javascript" src="{{asset('js/dropify.min.js')}}"></script>
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
<script>

   var table;
   // $(document).ready(function () {

     table = $('#dataTable').DataTable({
            "processing": true, //Feature control the processing indicator
            "serverSide": true, //Feature control DataTable server side processing mode
            "order": [], //Initial no order
            "responsive": true, //Make table responsive in mobile device
            "bInfo": true, //TO show the total number of data
            "bFilter": false, //For datatable default search box show/hide
            "lengthMenu": [
                [5, 10, 15, 25, 50, 100, 1000, 10000, -1],
                [5, 10, 15, 25, 50, 100, 1000, 10000, "All"]
            ],
            "pageLength": 5, //number of data show per page
                        "language": {
                processing: `<img src="{{asset('svg/table-loading.svg')}}" alt="Loading...."/>`,
                emptyTable: '<strong class="text-danger">No Data Found</strong>',
                infoEmpty: '',
                zeroRecords: '<strong class="text-danger">No Data Found</strong>'
            },
             "ajax": {
                "url": "{{route('user.list')}}",
                "type": "POST",
                "data": function (data) {
                   
                    data._token = _token;
                }
            },
     });


  // });
  

   $('.dropify').dropify();
    function showModal(title, btnText){
         $('#storeForm')[0].reset();
        $('#storeForm').find('.is-invalid').removeClass('is-invalid');
        $('#storeForm').find('.error').remove();
        $('#password, #password_confirmation').parent().removeClass('d-none');
        $('.dropify-clear').trigger('click');

        $('#saveDataModal').modal({
            keyboard: false,
            backdrop: 'static'
        });
        $('#saveDataModal .modal-title').text(title);
        $('#saveDataModal #save-btn').text(btnText);
    }

    $(document).on('click', '#save-btn', function () {
        let storeForm = document.getElementById('storeForm');
        let formData = new FormData(storeForm);
        let url = "{{route('user.store')}}";
        let id = $('#update_id').val();
        let method;
        if (id) {
            method = 'update';
        } else {
            method = 'add';
        }
        store_form_data(table, method, url, formData);
       
    });

    function store_form_data(table, method, url, formData){
         $.ajax({
                url: url,
                type: "POST",
                data: formData,
                dataType: "JSON",
                contentType: false,
                processData: false,
                cache: false,
                success: function(data){
                     $('#storeForm').find('.is-invalid').removeClass('is-invalid');
                     $('#storeForm').find('.error').remove();
                     if (data.status == false) {
                        $.each(data.errors, function(key, value){
                        $('#storeForm #'+key).addClass('is-invalid');
                        $('#storeForm #'+key).parent().append('<div class="error invalid-tooltip d-block">'+value+'</div>');
                         });

                     }else{
                        flashMessage(data.status, data.message);
                        if (data.status == 'success') {
                            if (method == 'update') {
                                table.ajax.reload(null, false);
                            } else {
                                table.ajax.reload();
                            }
                            $('#saveDataModal').modal('hide');
                         }
                  }
                    

                },
                error: function (xhr, ajaxOption, thrownError) {
                console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
                 }
            })
    }

    $(document).on('click', '.edit_data', function(){
        $('#storeForm')[0].reset();
        $('#storeForm').find('.is-invalid').removeClass('is-invalid');
        $('#storeForm').find('.error').remove();
        $('.dropify-clear').trigger('click');

        let id = $(this).data('id');
        if (id) {
             $.ajax({
                url: "{{route('user.edit')}}",
                type: "POST",
                data: {id:id, _token: _token},
                dataType: "JSON",
                success: function(data){
                     $('#password, #password_confirmation').parent().addClass('d-none');
                    $('#storeForm #update_id').val(data.user.id);
                    $('#storeForm #name').val(data.user.name);
                    $('#storeForm #email').val(data.user.email);
                    $('#storeForm #mobile_no').val(data.user.mobile_no);
                    $('#storeForm #district_id').val(data.user.district_id);
                    upazilaList(data.user.district_id, 'storeForm');
                    setTimeout(() => {
                        $('#storeForm #upazila_id').val(data.user.upazila_id);
                    }, 1000);
                    $('#storeForm #postal_code').val(data.user.postal_code);
                    $('#storeForm #address').val(data.user.address);
                    $('#storeForm #role_id').val(data.user.role_id);
                      if (data.user.avatar) {
                        let avatar = "{{asset('storage/'.USER_AVATAR)}}/" + data.user.avatar;
                        $('#storeForm .dropify-preview').css('display', 'block');
                        $('#storeForm .dropify-render').html('<image src="' + avatar + '"/>');
                        $('#storeForm #old_avatar').val(data.user.avatar);
                    }

                    $('#saveDataModal').modal({
                        keyboard: false,
                        backdrop: 'static',
                    });
                    $('#saveDataModal .modal-title').html(
                        '<i class="fas fa-edit"></i> <span>Edit ' + data.user.name + '</span>');
                    $('#saveDataModal #save-btn').text('update');
                  


                },
                error: function (xhr, ajaxOption, thrownError) {
                console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
                 }
            })

            
        }
    });



    $(document).on('click', '.view_data', function () {
        let id = $(this).data('id');
        if (id) {
            $.ajax({
                url: "{{route('user.show')}}",
                type: "POST",
                data: {
                    id: id,
                    _token: _token
                },
                dataType: "JSON",
                success: function (data) {
                    $('#view_data').html('');
                    $('#view_data').html(data.user_view);
                    $('#viewDataModal').modal({
                        keyboard: false,
                        backdrop: 'static',
                    });
                    $('#viewDataModal .modal-title').html('<i class="fas fa-eye"></i> <span> ' +
                        data.name + ' Details</span>');
                },
                error: function (xhr, ajaxOption, thrownError) {
                    console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
                }
            });
        }
    });


     $(document).on('click', '.delete_data', function () {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let row = table.row($(this).parent('tr'));
        let url = "{{ route('user.delete') }}";
        delete_data(id, url, table, row, name);
    });
    function delete_data(id, url, table, row, name) {
        Swal.fire({
            title: 'Are you sure to delete ' + name + ' data?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        id: id,
                        _token: _token
                    },
                    dataType: "JSON",
                }).done(function (response) {
                    if (response.status == "success") {
                        Swal.fire("Deleted", response.message, "success").then(function () {
                            table.row(row).remove().draw(false);
                        });
                    }
                }).fail(function () {
                    swal.fire('Oops...', "Somthing went wrong with ajax!", "error");
                });
            }
        });
    }

    function upazilaList(district_id){
        if (district_id) {
            $.ajax({
                url: "{{route('upazila.list')}}",
                type: "POST",
                data: {district_id:district_id, _token: _token},
                dataType: "JSON",
                success: function(data){
                    $('#upazila_id').html('');
                    $('#upazila_id').html(data);

                },
                error: function (xhr, ajaxOption, thrownError) {
                console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
                 }
            })
        }
    }

    function flashMessage(status, message){
        toastr.options = {
          "closeButton": true,
          "debug": false,
          "newestOnTop": false,
          "progressBar": true,
          "positionClass": "toast-top-right",
          "preventDuplicates": false,
          "onclick": null,
          "showDuration": "300",
          "hideDuration": "1000",
          "timeOut": "5000",
          "extendedTimeOut": "1000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
        }
        switch (status) {
            case 'success':
                toastr.success(message, 'SUCCESS');
                break;
            case 'error':
                toastr.error(message, 'ERROR');
                break;
            case 'info':
                toastr.info(message, 'INFORMARTION');
                break;
            case 'warning':
                toastr.warning(message, 'WARNING');
                break;
        }
    }
</script>
@endpush
