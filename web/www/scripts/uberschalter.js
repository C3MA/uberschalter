function updateSwitch( key, val ) {
  console.log("Key = " + key + ":" + val);
  $('#'+key).prop('checked', (val == 'h') ? true : false).trigger('create').flipswitch('refresh');
};

function setBState(key, value) {
  $.get( "php/api.php",
	 { 
	   type : "bin",
	   id : , 
	   v: (this.checked) ? 1 : 0
	}).done( function( data ) {
           $.each(jQuery.parseJSON(data), updateSwitch);
	});
}

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
  $(':checkbox').change(function() {
    setBState($(this).name, $(this).checked);
  });

  getAllStates();

  window.setInterval(getAllStates, 2000);

});
