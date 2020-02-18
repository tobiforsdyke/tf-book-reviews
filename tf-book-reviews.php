<?php
/*
Plugin Name: Book Reviews
Plugin URI: https://www.tobiforsdyke.co.uk
Description: Book Review post type
Author: Tobi Forsdyke
Author URI: https://www.tobiforsdyke.co.uk
Version: 1.0
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// required plugin class
require_once dirname( __FILE__ ) . '/lib/class-tgm-plugin-activation.php';

class TF_Book_Reviews {
	private static $instance;

	const FIELD_PREFIX = 'tfbr_';
	const CPT_SLUG = 'book_review';

	// this needs to be hard-coded, but this serves as a reminder,
	// and a find replace when searching and replacing
	const TEXT_DOMAIN = 'tf-book-reviews';

	public static function getInstance() {
		if (self::$instance == NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		// initialize Book Review custom post type
		add_action('init', 'TF_Book_Reviews::register_post_type' );
		// initialize custom taxonomies
		add_action('init', 'TF_Book_Reviews::register_taxonomies' );

		// initialize custom fields from Metabox.io:
		// first check for required plugin
		add_action( 'tgmpa_register', array( $this, 'check_required_plugins' ) );
		// then define the fields
		add_filter( 'rwmb_meta_boxes', array( $this, 'metabox_custom_fields' ) );
		// Add custom template and stylesheet
		add_action( 'template_include', array( $this, 'add_cpt_template' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles_scripts' ) );
	}

	/**
	 * Registers the Book Review custom post type
	 *
	 * Defined statically for use in activation hook
	 */
	public static function register_post_type() {
		register_post_type(self::CPT_SLUG, array(
			'labels' => array(
				'name' => __('Book Reviews'),
				'singular_name' => __('Book Review'),
			),
			'description' => __('Opinionated book reviews'),
			'supports' => array(
				'title', 'editor', 'excerpt', 'author', 'revisions', 'thumbnail',
			),
			'public' => TRUE,
			'menu_icon' => 'dashicons-book-alt',
		));
	}

		/**
		 * Registers the taxonomies
		 */
		public static function register_taxonomies() {
			register_taxonomy('book_types', array( self::CPT_SLUG ), array(
				'labels' => array(
					'name' => __('Genres'),
					'singular_name' => __('Genre'),
					'all_items' => __( 'All Genres' ),
					'edit_item' => __( 'Edit Genre' ),
					'view_item' => __( 'View Genre' ),
					'update_item' => __( 'Update Genre' ),
					'add_new_item' => __( 'Add New Genre' ),
					'new_item_name' => __( 'New Genre' ),
				),
				'public' => TRUE,
				'hierarchical' => TRUE,
				'rewrite' => array(
					'slug' => 'book-genres',
				),
			));
		}

	/**
	 * Activation hook (see register_activation_hook)
	 */
	public static function activate() {
		self::register_post_type();
		self::register_taxonomies();
		flush_rewrite_rules();
	}

	/**
	 * Implementation of the TGM Plugin Activation library
	 *
	 * Checks for the plugin(s) we need, and displays the appropriate messages
	 */
	function check_required_plugins() {
		$plugins = array(
			array(
				'name'               => 'Meta Box',
				'slug'               => 'meta-box',
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
		);

		$config  = array(
			'domain'           => 'tf_book_reviews',
			'default_path'     => '',
			'parent_slug'      => 'plugins.php',
			'capability'       => 'update_plugins',
			'menu'             => 'install-required-plugins',
			'has_notices'      => true,
			'is_automatic'     => false,
			'message'          => '',
			'strings'          => array(
				'page_title'                      => __( 'Install Required Plugins', 'tf-book-reviews' ),
				'menu_title'                      => __( 'Install Plugins', 'tf-book-reviews' ),
				'installing'                      => __( 'Installing Plugin: %s', 'tf-book-reviews' ),
				'oops'                            => __( 'Something went wrong with the plugin API.', 'tf-book-reviews' ),
				'notice_can_install_required'     => _n_noop( 'The Book Reviews plugin depends on the following plugin: %1$s.', 'The Book Reviews plugin depends on the following plugins: %1$s.' ),
				'notice_can_install_recommended'  => _n_noop( 'The Book Reviews plugin recommends the following plugin: %1$s.', 'The Book Reviews plugin recommends the following plugins: %1$s.' ),
				'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ),
				'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ),
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ),
				'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ),
				'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ),
				'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ),
				'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
				'activate_link'                   => _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
				'return'                          => __( 'Return to Required Plugins Installer', 'tf-book-reviews' ),
				'plugin_activated'                => __( 'Plugin activated successfully.', 'tf-book-reviews' ),
				'complete'                        => __( 'All plugins installed and activated successfully. %s', 'tf-book-reviews' ),
				'nag_type'                        => 'updated',
			)
		);
		tgmpa( $plugins, $config );
	}

	/**
	 * Create custom fields using metabox.io
	 */
	function metabox_custom_fields() {
		// define the book custom fields
		$meta_boxes[] = array(
			'id'       => 'book_data',
			'title'    => 'Additional Information',
			'pages'    => array( self::CPT_SLUG ),
			'context'  => 'normal',
			'priority' => 'high',
			'fields' => array(
				array(
					'name'  => 'Release Year',
					'desc'  => 'Year the book was published',
					'id'    => self::FIELD_PREFIX . 'book_year',
					'type'  => 'number',
					'std'   => date('Y'),
					'min'   => '1896',
				),
				array(
					'name'  => 'Author',
					'desc'  => 'Who wrote this book',
					'id'    => self::FIELD_PREFIX . 'book_author',
					'type'  => 'text',
					'std'   => '',
				),
				array(
					'name'  => 'Website Link',
					'desc'  => 'Link to this book on Amazon or Goodreads',
					'id'    => self::FIELD_PREFIX . 'book_link',
					'type'  => 'url',
					'std'   => '',
				),
			)
		);

		// define the review custom field(s)
		$meta_boxes[] = array(
			'id'       => 'review_data',
			'title'    => 'Review',
			'pages'    => array( self::CPT_SLUG ),
			'context'  => 'side',
			'priority' => 'high',
			'fields' => array(
				array(
					'name'    => 'Rating',
					'desc'    => 'On a scale of 1-5 (5 being the best)',
					'id'      => self::FIELD_PREFIX . 'book_rating',
					'type'    => 'select',
					'options' => array(
						'' => __('TBR (To be rated)'),
            0  => __('0 - Gave up, did not finish!'),
						1  => __('1 - Hated it!'),
						2  => __('2 - Finished it but did not enjoy it'),
						3  => __('3 - Not bad. Somewhat enjoyed it'),
						4  => __('4 - Really enjoyed it'),
						5  => __('5 - Loved it! Would read it again'),
					),
					'std' => '',
				),
			)
		);

		return $meta_boxes;
	}

	/**
	 * Template include to add a custom template
	 */
	function add_cpt_template( $template ) {
		if (is_singular( self::CPT_SLUG )) {
			// Check active theme directory for a version of the template
			if (file_exists( get_stylesheet_directory() . '/single-' . self::CPT_SLUG . '.php' )) {
				return get_stylesheet_directory() . '/single-' . self::CPT_SLUG . '.php';
			}
			// Else, use the bundled copy
			return plugin_dir_path(__FILE__) . 'single-' . self::CPT_SLUG . '.php';
		}
		return $template;
	}
	/**
	 * Enqueues the stylesheet for the book review post type
	 */
	function add_styles_scripts() {
		wp_enqueue_style( 'book-review-style', plugin_dir_url( __FILE__ ) . 'book-reviews.css' );
	}
}

// initialize plugin
TF_Book_Reviews::getInstance();

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'TF_Book_Reviews::activate' );
