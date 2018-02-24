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
function ectWPInsertSC(timerID) {
  console.log(timerID);
  
  
  var ectPopup = jQuery(".ectMainPopupContainer");
  
    
    var ShortcodeValue = "[ectSc id=\""+timerID+"\" ]";
    tinyMCE.activeEditor.selection.setContent(ShortcodeValue);
    if (ectPopup[0]) {    
      ectPopup.addClass("hidden");
    }
}

// close popup
function ectWPClosePopupButton() {
  var ectPopup = jQuery(".ectMainPopupContainer");
  ectPopup.addClass("hidden");
}
