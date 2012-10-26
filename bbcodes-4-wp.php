<?php
/*
 Plugin Name: BBcodes for WordPress
 Plugin URI:  http://github.com/Seebz/bbcodes-4-wp
 Description: Support des bbcodes pour WordPress
 Version:     1.1
 Author:      Seebz
 Author URI:  http://seebz.net/
 */


// Chargement de l'API
include_once 'api.php';

// Chargement des bbcodes
include_once 'bbcodes.php';



/**
 * Assets
 */

function bbcodes4wp_enqueue_style() {
	wp_enqueue_style( 'editor-buttons' );
	//wp_enqueue_style( 'bbcodes4wp', plugins_url('/style.css', __FILE__), array('editor-buttons'), '1.1' );
}
function bbcodes4wp_enqueue_script() {
	wp_enqueue_script( 'bbcodes4wp', plugins_url('/script.js', __FILE__), array('jquery', 'quicktags'), '1.1' );
}

add_action( 'wp_enqueue_scripts', 'bbcodes4wp_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'bbcodes4wp_enqueue_script' );



/**
 * Activation des bbcodes pour les contenus
 */

// WordPress
add_filter('the_content', 'do_bbcode', 11); // AFTER wpautop()

// bbPress
add_filter('bbp_get_reply_content', 'do_bbcode');

// BuddyPress
add_filter('bp_get_the_topic_post_content', 'do_bbcode');
add_filter('bp_get_activity_content_body',  'do_bbcode');
add_filter('bp_activity_comment_content',   'do_bbcode');
add_filter('bp_get_activity_latest_update', 'do_bbcode');
add_filter('bp_get_group_description',      'do_bbcode');



/**
 * WP filters
 */

add_filter('get_comment', 'get_comment_with_bbcode');
function get_comment_with_bbcode($comment) {
	if ( ! is_admin())
		$comment->comment_content = do_bbcode($comment->comment_content);
	return $comment;
}



/**
 * bbPress filters
 */

add_filter('bbp_use_wp_editor', '__return_false');



?>