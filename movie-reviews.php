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
    add_action( 'init', array($this, 'register_post_type'));
  }

  function register_post_type() {
    register_post_type( 'tf_book_review', array(
      'labels'  =>  array(
        'name'  =>  __('Book Reviews'),
        'singular_name' =>  __('Movie Review'),
      ),
      'description' =>  __('Opinionated book reviews'),
      'supports'  =>  array(
        'title', 'editor', 'excerpt', 'author', 'revisions', 'thumbnail', 'custom-fields'
      ),
      'public'  =>  TRUE,
    ));
  }
}

TF_Book_Reviews::getInstance();
