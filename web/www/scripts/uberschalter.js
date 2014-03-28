
function getAllStates() {
    for (var i=1;i<=6;i++){
        $.get( "php/api.php", 
	      { 
		 type : "bin" ,
	         id : i 
	      }).done( function( data ) {
                  $.each(jQuery.parseJSON(data), function( key, val ) {
	              console.log("Key = " + key + ":" + val);
	              $('#'+key).prop('checked', (val == "h") ? true : false).trigger('create').checkboxradio('refresh');
                  });
              });
    } 
};

/* main */
$({
  $(':checkbox').change(function() {
        $.get( "php/api.php",
	      { 
	          type : "bin" ,
	          id : this.name, 
	          v: (this.checked) ? 1 : 0 
		 
	      }).done( function( data ) {
                  $.each(jQuery.parseJSON(data), function( key, val ) {
                      console.log("Key = " + key + ":" + val);
                      $('#'+key).prop('checked', (val == "h") ? true : false).trigger('create').checkboxradio('refresh');
                  });
              });
  });

  getAllStates();

  window.setInterval(getAllStates, 2000);

});
