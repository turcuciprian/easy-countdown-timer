jQuery(document).ready(function($) {
  var ctoDatePicker = $('.ctoDatePicker');
  var ctoTimepicker = $('.ctoTimepicker');
  var ctoColorPicker = $('.ctoColorPicker');

  if (ctoDatePicker[0]) {
    ctoDatePicker.on('hover',function(){
      //check if datepicker exists as a function
      if (typeof ctoDatePicker.datepicker == 'function') {
        ctoDatePicker.datepicker({
            dateFormat: $(self).attr('data-dateformat')
        });
      }
    });
}


  //Timepicker
  if (ctoTimepicker[0]) {
      if (typeof ctoTimepicker.timepicker == 'function') {
          ctoTimepicker.timepicker({timeFormat: 'h:i A',});
      }
  }

  if (ctoColorPicker[0]) {
      if (typeof ctoColorPicker.iris == 'function') {
          ctoColorPicker.iris({
              hide: true
          });
      }
  }
  // expand collapse

  var accordionData = [
    {
      name:'Countdown',
      data: 'some lorem ipsum data'
    },
    {
      name:'Countdown',
      data: 'some lorem ipsum data asdasd as dsa das'
    }
  ];


});
