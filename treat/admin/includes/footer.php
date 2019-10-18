</div><br><br>
<div class="col-md-12 text-center">&copy; Copyright 2018-2119 deep.iiitd</div>

<script>

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

      },
      error : function(){alert("Something went worng");}
    });
}

  function updateSizes(){
    var sizeString= '';
    for(var i=1;i<=12;i++){
      if(jQuery('#size'+i).val()!=''){
        sizeString += String(jQuery('#size'+i).val())+':'+jQuery('#qty'+i).val()+':'+jQuery('#threshold'+i).val()+', ';
      }
    }
    sizeString = sizeString.slice(0,sizeString.length-2);
    jQuery('#sizes').val(sizeString);
  }

  function updateDelAreas(c){
    var areaString= '';
    for(var u=0;u<c;u++){
      if(jQuery('#del_areas'+u).val()!=''){
        areaString += jQuery('#del_areas'+u).val()+',';
      }
    }
    areaString = areaString.slice(0,areaString.length-2);
    jQuery('#del_areas').val(areaString);
  }

  function get_child_options(selected){
    if(typeof selected === 'undefined'){
      var selected = '';
    }

    var parentID = jQuery('#parent').val();
    jQuery.ajax({
      url: '/treat/admin/parsers/child_categories.php',
      type: 'POST',
      data: {parentID : parentID, selected: selected},
      success: function(data){
        jQuery('#child').html(data);
      },
      error: function(){alert("Something went wrong with the child options.")},
    });
  }
  jQuery('select[name="parent"]').change(function(){
    get_child_options();
  });

  function get_ar_child_options(selected){
    if(typeof selected === 'undefined'){
      var selected = '';
    }

    var area_parentID = jQuery('#parent_area').val();
    jQuery.ajax({
      url: '/treat/admin/parsers/ar_child_categories.php',
      type: 'POST',
      data: {area_parentID : area_parentID, selected: selected},
      success: function(data){
        jQuery('#child_area').html(data);
      },
      error: function(){alert("Something went wrong with the child options.")},
    });
  }
  jQuery('select[name="parent_area"]').change(function(){
    get_ar_child_options();
  });
</script>

  </body>
</html>
