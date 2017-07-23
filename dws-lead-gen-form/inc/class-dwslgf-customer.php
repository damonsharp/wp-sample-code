<?php

namespace DWSLGF;

use Fieldmanager_TextArea;
use Fieldmanager_TextField;
use const DWSLGF_PREFIX;

/**
 * Customers
 *
 * Create custom post type, taxonomies, and metaboxes for customers
 *
 * @uses  WordPress Fieldmanager plugin (http://fieldmanager.org)
 */
class Customer {
	/**
	 * The post type.
	 *
	 * @var string the post type.
	 */
	private $post_type = DWSLGF_PREFIX . '_customer';

	/**
	 * Fire off custom post type creation, etc. here
	 */
	public function setup() {
		$data_structures = new Data_Structures();
		$data_structures->setup();
		$data_structures->add_post_type( $this->post_type, [
			'singular'     => 'Customer',
			'supports'     => [ 'title' ],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
		] );
		add_action( "fm_post_{$this->post_type}", [ $this, 'init' ] );
	}

	/**
	 * Fire off the meta box, etc. creation here
	 */
	public function init() {
		$fm = new Fieldmanager_TextField( [
			'name' => DWSLGF_PREFIX . '_phone',
		] );
		$fm->add_meta_box( 'Customer Phone Number', [ $this->post_type ] );

		$fm = new Fieldmanager_TextField( [
			'name' => DWSLGF_PREFIX . '_email',
		] );
		$fm->add_meta_box( 'Customer Email Address', [ $this->post_type ] );

		$fm = new Fieldmanager_TextField( [
			'name' => DWSLGF_PREFIX . '_budget',
		] );
		$fm->add_meta_box( 'Customer Desired Budget', [ $this->post_type ] );

		$fm = new Fieldmanager_TextArea( [
			'name' => DWSLGF_PREFIX . '_message',
		] );
		$fm->add_meta_box( 'Customer Message', [ $this->post_type ] );

		$fm = new Fieldmanager_TextField( [
			'name' => DWSLGF_PREFIX . '_submission_datetime',
		] );
		$fm->add_meta_box( 'Customer Posted Date/Time', $this->post_type );
	}
}