jQuery(document).ready(function($){
  var popupButton = $('#ectPopupButton');
  var ectPopup = $('.ectMainPopupContainer');
  if(popupButton[0]){
    popupButton.on('click',function(e){
      ectPopup.removeClass('hidden');
      console.log('test');
    })

  }
});
