<?php
/*
Plugin Name: Random Media Widget
Plugin URI: http://chaselivingston.me
Description: Display a random image from your Media Library in a widget.
Author: Chase Livingston
Version: 1.0
Author URI: http://chaselivingston.me
License: GPL2
*/

function cl_get_random_image( $image_tag ) {
    if ( $image_tag != null ) {
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' =>'image',
            'tag' => $image_tag,
            'post_status' => 'inherit',
            'posts_per_page' => 1,
            'orderby' => 'rand'
        );
    } else {
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' =>'image',
            'post_status' => 'inherit',
            'posts_per_page' => 1,
            'orderby' => 'rand'
        );
    }

    $query_image = new WP_Query( $args );
    $images = array();
    foreach ( $query_image->posts as $image) {
       $images[]= $image->guid;
   }

   return $images[0];
}

function cl_attachment_taxonomy() {
    register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}
add_action( 'init', 'cl_attachment_taxonomy' );

class RandomMediaWidget extends WP_Widget {
    function __construct() {
        parent::__construct(
        'rand_media_widget',
        __( 'Random Media Widget', 'rand_media_widget' ),
        array( 'description' => __( 'A widget to display a random media library image.', 'rand_media_widget' ), )
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
        if ( empty( $instance['tag'] ) ) {
            $instance['tag'] = null;
	}
	echo '<img src="' . cl_get_random_image( $instance['tag'] ) . '" />';
	echo $args['after_widget'];
    }

    public function form( $instance ) {
	$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'rand_media_widget' );
        $tag = ! empty( $instance['tag'] ) ? $instance['tag'] : __( 'Enter an image tag (optional)', 'rand_media_widget' );
		?>
		<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        <label for="<?php echo esc_attr( $this->get_field_id( 'tag' ) ); ?>"><?php _e( esc_attr( 'Tag:' ) ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tag' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tag' ) ); ?>" type="text" value="<?php echo esc_attr( $tag ); ?>">
		</p>
		<?php
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'RandomMediaWidget' );
});
