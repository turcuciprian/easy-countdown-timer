var ectInsertSC = jQuery("#ectInsertSC"); //popup insert Button
var ectClosePopupButton = jQuery(".ectClosePopupButton"); //close popup button

var popupButton = jQuery("#ectPopupButton");
if (popupButton[0]) {
  var ectPopup = jQuery(".ectMainPopupContainer");
  popupButton.on("click", function(e) {
    ectPopup.removeClass("hidden");
  });
}
//insert into tinymce editor
function ectWPInsertSC() {
  console.log('inserting?');
  
  var ectPopup = jQuery(".ectMainPopupContainer");
  console.log(ectPopup);
  
  var ectSCInput = jQuery("#ectSCInput"); //popup Shortcode Input
  if (ectSCInput[0]) {
    
    var ShortcodeValue = ectSCInput.val();
    tinyMCE.activeEditor.selection.setContent(ShortcodeValue);
    if (ectPopup[0]) {
      
      ectPopup.addClass("hidden");
    }
  }
}

// close popup
function ectWPClosePopupButton() {
  var ectPopup = jQuery(".ectMainPopupContainer");
  ectPopup.addClass("hidden");
}
