jQuery(document).ready(function($){
  var popupButton = $('#ectPopupButton');
  var ectPopup = $('.ectMainPopupContainer');
  var ectInsertSC = $('#ectInsertSC'); //popup insert Button
  var ectSCInput = $('#ectSCInput'); //popup Shortcode Input
  var ectClosePopupButton = $('.ectClosePopupButton'); //close popup button

  if(popupButton[0]){
    popupButton.on('click',function(e){
      ectPopup.removeClass('hidden');
      console.log('test');
    });
    //insert into tinymce editor
    if(ectInsertSC[0]){

    ectInsertSC.on('click',function(){
      let ShortcodeValue = ectSCInput.val();
      tinyMCE.activeEditor.selection.setContent(ShortcodeValue);
      ectPopup.addClass('hidden');

    });
  }
    // close popup
    if(ectClosePopupButton[0]){
    ectClosePopupButton.on('click',function(){
      ectPopup.addClass('hidden');

    });
  }
  }
});
