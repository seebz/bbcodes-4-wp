<?php

/**
 * Adaptation de WPINC . '/shortcodes.php' pour les bbcodes
 */

if (function_exists('add_bbcode')) return;




/**
 * Container for storing bbcode tags and their hook to call for the bbcode
 *
 * @since 1.0
 * @name $bbcode_tags
 * @var array
 * @global array $bbcode_tags
 */
$bbcode_tags = array();

/**
 * Add hook for bbcode tag.
 *
 * There can only be one hook for each bbcode. Which means that if another
 * plugin has a similar bbcode, it will override yours or yours will override
 * theirs depending on which order the plugins are included and/or ran.
 *
 * Simplest example of a bbcode tag using the API:
 *
 * <code>
 * // [footag foo="bar"]
 * function footag_func($atts) {
 * 	return "foo = {$atts[foo]}";
 * }
 * add_bbcode('footag', 'footag_func');
 * </code>
 *
 * Example with nice attribute defaults:
 *
 * <code>
 * // [bartag foo="bar"]
 * function bartag_func($atts) {
 * 	extract(bbcode_atts(array(
 * 		'foo' => 'no foo',
 * 		'baz' => 'default baz',
 * 	), $atts));
 *
 * 	return "foo = {$foo}";
 * }
 * add_bbcode('bartag', 'bartag_func');
 * </code>
 *
 * Example with enclosed content:
 *
 * <code>
 * // [baztag]content[/baztag]
 * function baztag_func($atts, $content='') {
 * 	return "content = $content";
 * }
 * add_bbcode('baztag', 'baztag_func');
 * </code>
 *
 * @since 1.0
 * @uses $bbcode_tags
 *
 * @param string $tag BBcode tag to be searched in post content.
 * @param callable $func Hook to run when bbcode is found.
 */
function add_bbcode($tag, $func) {
	global $bbcode_tags;

$tag = strtolower($tag);
	if ( is_callable($func) )
		$bbcode_tags[$tag] = $func;
}

/**
 * Removes hook for bbcode.
 *
 * @since 1.0
 * @uses $bbcode_tags
 *
 * @param string $tag bbcode tag to remove hook for.
 */
function remove_bbcode($tag) {
	global $bbcode_tags;

	unset($bbcode_tags[$tag]);
}

/**
 * Clear all bbcodes.
 *
 * This function is simple, it clears all of the bbcode tags by replacing the
 * bbcodes global by a empty array. This is actually a very efficient method
 * for removing all bbcodes.
 *
 * @since 1.0
 * @uses $bbcode_tags
 */
function remove_all_bbcodes() {
	global $bbcode_tags;

	$bbcode_tags = array();
}

/**
 * Search content for bbcodes and filter bbcodes through their hooks.
 *
 * If there are no bbcode tags defined, then the content will be returned
 * without any filtering. This might cause issues when plugins are disabled but
 * the bbcode will still show up in the post or content.
 *
 * @since 1.0
 * @uses $bbcode_tags
 * @uses get_bbcode_regex() Gets the search pattern for searching bbcodes.
 *
 * @param string $content Content to search for bbcodes
 * @return string Content with bbcodes filtered out.
 */
function do_bbcode($content) {
	global $bbcode_tags;

	if (empty($bbcode_tags) || !is_array($bbcode_tags))
		return $content;

$content = apply_filters( 'pre_do_bbcode', $content );

	$pattern = get_bbcode_regex();
//	return preg_replace_callback( "/$pattern/s", 'do_bbcode_tag', $content );
	return preg_replace_callback( "/$pattern/is", 'do_bbcode_tag', $content );
}

/**
 * Retrieve the bbcode regular expression for searching.
 *
 * The regular expression combines the bbcode tags in the regular expression
 * in a regex class.
 *
 * The regular expression contains 6 different sub matches to help with parsing.
 *
 * 1 - An extra [ to allow for escaping bbcodes with double [[]]
 * 2 - The bbcode name
 * 3 - The bbcode argument list
 * 4 - The self closing /
 * 5 - The content of a bbcode when it wraps some content.
 * 6 - An extra ] to allow for escaping bbcodes with double [[]]
 *
 * @since 1.0
 * @uses $bbcode_tags
 *
 * @return string The bbcode search regular expression
 */
function get_bbcode_regex() {
	global $bbcode_tags;
	$tagnames = array_keys($bbcode_tags);
	$tagregexp = join( '|', array_map('preg_quote', $tagnames) );

	// WARNING! Do not change this regex without changing do_bbcode_tag() and strip_bbcode_tag()
	return
		  '\\['                              // Opening bracket
		. '(\\[?)'                           // 1: Optional second opening bracket for escaping bbcodes: [[tag]]
		. "($tagregexp)"                     // 2: BBcode name
		. '\\b'                              // Word boundary
		. '('                                // 3: Unroll the loop: Inside the opening bbcode tag
		.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
		.     '(?:'
		.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
		.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
		.     ')*?'
		. ')'
		. '(?:'
		.     '(\\/)'                        // 4: Self closing tag ...
		.     '\\]'                          // ... and closing bracket
		. '|'
		.     '\\]'                          // Closing bracket
		.     '(?:'
		.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing bbcode tags
		.             '[^\\[]*+'             // Not an opening bracket
		.             '(?:'
		.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing bbcode tag
		.                 '[^\\[]*+'         // Not an opening bracket
		.             ')*+'
		.         ')'
		.         '\\[\\/\\2\\]'             // Closing bbcode tag
		.     ')?'
		. ')'
		. '(\\]?)';                          // 6: Optional second closing brocket for escaping bbcodes: [[tag]]
}

/**
 * Regular Expression callable for do_bbcode() for calling bbcode hook.
 * @see get_bbcode_regex for details of the match array contents.
 *
 * @since 1.0
 * @access private
 * @uses $bbcode_tags
 *
 * @param array $m Regular expression match array
 * @return mixed False on failure.
 */
function do_bbcode_tag( $m ) {
	global $bbcode_tags;

	// allow [[foo]] syntax for escaping a tag
	if ( $m[1] == '[' && $m[6] == ']' ) {
		return substr($m[0], 1, -1);
	}

	$tag = $m[2];
$tag = strtolower($tag);
	$attr = bbcode_parse_atts( $m[3] );

	if ( isset( $m[5] ) ) {
		// enclosing tag - extra parameter
		return $m[1] . call_user_func( $bbcode_tags[$tag], $attr, $m[5], $tag ) . $m[6];
	} else {
		// self-closing tag
		return $m[1] . call_user_func( $bbcode_tags[$tag], $attr, null,  $tag ) . $m[6];
	}
}

/**
 * Retrieve all attributes from the bbcodes tag.
 *
 * The attributes list has the attribute name as the key and the value of the
 * attribute as the value in the key/value pair. This allows for easier
 * retrieval of the attributes, since all attributes have to be known.
 *
 * @since 1.0
 *
 * @param string $text
 * @return array List of attributes and their value.
 */
function bbcode_parse_atts($text) {
	$atts = array();
	$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
	$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
	if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
		foreach ($match as $m) {
			if (!empty($m[1]))
				$atts[strtolower($m[1])] = stripcslashes($m[2]);
			elseif (!empty($m[3]))
				$atts[strtolower($m[3])] = stripcslashes($m[4]);
			elseif (!empty($m[5]))
				$atts[strtolower($m[5])] = stripcslashes($m[6]);
			elseif (isset($m[7]) and strlen($m[7]))
				$atts[] = stripcslashes($m[7]);
			elseif (isset($m[8]))
				$atts[] = stripcslashes($m[8]);
		}
	} else {
		$atts = ltrim($text);
	}

	return $atts;
}

/**
 * Combine user attributes with known attributes and fill in defaults when needed.
 *
 * The pairs should be considered to be all of the attributes which are
 * supported by the caller and given as a list. The returned attributes will
 * only contain the attributes in the $pairs list.
 *
 * If the $atts list has unsupported attributes, then they will be ignored and
 * removed from the final returned list.
 *
 * @since 1.0
 *
 * @param array $pairs Entire list of supported attributes and their defaults.
 * @param array $atts User defined attributes in bbcode tag.
 * @return array Combined and filtered attribute list.
 */
function bbcode_atts($pairs, $atts) {
	$atts = (array)$atts;
	$out = array();
	foreach($pairs as $name => $default) {
		if ( array_key_exists($name, $atts) )
			$out[$name] = $atts[$name];
		else
			$out[$name] = $default;
	}
	return $out;
}

/**
 * Remove all bbcode tags from the given content.
 *
 * @since 1.0
 * @uses $bbcode_tags
 *
 * @param string $content Content to remove bbcode tags.
 * @return string Content without bbcode tags.
 */
function strip_bbcodes( $content ) {
	global $bbcode_tags;

	if (empty($bbcode_tags) || !is_array($bbcode_tags))
		return $content;

	$pattern = get_bbcode_regex();

	return preg_replace_callback( "/$pattern/s", 'strip_bbcode_tag', $content );
}

function strip_bbcode_tag( $m ) {
	// allow [[foo]] syntax for escaping a tag
	if ( $m[1] == '[' && $m[6] == ']' ) {
		return substr($m[0], 1, -1);
	}

	return $m[1] . $m[6];
}
