</div>

<footer class="text-center" id="footer">&copy; Copyright 2018-2119 deep.iiitd</footer>




<script>
jQuery(window).scroll(function(){
  var vscroll = jQuery(this).scrollTop();
  jQuery('#logotext').css({
    "transform" : "translate(0px, "+vscroll/2+"px)"
  });

  var vscroll = jQuery(this).scrollTop();
  jQuery('#back-flower').css({
    "transform" : "translate("+vscroll/5+"px,-"+vscroll/12+"px)"
  });

  var vscroll = jQuery(this).scrollTop();
  jQuery('#fore-flower').css({
    "transform" : "translate(0px, -"+vscroll/2+"px)"
  });
});

function detailsmodal(id){
  var data = {"id" : id};
  jQuery.ajax({
    url : '/treat/includes/detailsmodal.php',
    method : "post",
    data : data,
    success: function(data){
      jQuery('body').append(data);
      jQuery('#details-modal').modal('toggle');
    },
    error: function(){
      alert("Something went wrong!");
    }
  });
}


function update_cart(mode,edit_id){
  var data = {"mode" : mode, "edit_id" : edit_id};
  jQuery.ajax({
    url : '/treat/admin/parsers/update_cart.php',
    method : "post",
    data : data,
    success : function(){location.reload();},
    error : function(){alert("Something went wrong.");}
  });
}


function add_to_cart(id,quantity){
var data = {"id" : id, "quantity" : quantity};

/*
  else if(quantity > 1000){
    error += '<p class="text-danger text-center">You can order this item twice for more than 10 quantity.</p>';
    jQuery('#modal_errors').html(error);
    return;
  }*/
    jQuery.ajax({
      url : '/treat/admin/parsers/add_cart.php',
      method : 'post',
      data : data,
      success : function(){
        location.reload();
      },
      error : function(){alert("Something went worng");}
    });
}
/*
var number_of_uploads;
$("#photo").change(function() {
  if(number_of_uploads > $(this).attr(max-uploads))
  {
  alert('Your Message');
  }
  else
  {
  number_of_uploads = number_of_uploads + 1;
  }
});*/
</script>

</body>
</html>
