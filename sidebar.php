<aside class="span4 padded">

	<?php global $post;

	wp_reset_query();

	// Sidebar for the Product section pages
	if ( 15 == $post->ID || in_array( $post->ID, get_child_ids_array( 15 ) ) ) : ?>

		<ul class="sidebar-submenu unstyled margin-btm">
			<?php wp_list_pages( 'title_li=&child_of=15&depth=-1' ); ?>
		</ul>

		<div class="plans-sidebar-area">
			<h3>updox integrates with existing EHR systems:</h3>
			<ul>
				<?php wp_list_pages( 'child_of=40&title_li&depth=-1' ); ?>
			</ul>
		</div>
		
	<?php endif;


	// Sidebar for the Pricing and Plans pages
	if ( 2 == $post->ID || in_array( $post->ID, get_child_ids_array( 2 ) ) ) : ?>

		<div class="plans-sidebar-area">

			<?php if (in_array( $post->ID, get_child_ids_array( 40 ) ) ) : ?>
				<h3>Benefits you can expect:</h3>
				<?php echo get_post_meta($post->ID, '_benefits', true);
			
			else : ?>

				<h3>updox integrates with existing EHR systems:</h3>
				<ul>
					<?php wp_list_pages( 'child_of=40&title_li&depth=-1' ); ?>
				</ul>
			<?php endif; ?>
		</div>
		<div class="plan-price">
			<div class="plan-price-inner">
				<span class="price">
					$<?php echo get_post_meta($post->ID, '_price', true); ?>
				</span>
				<span class="price-term">&nbsp;/&nbsp; mo&#42;</span>
			</div>
		</div>
		<div class="arrow-large"></div>
		<div class="well start-trial">
			<a href="http://myupdox.com/updox-ui/" class="external-link">
				<img src="<?php bloginfo('template_directory'); ?>/assets/images/free-trial.png" alt="Free trial icon">
			</a>
			<p>Updox Electronic Fax and Patient Portal Free Trial We are so confident you will love our service we are offering a <strong>15 Day FREE trial</strong>...</p>
			<a href="http://myupdox.com/updox-ui/" class="capitalize btn btn-link centered">Start trial now</a>
		</div>

		<?php echo get_post_meta($post->ID, '_price_desc', true); ?>

	<?php endif;

	
	// Sidebar for Company section
	$company = is_page( 51 );
	$companyChildren = in_array( $post->ID, get_child_ids_array( 51 ) );
	$newsPost = $post->ID == 324;
	//$newsPostChildren = in_array($post->ID, get_child_ids_array(324));

	if ( $company || $companyChildren || $newsPost || 'careers' == $post->post_type ) : ?>

		<ul class="sidebar-submenu unstyled margin-btm">
			<?php wp_list_pages( 'title_li=&child_of=51&depth=-1' ); ?>
		</ul>
		<div class="contact sidebar">
			<a class="submenu" href="<?php echo get_permalink(54); ?>">Contact updox</a>
			
			<div class="address">
				<p>
					<?php echo gcb(1);?><br>
					<?php echo gcb(8);?>
				</p>
			</div>
			
			<div class="map">
				<p>
					<?php echo gcb(3);?>
				</p>
			</div>

			<div class="phone">
				<p>
					<?php echo gcb(2);?>
				</p>
			</div>
		</div>

	<?php endif;


	// If on the contact page or contact confirmation
	if ( is_page( array( 54, 153 ) ) ) : ?>

		<div class="contact sidebar">
			<div class="address">
				<p>
					<?php echo gcb( 1 );?><br>
					<?php echo gcb( 8 );?>
				</p>
			</div>
		</div>

	<?php endif;

	// If on event calendar page (/training) or event single page (/event/{event-title})
	if ( ( -9999 == $post->ID && 'page' == $post->post_type ) || 'tribe_events' == $post->post_type ) :

		get_template_part( 'inc/form', 'email-reminder' );?>
	
		<div class="gotomeeting">
			<p>Download GoToMeeting for free to connect to our online training sessions.</p>
			<p>
				<a href="http://www.gotomeeting.com/">
					<img src="<?php bloginfo( 'template_directory' ) ?>/assets/images/button-gotomeeting.png" alt="GoToMeeting Logo">
				</a>
			</p>
		</div>

	<?php endif; ?>
	
</aside>