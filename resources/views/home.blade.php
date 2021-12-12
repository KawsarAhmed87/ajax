@extends('layouts.app')
@push('style')
<style type="text/css">
    .required label:first-child::after{
    content: "* ";
    color: red;
    font-weight: bold;
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

                    <table class="table table-border">
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    function showModal(title, btnText){
        $('#storeForm')[0].reset();
        $('#storeForm').find('.is-invalid').removeClass('is-invalid');
        $('#storeForm').find('.error').remove();

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
                        console.log(data.status);
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
</script>
@endpush
