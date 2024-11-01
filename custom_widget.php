<?php
/**
 * Plugin Name: WP Custom Image Widget
 * Description: Creates image widget and can customize image as per your need..
 * Version: 2.0
 * Author URI: http://www.yudiz.com
 * Author: Yudiz Solutions Ltd.
 */
?>
<?php
define( 'CUS_IMG_WID', __FILE__ );

//Add Admin scripts
add_action('admin_enqueue_scripts', 'ciw_custom_image_widget_admin_js');
function ciw_custom_image_widget_admin_js() {
    wp_enqueue_media();
    wp_enqueue_script('js_script', plugin_dir_url( CUS_IMG_WID ) . '/js/widget.js', false, '1.0', true);
}

//Add Front scripts
add_action('wp_enqueue_scripts', 'ciw_custom_image_widget_js');
function ciw_custom_image_widget_js() {
    wp_enqueue_media();
    wp_enqueue_script('js_script', plugin_dir_url( CUS_IMG_WID ) . '/js/front.js', false, '1.0', true);
}

// Register widget
function ciw_custom_image_widget_register() { 
   register_widget( 'ciw_custom_image_widget' );
}
add_action( 'widgets_init', 'ciw_custom_image_widget_register' );

// Widget class
class ciw_custom_image_widget extends WP_Widget {
    function __construct() {
        $widget_detials = array( 
            'classname' => 'ciw_custom_image_widget',
            'description' => 'This is an Custom Image Widget',
        );
        parent::__construct( 'ciw_custom_image_widget', 'WP Custom Image Widget', $widget_detials );
    }

    //Function to display widget on front
    function widget($args, $instance) {
    	if(!empty($instance['image'])){
    		echo $args['before_widget'];
			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}
			if( ! empty( $instance['image'] )){
				global $wpdb;
		        $sql = $wpdb->get_col($wpdb->prepare("SELECT ID FROM wp_posts WHERE guid = '%s';",$instance['image']));
		        $file_size = wp_get_attachment_image_src($sql[0],$size = $instance['size']);
		        $file = wp_get_attachment_metadata($sql[0]);
		        if(!empty($instance['size'])){
                	if($instance['size'] == 'full_size' || $instance['size'] == 'large'){
                    	$instance['width'] = $file['width'];
                     	$instance['height'] = $file['height'];
                	}
        	    }
	    		?>
	    		<div style="text-align: <?php echo $instance['align']; ?>">
	    			<a target="<?php if(!empty($instance['link'])){ echo $instance['target']; } ?>" href="<?php if(!empty($instance['link'])){ echo $instance['link']; } ?>" title="<?php if(!empty($instance['link'])){ echo $instance['link_title']; } ?>" id="<?php if(!empty($instance['link'])){ echo $instance['link_id']; } ?>">
						<img class="custom_media_image" src="<?php if($instance['image']){ echo $file_size[0]; } ?>" alt="<?php echo $instance['alt_text']; ?>" width="<?php if($instance['size'] != 'custom'){ echo $file_size[1]; }else{ echo $instance['width']; } ?>" 
			                height="<?php if($instance['size'] != 'custom'){ echo $file_size[2]; }else{ echo $instance['height']; } ?>">
            		</a>
            	</div>
				<?php
	    	}
	    	echo '<p>'.$instance['caption'].'</p>';
			echo $args['after_widget'];
    	}
    	else{
    		echo "";
    	}
    }

    //Function to save the widget
    function update($new_instance, $old_instance) {
        $instance = array();
		$instance['image'] = ( ! empty( $new_instance['image'] ) ) ? sanitize_text_field( $new_instance['image'] ) : '';
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['alt_text'] = ( ! empty( $new_instance['alt_text'] ) ) ? sanitize_text_field( $new_instance['alt_text'] ) : '';
		$instance['caption'] = ( ! empty( $new_instance['caption'] ) ) ? sanitize_text_field( $new_instance['caption'] ) : '';
		$instance['link'] = ( ! empty( $new_instance['link'] ) ) ? sanitize_text_field( $new_instance['link'] ) : '';
		$instance['link_title'] = ( ! empty( $new_instance['link_title'] ) ) ? sanitize_text_field( $new_instance['link_title'] ) : '';
		$instance['link_id'] = ( ! empty( $new_instance['link_id'] ) ) ? sanitize_text_field( $new_instance['link_id'] ) : '';
		$instance['target'] = ( ! empty( $new_instance['target'] ) ) ? sanitize_text_field( $new_instance['target'] ) : '';
		$instance['size'] = ( ! empty( $new_instance['size'] ) ) ? sanitize_text_field( $new_instance['size'] ) : '';
		$instance['align'] = ( ! empty( $new_instance['align'] ) ) ? sanitize_text_field( $new_instance['align'] ) : '';
		$instance['width'] = ( ! empty( $new_instance['width'] ) ) ? sanitize_text_field( $new_instance['width'] ) : '';
        $instance['height'] = ( ! empty( $new_instance['height'] ) ) ? sanitize_text_field( $new_instance['height'] ) : '';
		return $instance;
    }

    //Function to make widget form in backend
    function form($instance) {
        ?>
        <p>
        	<input type="button" class="button button-primary custom_media_button widefat" id="custom_media_button" 
        		name="<?php echo $this->get_field_name('image_btn'); ?>" value="Upload Image" style="margin-top:5px;" />
    	</p>
    	<?php
			$image = ! empty( $instance['image'] ) ? $instance['image'] : ''; 
			$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
			$alt_text = !empty( $instance['alt_text'] ) ? $instance['alt_text'] : '';
			$caption = !empty( $instance['caption'] ) ? $instance['caption'] : '';
			$link = !empty( $instance['link'] ) ? $instance['link'] : '';
			$link_title = !empty( $instance['link_title'] ) ? $instance['link_title'] : '';
			$link_id = !empty( $instance['link_id'] ) ? $instance['link_id'] : '';
			$target = !empty( $instance['target'] ) ? $instance['target'] : '';
			$size = !empty( $instance['size'] ) ? $instance['size'] : '';
			$align = !empty( $instance['align'] ) ? $instance['align'] : '';
			$width = !empty( $instance['width'] ) ? $instance['width'] : '';
			$height = !empty( $instance['height'] ) ? $instance['height'] : '';

			if( ! empty($instance['image']) ){
				global $wpdb;
				$sql = $wpdb->get_col($wpdb->prepare("SELECT ID FROM wp_posts WHERE guid = '%s';",$instance['image']));
				$file_size = wp_get_attachment_image_src($sql[0],$size = $instance['size']);
				$file = wp_get_attachment_metadata($sql[0]);
				if(!empty($instance['size'])){
					if($instance['size'] == 'full_size' || $instance['size'] == 'large'){
						$instance['width'] = $file['width'];
						$instance['height'] = $file['height'];
					}
				}
			} 
			if(!empty($instance['image'])){  
				$display = 'display : block'; 
			}else{ 
				$display = 'display : none'; 
			}
			?>
			<div class="content" style="<?php echo $display; ?>">
				<label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_attr_e( 'Image:', 'text_domain' ); ?></label>
				<p style="text-align: <?php echo $instance['align']; ?>">
					<img class="custom_media_image" style="max-width: 300px;" src="<?php if($image){ echo $file_size[0]; } ?>"  
						width="<?php echo $file['width']; ?>" 
						height="<?php echo $file['height']; ?>">
				</p>

				<input class="widefat custom_media_url" id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" type="hidden" value="<?php echo esc_attr( $image ); ?>">

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label> 
					<input class="widefat title" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'alt_text' ) ); ?>"><?php esc_attr_e( 'Alternate Text:', 'text_domain' ); ?></label> 
					<input class="widefat alt_text" id="<?php echo esc_attr( $this->get_field_id( 'alt_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'alt_text' ) ); ?>" type="text" value="<?php echo esc_attr( $alt_text ); ?>">
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'caption' ) ); ?>"><?php esc_attr_e( 'Caption:', 'text_domain' ); ?></label>
					<textarea class="widefat caption" id="<?php echo esc_attr( $this->get_field_id( 'caption' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'caption' ) ); ?>" rows="6" cols="6"><?php echo esc_attr( $caption ); ?></textarea>
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_attr_e( 'Link:', 'text_domain' ); ?></label> 
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>">
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'link_title' ) ); ?>"><?php esc_attr_e( 'Link Title:', 'text_domain' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_title' ) ); ?>" type="text" value="<?php echo esc_attr( $link_title ); ?>">
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'link_id' ) ); ?>"><?php esc_attr_e( 'Link ID:', 'text_domain' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_id' ) ); ?>" type="text" value="<?php echo esc_attr( $link_id ); ?>">
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>"></label>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target' ) ); ?>">
						<option value="_self"<?php if($target == '_self'){ ?> selected <?php } ?>><?php esc_attr_e('Stay In Current Window','text_domain'); ?></option>
						<option value="_blank"<?php if($target == '_blank'){ ?> selected <?php } ?>><?php esc_attr_e('Open In New Window','text_domain'); ?></option>
					</select>
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php esc_attr_e( 'Size', 'text_domain' ); ?></label>
					<select class="widefat image_size" id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>">
						<option value="thumbnail"<?php if($size == 'thumbnail'){ ?> selected <?php } ?>><?php esc_attr_e('Thumbnail','text_domain'); ?></option>
						<option value="medium"<?php if($size == 'medium'){ ?> selected <?php } ?>><?php esc_attr_e('Medium','text_domain'); ?></option>
						<option value="large"<?php if($size == 'large'){ ?> selected <?php } ?>><?php esc_attr_e('Large','text_domain'); ?></option>
						<option value="small"<?php if($size == 'small'){ ?> selected <?php } ?>><?php esc_attr_e('Small','text_domain'); ?></option>
						<option value="custom"<?php if($size == 'custom'){ ?> selected <?php } ?>><?php esc_attr_e('Custom','text_domain'); ?></option>
					</select>
				</p>

				<?php
				if(!empty($instance['size'])){ 
					if($instance['size'] == 'custom'){ 
						$display = 'display : block'; 
					}else{ 
						$display = 'display : none'; 
					}
				} 
				?>
				<p class="custom_size" style="<?php echo $display; ?>">
					<label for="<?php echo $this->get_field_name('width'); ?>"><?php esc_html_e('Width', 'my_image_widget'); ?></label>
					<input type="text" 
							name="<?php echo $this->get_field_name('width'); ?>" 
							id="<?php echo $this->get_field_id('width'); ?>" 
							value="<?php echo $width; ?>">
					<br>
					<label for="<?php echo $this->get_field_name('height'); ?>"><?php esc_html_e('Height', 'my_image_widget'); ?></label>
					<input type="text" 
							name="<?php echo $this->get_field_name('height'); ?>" 
							id="<?php echo $this->get_field_id('height') ?>" 
							value="<?php echo $height; ?>">
				</p>

				<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'align' ) ); ?>"><?php esc_attr_e( 'Align:', 'text_domain'); ?></label>
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'align' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'align' ) ); ?>">
					<option value="none"<?php if($align == 'none'){ ?> selected <?php } ?>><?php esc_attr_e('None','text_domain'); ?></option>
					<option value="left"<?php if($align == 'left'){ ?> selected <?php } ?>><?php esc_attr_e('Left','text_domain'); ?></option>
					<option value="center"<?php if($align == 'center'){ ?> selected <?php } ?>><?php esc_attr_e('Center','text_domain'); ?></option>
					<option value="right"<?php if($align == 'right'){ ?> selected <?php } ?>><?php esc_attr_e('Right','text_domain'); ?></option>
				</select>
				</p> 
			</div>
		<?php
    }
}