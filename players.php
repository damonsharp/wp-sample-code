<?php $players = MBB_Query()->get_players(); ?>
<?php if ( ! empty( $players ) ) : ?><?php foreach ( $players as $post ) : setup_postdata( $post ); ?><?php $player_meta = get_post_meta( $post->ID, 'player_details', true ); ?>
	<article class="player">
		<div class="portrait-container">
			<?php if ( has_post_thumbnail() ) : ?><?php the_post_thumbnail( 'medium' ); ?><?php else : ?>
				<img class="wp-post-image" src="<?php echo bloginfo( 'template_directory' ); ?>/assets/img/placeholder.jpg" alt="placeholder image">
			<?php endif; ?>
			<h2><?php the_title(); ?></h2>
		</div>
		<div class="details">
			<?php if ( ! empty( $player_meta['bio']['bio'] ) ) : ?>
				<p class="bio">
					<strong>Bio:</strong>
					<?php echo wp_kses_post( $player_meta['bio']['bio'] ); ?>
				</p>
			<?php endif; ?>
			<?php if ( ! empty( $player_meta['details']['grad_year'] ) ) : ?>
				<p>
					<strong>Grade:</strong>
					<?php echo esc_html( ucwords( MBB_Helpers()->get_current_grade_level( $player_meta['details']['grad_year'] ) ) ); ?>
				</p>
			<?php endif; ?>
			<?php if ( ! empty( $player_meta['details']['positions'] ) ) : ?>
				<p>
					<strong>Positions:</strong>
					<?php echo esc_html( implode( ', ', $player_meta['details']['positions'] ) ); ?>
				</p>
			<?php endif; ?>
			<?php if ( ! empty( $player_meta['details']['bats'] ) ) : ?>
				<p>
					<strong>Bats: </strong>
					<?php echo esc_html( $player_meta['details']['bats'] ); ?>
				</p>
			<?php endif; ?>
			<?php if ( ! empty( $player_meta['details']['throws'] ) ) : ?>
				<p>
					<strong>Throws: </strong>
					<?php echo esc_html( $player_meta['details']['throws'] ); ?>
				</p>
			<?php endif; ?>
			<a class="btn secondary" href="<?php echo esc_url( get_the_permalink( $player->ID ) ); ?>">
				View More Details &rarr;
			</a>
		</div>
	</article>
<?php endforeach; endif; ?>
<?php wp_reset_postdata(); ?>
