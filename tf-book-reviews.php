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

class TF_Book_Reviews {
  private static $instance;

  public static function getInstance() {
    if (self::$instance == NULL) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  private function __construct() {
    // implement hooks here
    add_action( 'init', 'TF_Book_Reviews::register_post_type' );
  }

  static function register_post_type() {
    register_post_type( 'tf_book_review', array(
      'labels'  =>  array(
        'name'  =>  __('Book Reviews'),
        'singular_name' =>  __('Book Review'),
      ),
      'description' =>  __('Opinionated book reviews'),
      'supports'  =>  array(
        'title', 'editor', 'excerpt', 'author', 'revisions', 'thumbnail', 'custom-fields'
      ),
      'public'  =>  TRUE,
      'menu_icon' =>  'dashicons-book-alt',
    ));
  }

  static function activate() {
    self::register_post_type();
    flush_rewrite_rules();
  }
}

TF_Book_Reviews::getInstance();

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'TF_Book_Reviews::activate' );
