<?php

 /**
  * Adds ect_widget widget.
  */
 class ect_Widget extends WP_Widget {

 	/**
 	 * Register widget with WordPress.
 	 */
 	function __construct() {
 		parent::__construct(
 			'ect_Widget', // Base ID
 			esc_html__( 'Easy Countdown Timer', 'text_domain' ), // Name
 			array( 'description' => esc_html__( 'ect Widget', 'text_domain' ), ) // Args
 		);
 	}

private function returnDelimiter($del){
  switch($del){
    case 1:
      $delimiter = '<br/>';

    break;
    case 2:
      $delimiter = ' - ';
    break;
    case 3:
      $delimiter = ' &sol; ';
    break;
    case 4:
      $delimiter = ' &bsol; ';
    break;
    case 5:
      $delimiter = ' &amp; ';
    break;
    case 6:
      $delimiter = ' &verbar; ';
    break;
    default:
    $delimiter = '<br/>';
    break;
  }
  return $delimiter;
}
 	/**
 	 * Front-end display of widget.
 	 *
 	 * @see WP_Widget::widget()
 	 *
 	 * @param array $args     Widget arguments.
 	 * @param array $instance Saved values from database.
 	 */
 	public function widget( $args, $instance ) {
    $widgetID = md5($instance['title'].rand (1 , 100));
    $widgetID = substr($widgetID,0,4);

 		echo $args['before_widget'];
 		if ( ! empty( $instance['title'] ) ) {
 			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
 		}
    $ect_NumbersFontSize = (! empty( $instance['ect_NumbersFontSize'] ) && isset($instance['ect_NumbersFontSize'])) ? $instance['ect_NumbersFontSize'] : esc_html__( '', 'text_domain' );
    $ect_boldNumbers = (! empty( $instance['ect_boldNumbers'] ) && isset($instance['ect_boldNumbers'])) ? $instance['ect_boldNumbers'] : esc_html__( '', 'text_domain' );
    $ect_boldNumbersString = 'normal';
    if($ect_boldNumbers==='true'){
      $ect_boldNumbersString = 'bold';
    }
    $ect_colorPicker = (! empty( $instance['ect_colorPicker'] ) && isset($instance['ect_colorPicker'])) ? $instance['ect_colorPicker'] : esc_html__( '', 'text_domain' );
    $ect_textTimerLayout = (! empty( $instance['ect_textTimerLayout'] ) && isset($instance['ect_textTimerLayout'])) ? $instance['ect_textTimerLayout'] : esc_html__( '', 'text_domain' );
    $ect_delimiter = (! empty( $instance['ect_delimiter'] ) && isset($instance['ect_delimiter'])) ? $instance['ect_delimiter'] : esc_html__( '', 'text_domain' );
    $delimiter = $this->returnDelimiter($ect_delimiter);

    ?>
    <style media="screen">
      .ectNumbers<?php echo $widgetID;?>{
        font-size:<?php echo $ect_NumbersFontSize; ?>;
        font-weight:<?php echo $ect_boldNumbersString;?>;
        color:<?php echo $ect_colorPicker;?>;
      }
      .preview .ectContainer{
        width:auto;
        display: inline-block;
      }
            .preview .ectContainer span{
              display: inline-block;
            }
    </style>
    <div id="ectWig<?php echo $widgetID;?>" class="preview">
    </div>
    <script type="text/javascript">
    function ect_getTimeRemaining(endtime){
      var t = Date.parse(endtime) - Date.parse(new Date());
      var seconds = Math.floor( (t/1000) % 60 );
      var minutes = Math.floor( (t/1000/60) % 60 );
      var hours = Math.floor( (t/(1000*60*60)) % 24 );
      var days = Math.floor( t/(1000*60*60*24) );
      return {
        'total': t,
        'days': days,
        'hours': hours,
        'minutes': minutes,
        'seconds': seconds
      };
    }

    function ect_initializeClock(id, endtime){
      var clock = document.getElementById(id);
      var timeinterval = setInterval(function(){
        <?php
        $delimiter = $this->returnDelimiter($instance['ect_delimiter']);
        ?>
        var t = ect_getTimeRemaining(endtime);



        clock.innerHTML =  '<?php echo $this->generatePerioud('day',0,'Days',$delimiter,$ect_textTimerLayout,$widgetID,$ect_NumbersFontSize);?>' +
        '<?php echo $this->generatePerioud('hour',0,'Hours',$delimiter,$ect_textTimerLayout,$widgetID,$ect_NumbersFontSize);?>'+
        '<?php echo $this->generatePerioud('minute',0,'Minutes',$delimiter,$ect_textTimerLayout,$widgetID,$ect_NumbersFontSize);?>'+
        '<?php echo $this->generatePerioud('second',0,'Seconds','',$ect_textTimerLayout,$widgetID,$ect_NumbersFontSize);?>';

        document.getElementById("day_<?php echo $widgetID;?>").innerHTML = t.days;
        document.getElementById("hour_<?php echo $widgetID;?>").innerHTML = t.hours;
        document.getElementById("minute_<?php echo $widgetID;?>").innerHTML = t.minutes;
        document.getElementById("second_<?php echo $widgetID;?>").innerHTML = t.seconds;

        if(t.total<=0){
          clearInterval(timeinterval);
        }
      },1000);
}
var hrs = -(new Date().getTimezoneOffset() / 60)
var ect_Deadline = '<?php echo $instance['ect_toDate'];?> <?php echo $instance['ect_toTime'];?> GMT'+hrs;
ect_initializeClock('ectWig<?php echo $widgetID;?>', ect_Deadline);
var d = new Date()
var n = d.getTimezoneOffset();
    </script>
    <?php
 		echo $args['after_widget'];
 	}

 	/**
 	 * Back-end widget form.
 	 *
 	 * @see WP_Widget::form()
 	 *
 	 * @param array $instance Previously saved values from database.
 	 */
 	public function form( $instance ) {
    $widgetID = md5($instance['title'].rand(1 , 100));
    $widgetID = substr($widgetID,0,4);
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'text_domain' );
    $ect_toDate = (! empty( $instance['ect_toDate'] ) && isset($instance['ect_toDate'])) ? $instance['ect_toDate'] : esc_html__( '', 'text_domain' );
    $ect_toTime = (! empty( $instance['ect_toTime'] ) && isset($instance['ect_toTime'])) ? $instance['ect_toTime'] : esc_html__( '', 'text_domain' );
    $ect_delimiter = (! empty( $instance['ect_delimiter'] ) && isset($instance['ect_delimiter'])) ? $instance['ect_delimiter'] : esc_html__( '', 'text_domain' );
 		$ect_NumbersFontSize = (! empty( $instance['ect_NumbersFontSize'] ) && isset($instance['ect_NumbersFontSize'])) ? $instance['ect_NumbersFontSize'] : esc_html__( '', 'text_domain' );
    $ect_boldNumbers = (! empty( $instance['ect_boldNumbers'] ) && isset($instance['ect_boldNumbers'])) ? $instance['ect_boldNumbers'] : esc_html__( '', 'text_domain' );
    $ect_colorPicker = (! empty( $instance['ect_colorPicker'] ) && isset($instance['ect_colorPicker'])) ? $instance['ect_colorPicker'] : esc_html__( '', 'text_domain' );
    $ect_textTimerLayout = (! empty( $instance['ect_textTimerLayout'] ) && isset($instance['ect_textTimerLayout'])) ? $instance['ect_textTimerLayout'] : esc_html__( '', 'text_domain' );
 		?>
 		<p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
 		    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
 		</p>
    <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'ect_toDate' ) ); ?>"><?php esc_attr_e( 'End date:', 'text_domain' ); ?></label>
 		    <input class="widefat ectDatePicker" id="<?php echo esc_attr( $this->get_field_id( 'ect_toDate' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ect_toDate' ) ); ?>" type="text" value="<?php echo esc_attr( $ect_toDate ); ?>">
        <script type="text/javascript">
        jQuery(document).ready(function($) {
          var ectDatePicker = $('.ectDatePicker');
          ectDatePicker.on('hover',function(){
            if (ectDatePicker[0]) {
                //check if datepicker exists as a function
                if (typeof ectDatePicker.datepicker == 'function') {
                  ectDatePicker.datepicker({
                      dateFormat: $(self).attr('data-dateformat')
                  });
                }
            }
          });


          //Timepicker
          var ectTimepicker = $('.ectTimepicker');
          if (ectTimepicker[0]) {
              if (typeof ectTimepicker.timepicker == 'function') {
                  ectTimepicker.timepicker({timeFormat: 'h:i A',});
              }
          }
          var ectColorPicker = $('.ectColorPicker');
          if (ectColorPicker[0]) {
              if (typeof ectColorPicker.iris == 'function') {
                  ectColorPicker.iris({
                      hide: true
                  });
              }
          }
        });

        </script>
 		</p>
    <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'ect_toTime' ) ); ?>"><?php esc_attr_e( 'End time:', 'text_domain' ); ?></label>
 		    <input class="widefat ectTimepicker" id="<?php echo esc_attr( $this->get_field_id( 'ect_toTime' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ect_toTime' ) ); ?>" type="text" value="<?php echo esc_attr( $ect_toTime ); ?>">
 		</p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'ect_delimiter' ) ); ?>"><?php esc_attr_e( 'Delimiter:', 'text_domain' ); ?></label><br/>
      <select class="" name="<?php echo esc_attr( $this->get_field_name('ect_delimiter')); ?>">
        <option value="1" <?php selected($ect_delimiter,1);?>>New Line</option>
        <option value="2" <?php selected($ect_delimiter,2);?>>-</option>
        <option value="3" <?php selected($ect_delimiter,3);?>>/</option>
        <option value="4" <?php selected($ect_delimiter,4);?>>\</option>
        <option value="5" <?php selected($ect_delimiter,5);?>>&amp;</option>
        <option value="6" <?php selected($ect_delimiter,6);?>>|</option>
      </select>
    </p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'ect_NumbersFontSize' ) ); ?>"><?php esc_attr_e( 'Numbers Font Size:', 'text_domain' ); ?></label><br/>
      <select class="" name="<?php echo esc_attr( $this->get_field_name('ect_NumbersFontSize')); ?>">
        <option value="11px" <?php selected($ect_NumbersFontSize,'11px',true);?>>Tiny (11px)</option>
        <option value="14px" <?php selected($ect_NumbersFontSize,'14px',true);?>>Small(14px)</option>
        <option value="16px" <?php selected($ect_NumbersFontSize,'16px',true);?>>Normal (16px)</option>
        <option value="24px" <?php selected($ect_NumbersFontSize,'24px',true);?>>Big (24px)</option>
        <option value="36px" <?php selected($ect_NumbersFontSize,'36px',true);?>>Large (36px)</option>
        <option value="42px" <?php selected($ect_NumbersFontSize,'42px',true);?>>Huge (42px)</option>
      </select>
    </p>
    <p>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'ect_boldNumbers' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ect_boldNumbers' ) ); ?>" type="checkbox" value="true" <?php checked($ect_boldNumbers,'true'); ?>>
        <label for="<?php echo esc_attr( $this->get_field_id( 'ect_boldNumbers' ) ); ?>"><?php esc_attr_e( ' - Bold Numbers:', 'text_domain' ); ?></label>

 		</p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'ect_colorPicker' ) ); ?>"><?php esc_attr_e( 'Text Color:', 'text_domain' ); ?></label>
        <input class="ectColorPicker" id="<?php echo esc_attr( $this->get_field_id( 'ect_colorPicker' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ect_colorPicker' ) ); ?>" type="textbox" value="<?php echo $ect_colorPicker; ?>" size="10">
 		</p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'ect_textTimerLayout' ) ); ?>"><?php esc_attr_e( 'Timer Text Layout:', 'text_domain' ); ?></label><br/>
      <select class="" name="<?php echo esc_attr( $this->get_field_name('ect_textTimerLayout')); ?>">
        <option value="vNT" <?php selected($ect_textTimerLayout,'vNT',true);?>>Vertical - Numbers on Top</option>
        <option value="vLT" <?php selected($ect_textTimerLayout,'vLT',true);?>>Vertical - Label on Top</option>
        <option value="hNL" <?php selected($ect_textTimerLayout,'hNL',true);?>>Horizontal - Numbers on LEFT</option>
        <option value="hNR" <?php selected($ect_textTimerLayout,'hNR',true);?>>Horizontal - Numbers on RIGHT</option>
      </select>
    </p>

    <?php
      $delimiter = $this->returnDelimiter($ect_delimiter);
      $ect_boldNumbersString = 'normal';
      if($ect_boldNumbers==='true'){
        $ect_boldNumbersString = 'bold';
      }
    ?>
    <style media="screen">
      .ectNumbers<?php echo $widgetID;?>{
        font-size:<?php echo $ect_NumbersFontSize; ?>;
        font-weight:<?php echo $ect_boldNumbersString;?>;
        color:<?php echo $ect_colorPicker;?>;
      }
    </style>
    <h3>Result Preview:</h3>
<div class="preview">
  <?php echo $this->generatePerioud('day',99,'Days',$delimiter,$ect_textTimerLayout,$widgetID,$ect_NumbersFontSize);?>
  <?php echo $this->generatePerioud('hour',24,'Hours',$delimiter,$ect_textTimerLayout,$widgetID,$ect_NumbersFontSize);?>
  <?php echo $this->generatePerioud('minute',59,'Minutes',$delimiter,$ect_textTimerLayout,$widgetID,$ect_NumbersFontSize);?>
  <?php echo $this->generatePerioud('second',59,'Seconds','',$ect_textTimerLayout,$widgetID,$ect_NumbersFontSize);?>
</div>
 		<?php
 	}
  private function generatePerioud($type,$Number,$labelString,$delimiter,$layoutType,$widgetID,$fontSize){
    $returnStr = '';
    $returnStr .= '<div class="ectContainer" style="line-height:'.$fontSize.'">';
      $nrText = '<span class="ectNumbers'.$widgetID.' '.$type.'" id="'.$type.'_'.$widgetID.'">'.$Number.'</span>';
      $labelText = '  <span class="ectLabelText">'.$labelString.'</span>';

      switch($layoutType){
        case 'vNT':
        $returnStr .= '<span class="ectNumbers'.$widgetID.' '.$type.'" id="'.$type.'_'.$widgetID.'">'.$Number.'</span>';
        $returnStr .= '<span class="ectLabelText fDiv">'.$labelString.'</span>';
        break;
        case 'vLT':
        $returnStr .=  '<span class="ectLabelText fDiv">'.$labelString.'</span>';
        $returnStr .=  '<span class="ectNumbers'.$widgetID.' '.$type.'" id="'.$type.'_'.$widgetID.'">'.$Number.'</span>';
        break;
        case 'hNL':
        $returnStr .=  '<span class="ectNumbers'.$widgetID.' '.$type.'" id="'.$type.'_'.$widgetID.'">'.$Number.'</span>';
        $returnStr .=  '<span class="ectLabelText">'.$labelString.'</span>';
        break;
        case 'hNR':
        $returnStr .=  '<span class="ectLabelText">'.$labelString.'</span>';
        $returnStr .=  '<span class="ectNumbers'.$widgetID.' '.$type.'" id="'.$type.'_'.$widgetID.'">'.$Number.'</span>';
        break;
      }
    $returnStr .=  '</div>'.$delimiter;
    return $returnStr;
  }

 	/**
 	 * Sanitize widget form values as they are saved.
 	 *
 	 * @see WP_Widget::update()
 	 *
 	 * @param array $new_instance Values just sent to be saved.
 	 * @param array $old_instance Previously saved values from database.
 	 *
 	 * @return array Updated safe values to be saved.
 	 */
 	public function update( $new_instance, $old_instance ) {
 		$instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['ect_toDate'] = ( ! empty( $new_instance['ect_toDate'] ) ) ? strip_tags( $new_instance['ect_toDate'] ) : '';
    $instance['ect_toTime'] = ( ! empty( $new_instance['ect_toTime'] ) ) ? strip_tags( $new_instance['ect_toTime'] ) : '';
    $instance['ect_delimiter'] = ( ! empty( $new_instance['ect_delimiter'] ) ) ? strip_tags( $new_instance['ect_delimiter'] ) : '';
    $instance['ect_NumbersFontSize'] = ( ! empty( $new_instance['ect_NumbersFontSize'] ) ) ? strip_tags( $new_instance['ect_NumbersFontSize'] ) : '';
    $instance['ect_boldNumbers'] = ( ! empty( $new_instance['ect_boldNumbers'] ) ) ? strip_tags( $new_instance['ect_boldNumbers'] ) : '';
    $instance['ect_colorPicker'] = ( ! empty( $new_instance['ect_colorPicker'] ) ) ? strip_tags( $new_instance['ect_colorPicker'] ) : '';
 		$instance['ect_textTimerLayout'] = ( ! empty( $new_instance['ect_textTimerLayout'] ) ) ? strip_tags( $new_instance['ect_textTimerLayout'] ) : '';

 		return $instance;
 	}

 } // class ect_Widget

 // register ect_Widget widget
function ect_register_widget() {
    register_widget( 'ect_Widget' );
}
add_action( 'widgets_init', 'ect_register_widget' );
