console.log('1');
jQuery(document).ready(function($){
  var popupButton = $('#ectPopupButton');
  if(popupButton[0]){
    popupButton.on('click',function(e){
      e.preventDefault();
      console.log('test');
    })

  }
});
