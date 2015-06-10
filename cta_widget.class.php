<?php

	/**
	 * Call to action widget
	 * 
	 * Create a call to action button based on custom post type options
	 * 
	 */
	class CTA_Widget extends WP_Widget {

		/**
		 * Sets up the widgets name etc
		 */
		public function __construct() {
			
			parent::__construct(
				'cta_widget',
				__( 'Call to Action', 'sws' ),
				array(
					'description' => __( 'Drag into a widget area to customize a call to action banner.', 'sws' )
				)
			);

		}

		/**
		 * Outputs the content of the widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			
			$cta_widget = get_post( $instance['cta_widget_choice'], 'OBJECT', 'display' );
			$cta_widget_meta_link = get_post_meta( $cta_widget->ID, '_call_to_action_link', true );
			$cta_widget_meta_link_new_window = get_post_meta( $cta_widget->ID, '_call_to_action_link_new_window', true ); ?>
			<?php if ( $cta_widget ) {
				echo $args['before_widget']; ?>
				<div class="cta-widget-item <?php echo $instance['cta_widget_background']; ?>">
					<a href="<?php echo $cta_widget_meta_link; ?>"<?php echo $cta_widget_meta_link_new_window ? ' target="_blank"' : ''; ?>>
						<div class="pull-left">
							<?php
								if ( has_post_thumbnail( $cta_widget->ID ) ) {
									echo get_the_post_thumbnail( $cta_widget->ID, 'cta-icon' );
								} else { ?>
									<img src="<?php bloginfo( 'template_directory' ); ?>/assets/img/missing_cta_image.png" alt="Missing call to action graphic">
								<?php }
							?>
						</div>
						<div>
							<strong><?php echo $cta_widget->post_title; ?></strong>
						</div>
					</a>
				</div>
				<?php echo $args['after_widget'];
			}

		}

		/**
		 * Outputs the options form on admin
		 *
		 * @param array $instance widget options
		 */
		public function form( $instance ) {
			
			$options = array(
				'cta-background-green' => 'Green',
				'cta-background-drk-green' => 'Dark Green',
				'cta-background-orange' => 'Orange',
				'cta-background-gray' => 'Gray',
				'cta-background-red' => 'Red',
				'cta-background-blue' => 'Blue',

			);
			asort( $options );

			$args = array(
				'post_type' => 'calls_to_action',
				'order_by' => 'name'
			);
			$cta_widgets = new WP_Query( $args );

			if ( $cta_widgets->have_posts() ) : ?>
				<p><label for="<?php echo $this->get_field_id( 'cta-widget-choice' ); ?>">Choose a Call to Action to display...</label></p>
				<p>
					<select name="<?php echo $this->get_field_name( 'cta_widget_choice' ); ?>" id="<?php echo $this->get_field_id( 'cta-widget-choice' ); ?>" class="cta-widget-select">
						<option value="0">...</option>
						<?php while ( $cta_widgets->have_posts() ) : $cta_widgets->the_post(); ?>
							<option value="<?php the_ID(); ?>" <?php echo ( isset($instance['cta_widget_choice']) && $instance['cta_widget_choice'] == get_the_ID() ) ? 'selected="selected"' : ''; ?>><?php the_title(); ?></option>
						<?php endwhile; wp_reset_postdata(); ?>
					</select>
				</p>
				<p><label for="<?php echo $this->get_field_id( 'cta-widget-background' ); ?>">Choose a background color...</label></p>
				<p>
					<select name="<?php echo $this->get_field_name( 'cta_widget_background' ); ?>" id="<?php echo $this->get_field_id( 'cta-widget-background' ); ?>" class="">
						<option value="0">...</option>
						<?php foreach ( $options as $css_class => $color ) : ?>
							<option value="<?php echo $css_class; ?>" <?php echo ( isset($instance['cta_widget_background']) && $instance['cta_widget_background'] == $css_class ) ? 'selected="selected"' : ''; ?>><?php echo $color; ?></option>
						<?php endforeach; ?>
					</select>
				</p>
			<?php else : ?>
				<p>You have not created any Calls to Action. Create your first one by <a href="<?php echo admin_url( 'post-new.php?post_type=calls_to_action' ); ?>">clicking here.</a></p>
			<?php endif;
		}

		/**
		 * Processing widget options on save
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			
			$instance = $old_instance;
			foreach ( $new_instance as $key => $value ) {
				$instance[$key] = $value;
			}
			return $instance;

		}

	}