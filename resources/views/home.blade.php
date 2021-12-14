@extends('layouts.app')
@push('style')
<link rel="stylesheet" type="text/css" href="{{asset('css/datatables.bundle7.0.8.css')}}">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" type="text/css" href="{{asset('css/dropify.min.css')}}">
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
@include('modal.modal-xl')
@endsection

@push('script')
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="{{asset('js/datatables.bundle7.0.8.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script type="text/javascript" src="{{asset('js/dropify.min.js')}}"></script>
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
        store_form_data(formData);
       
    });

    function store_form_data(formData){
         $.ajax({
                url: "{{route('user.store')}}",
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
                        flashMessage(data.status, data.message)
                        $('#saveDataModal').modal('hide');
                     }
                    

                },
                error: function (xhr, ajaxOption, thrownError) {
                console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
                 }
            })
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
