<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Testimonial
 *
 * @author csweet
 */
class IATI_Event_Widget extends WP_Widget {

        /**
	 * Register widget with WordPress.
	 */
	public function __construct() {

            parent::__construct(
	 		'sweetapple_widget_event', // Base ID
			'Events', // Name
			array( 'description' => __( 'Display Events', 'sweetapple' ), ) // Args
		);

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

		$instance['title'] = strip_tags( $new_instance['title'] );
                $instance['number'] = absint( $new_instance['number'] );
                $instance['random'] = ( 'true' == $new_instance['random'] ) ? 'true' : '';

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Title', 'sweetapple' );
		}
                if ( isset( $instance[ 'number' ] ) ) {
                    $number = $instance[ 'number' ];
                }
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
                <p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of testimonials to show:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="2" />
		</p>
                <p>
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'random' ); ?>" name="<?php echo $this->get_field_name( 'random' ); ?>" value="true" <?php echo checked( $instance['random'], 'true', false ); ?> /> <label for="<?php echo $this->get_field_id( 'random' ); ?>"><?php _e( 'Display Randomly?', 'sweetapple' ); ?></label>
                </p>

		<?php
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
            extract( $args );

            echo $before_widget;

            $title = apply_filters( 'widget_title', $instance['title'] );
            if ( ! empty( $title ) ){
                echo $before_title . $title . $after_title;
            }

            $order = ( $instance['random'] == 'true' ) ? "rand" : "menu_order" ;

            $options = array(
                'numberposts' => $instance['number'],
                'orderby' => $order,
            );
            $output = $this->get_widget_html( $options );
            //Make the output filterable so we can modify the output in child themes.
            echo apply_filters("sweetapple_event_widget_display", $output);
            echo $after_widget;
	}


        //Returns the widget output
        public function get_widget_html( $args = null ) {
//            $testimonials = neilwatson_get_testimonials( $args );
//            $output = "";
//            if( count($testimonials) > 0 ) {
//                $output .= "<div class='events'>";
//                foreach ($testimonials as $testimonial) {
//                    $output .= "<div class='testimonial'>";
//                    $output .= wpautop($testimonial->quote);
//                    $output .= "<div class='testimonial-meta'>";
//                    $output .= "<span class='title'>" . $testimonial->post_title . "</span>";
//                    $output .= "</div>";
//                    $output .= "</div>";
//                }
//                $output .= "</div><!-- //.testimonials -->";
//            }
            return $output;
        }
}
