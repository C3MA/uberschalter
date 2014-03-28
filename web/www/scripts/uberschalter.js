function updateSwitch( key, val ) {
  console.log("Key = " + key + ":" + val);
  $('#'+key).prop('checked', (val > 0) ? true : false).trigger('create').flipswitch('refresh');
};

function getBStatus(key, callback) {
  $.get( "php/api.php",
	 { 
	   type : "bin",
	   id : key 
	 }).done( function( data ) {
           $.each(jQuery.parseJSON(data), callback);  
         });
}

function getAllStates() {
    for (var i=1;i<=6;i++){
        getBStatus(i, updateSwitch);
    } 
};

/* main */
$(function() {
  $(':checkbox').click(function() {
        $.get( "php/api.php",
	      { 
	          type : "bin" ,
	          id : this.name, 
	          v: (this.checked) ? 1 : 0 
		 
	      }).done( function( data ) {
                  $.each(jQuery.parseJSON(data), updateSwitch);
              });
  });

  getAllStates();

  //window.setInterval(getAllStates, 2000);

});
