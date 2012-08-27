<?php


// [b]
add_bbcode('b', 'bbcode_b');
function bbcode_b( $atts = array(), $content = null ) {
	if ( $content ) {
		$content = '<strong class="bbcode">' . do_bbcode( $content ) . '</strong>'; 
	}
	return $content;
}


// [i]
add_bbcode('i', 'bbcode_i');
function bbcode_i( $atts = array(), $content = null ) {
	if ( $content ) {
		$content = '<em class="bbcode">' . do_bbcode( $content ) . '</em>'; 
	}
	return $content;
}


// [u]
add_bbcode('u', 'bbcode_u');
function bbcode_u( $atts = array(), $content = null ) {
	if ( $content ) {
		$content = '<span class="bbcode" style="text-decoration:underline">' . do_bbcode( $content ) . '</span>'; 
	}
	return $content;
}


// [url]
add_bbcode('url', 'bbcode_url');
function bbcode_url( $atts = array(), $content = null ) {
	if ( $content ) {

		if ( is_array($atts) && array_key_exists(0, $atts) && strpos($atts[0], '=') === 0 ) {
			$atts[0] = substr($atts[0], 1);
			$atts[0] = trim($atts[0], '"');
			$atts[0] = trim($atts[0], "'");
		}

		if ( isset($atts[0]) ) { // [url="http://wordpress.org/"]WordPress[/url]
			$url = $atts[0];
		} else { // [url]http://wordpress.org/[/url]
			$url = $content;
		}

		$content = '<a class="bbcode" href="' . esc_attr($url) . '">' . do_bbcode( $content ) . '</a>'; 
	}
	return $content;
}

add_filter('pre_do_bbcode', 'pre_bbcode_url');
function pre_bbcode_url($content) {
	// [url=http://wordpress.org/] -> [url="http://wordpress.org/"]
	$content = preg_replace('`\[url=([^"|\']*/)\]`', '[url="$1"]', $content);
	return $content;
}


// [img]
add_bbcode('img', 'bbcode_img');
function bbcode_img( $atts = array(), $content = null ) {
	if ( $content ) {
		$content = '<img class="bbcode" src="' . esc_attr($content) . '" alt="" />'; 
	}
	return $content;
}


// [sub]
add_bbcode('sub', 'bbcode_sub');
function bbcode_sub( $atts = array(), $content = null ) {
	if ( $content ) {
		$content = '<sub class="bbcode">' . do_bbcode( $content ) . '</sub>'; 
	}
	return $content;
}
// [sup]
add_bbcode('sup', 'bbcode_sup');
function bbcode_sup( $atts = array(), $content = null ) {
	if ( $content ) {
		$content = '<sup class="bbcode">' . do_bbcode( $content ) . '</sup>'; 
	}
	return $content;
}


// [del]
add_bbcode('del', 'bbcode_del');
function bbcode_del( $atts = array(), $content = null ) {
	if ( $content ) {
		$content = '<strike class="bbcode">' . do_bbcode( $content ) . '</strike>'; 
	}
	return $content;
}


// [color]
add_bbcode('color', 'bbcode_color');
function bbcode_color( $atts = array(), $content = null ) {
	if ( $content ) {

		if ( is_array($atts) && array_key_exists(0, $atts) && strpos($atts[0], '=') === 0 ) {
			$atts[0] = substr($atts[0], 1);
			$atts[0] = trim($atts[0], '"');
			$atts[0] = trim($atts[0], "'");
		}

		if ( preg_match('`^[a-z]+$`', $atts[0]) ) {
			$color = $atts[0];
		} elseif ( preg_match('`^[0-9A-F]{6}$`', $atts[0]) ) {
			$color = '#' . $atts[0];
		}
		if ( isset($color) ) {
			$content = '<span class="bbcode" style="color:' . $color . '">' . do_bbcode( $content ) . '</span>'; 
		}
	}
	return $content;
}


// [list]
add_bbcode('list', 'bbcode_list');
function bbcode_list( $atts = array(), $content = null ) {
	if ( $content ) {
		$content = str_replace('[*]', '<li>', $content);
		$content = '<ul class="bbcode">' . do_bbcode( $content ) . '</ul>'; 
	}
	return $content;
}


// [quote]
add_bbcode('quote', 'bbcode_quote');
function bbcode_quote( $atts = array(), $content = null ) {
	if ( $content ) {

		if ( is_array($atts) && array_key_exists(0, $atts) && strpos($atts[0], '=') === 0 ) {
			$atts[0] = substr($atts[0], 1);
			$atts[0] = trim($atts[0], '"');
			$atts[0] = trim($atts[0], "'");

			$content = sprintf('<blockquote class="bbcode"><div><cite>%s</cite>%s</div></blockquote>',
				sprintf( __('%s wrote:'), $atts[0]),
				do_bbcode( $content )
			);
		} else {
			$content = sprintf('<blockquote class="bbcode uncited"><div>%s</div></blockquote>',
				do_bbcode( $content )
			);
		}

	}
	return $content;
}


// [center]
add_bbcode('center', 'bbcode_center');
function bbcode_center( $atts = array(), $content = null ) {
	if ( $content ) {
		$content = '<div class="bbcode center" style="text-align:center">' . do_bbcode( $content ) . '</div>'; 
	}
	return $content;
}


// [noparse]
add_bbcode('noparse', 'bbcode_noparse');
function bbcode_noparse( $atts = array(), $content = null ) {
	if ( $content ) {
		// no parse, ne converti pas le bbcode du contenu
		$content = '<!-- bbcode noparse -->' . $content . '<!-- /bbcode noparse -->'; 
	}
	return $content;
}



// Todo
add_bbcode('spoiler', 'bbcode_spoiler');
function bbcode_spoiler( $atts = array(), $content = null ) {
	if ( $content ) {
		$content = do_bbcode( $content ); 
	}
	return $content;
}


?>