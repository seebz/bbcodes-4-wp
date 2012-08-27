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
	wp_enqueue_style( 'bbcodes4wp', plugins_url('/style.css', __FILE__) );
}
function bbcodes4wp_enqueue_script() {
	wp_enqueue_script( 'bbcodes4wp', plugins_url('/script.js', __FILE__), array('jquery', 'quicktags') );
}

add_action( 'wp_enqueue_scripts', 'bbcodes4wp_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'bbcodes4wp_enqueue_script' );



/**
 * Activation des bbcodes pour les contenus 
 */

// WP
add_filter('the_content', 'do_bbcode', 11); // AFTER wpautop()

// BBpress
add_filter('bbp_get_reply_content', 'do_bbcode', 11);

// BuddyPress
add_filter('bp_get_the_topic_post_content', 'do_bbcode', 11);



?>