<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';
  if(!is_logged_in()){
    login_error_redirect();
  }

  if((!has_permission()) && (!has_permission_editor())){
    permission_error_redirect('/treat/admin/login.php');
  }

  include 'includes/head.php';
  include 'includes/navigation.php';
  ?>
  <h2 align="center">Billing</h2>
  <br />
  <a href="cart.php" class="btn btn-success pull-right" style="margin-right:10px" id="add-product-btn">Next>></a>
  <div class="clearfix"></div>
  <div class="container col-md-10">
   <br />
   <div class="form-group">
    <div class="input-group">
     <span class="input-group-addon">Search</span>
     <input type="text" name="search_text" id="search_text" placeholder="Search by name" class="form-control" />
    </div>
   </div>
   <br />
   <div id="result"></div>
  </div>

  <script>
  $(document).ready(function(){

   load_data();

   function load_data(query)
   {
    $.ajax({
     url:"fetch.php",
     method:"POST",
     data:{query:query},
     success:function(data)
     {
      $('#result').html(data);
     }
    });
   }
   $('#search_text').keyup(function(){
    var search = $(this).val();
    if(search != '')
    {
     load_data(search);
    }
    else
    {
     load_data();
    }
   });
  });
  </script>
  <?php
  	include 'includes/rightbar.php';
    ?>
  <?php  include 'includes/footer.php';
  ?>
