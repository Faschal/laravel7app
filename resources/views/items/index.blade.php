<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.js"></script>
    <title>Hello, world!</title>
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="card mt-5">
            <div class="card-header">
               <div class="col-md-12">
                   <h4 class="card-title">Laravel 7 Ajax CRUD tutorial using Datatable - nicesnippets.com  
                     <a class="btn btn-success ml-5" href="javascript:void(0)" id="createNewItem"> Create New Item</a>
                   </h4>
               </div>
            </div>
            <div class="card-body">              
              <table class="table table-bordered data-table">
                   <thead>
                       <tr>
                           <th width="5%">No</th>
                           <th>Name</th>
                           <th>Descriptions</th>
                           <th width="15%">Action</th>
                       </tr>
                   </thead>
                   <tbody>
                   </tbody>
               </table>
           </div>
           <div class="modal fade" id="ajaxModel" aria-hidden="true">
               <div class="modal-dialog">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h4 class="modal-title" id="modelHeading"></h4>
                       </div>
                       <div class="modal-body">               
                          <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul></ul>
                          </div>          
                           <form id="ItemForm" name="ItemForm" class="form-horizontal">
                              <input type="hidden" name="id" id="id">
                               <div class="form-group">
                                   <label for="name" class="col-sm-2 control-label">Name</label>
                                   <div class="col-sm-12">
                                       <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">                                                                                                                                                        
                                       <span class="text-danger" id=" nameError"></span>
                                   </div>
                               </div>
                               <div class="form-group">
                                   <label class="col-sm-3 control-label">descriptions</label>
                                   <div class="col-sm-12">
                                       <textarea id="description" name="description" required="" placeholder="Enter descriptions" class="form-control"></textarea>
                                   </div>
                               </div>
                               <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                                </button>
                               </div>
                           </form>
                       </div>
                   </div>
               </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>    
    <script type="text/javascript">
      $(function () {
         
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
        });
        
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('items.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
         
        $('#createNewItem').click(function () {       
          $(".print-error-msg").css({
                            display:"none"
                        });                     
            $('#saveBtn').val("create-Item");            
            $('#id').val('');            
            $('#ItemForm').trigger("reset");
            $('#modelHeading').html("Create New Item");            
            $('#ajaxModel').modal('show');
        });
        
        $('body').on('click', '.editItem', function () {
          var Item_id = $(this).data('id');
          $.get("{{ route('items.index') }}" +'/' + Item_id +'/edit', function (data) {
            $(".print-error-msg").css({
                            display:"none"
                        });
              $('#modelHeading').html("Edit Item");
              $('#saveBtn').val("edit-user");
              $('#ajaxModel').modal('show');
              $('#id').val(data.id);
              $('#name').val(data.name);
              $('#description').val(data.description);
          })
       });
        
        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $('#saveBtn').html('Sending..');
        
            $.ajax({
              data: $('#ItemForm').serialize(),
              url: "{{ route('items.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {                
                  if($.isEmptyObject(data.error)){
                    $('#ItemForm').trigger("reset");
                    $('.print-error-msg ul').empty();
                    $(".print-error-msg").css({
                            display:"none"
                        });
                    $('#ajaxModel').modal('hide');
                    table.draw();                               
                  }else{
                      printErrorMsg(data.error);
                  }      
                  
              },
              error: function (response) {                         
                  console.log('Error:', data);
                  $('#saveBtn').html('Save Changes');
              }
          });
        });

        function printErrorMsg (msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display','block');
            $.each( msg, function( key, value ) {
                $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
            });
        }
  
        $('body').on('click', '.deleteItem', function () {
         
            var Item_id = $(this).data("id");
            confirm("Are You sure want to delete !");
          
            $.ajax({
                type: "DELETE",
                url: "items"+'/'+Item_id,
                success: function (data) {
                    table.draw();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        });
         
      });
    </script>
  </body>
</html>