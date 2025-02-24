<?php
/*
Plugin Name: RS Gravity Forms IP Block
Description: Block IP addresses from submitting Gravity Forms. To use this plugin, provide an array of ip address strings using the filter <code>rsgf/get_ip_block_list</code>. To instantly delete blocked entries add: <code>add_filter( 'rsgf/delete_blocked_entries', '__return_true' );</code>
Version: 1.0.0
Author: Radley Sustaire
Author URI: https://radleysustaire.com/
GitHub Plugin URI: https://github.com/RadGH/RS-Gravity-Forms-IP-Block
GitHub Branch: master
*/

// EXAMPLE USAGE (Put in functions.php)

/*
// Specify custom block list
function rsgf_filter_ip_list( $ip_list ) {
	return array(
		'123.45.67.89',
	);
}
add_filter( 'rsgf/get_ip_block_list', 'rsgf_filter_ip_list' );

// Delete blocked entries (instead of marking as spam)
add_filter( 'rsgf/delete_blocked_entries', '__return_true' );
*/


/**
 * Get the IP address of the current user
 *
 * @return string
 */
function rsgf_get_ip() {
	$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? null;
	if ( $ip === null ) $ip = $_SERVER['REMOTE_ADDR'] ?? null;
	
	// Allow splitting if comma separated (cloudflare does this sometimes)
	if ( strpos( $ip, ',' ) !== false ) {
		$ip = explode( ',', $ip );
		$ip = trim( $ip[0] );
	}
	
	return $ip;
}

/**
 * Get the list of blocked IP addresses
 *
 * @return array
 */
function rsgf_get_ip_block_list() {
	return apply_filters( 'rsgf/get_ip_block_list', array() );
}


/**
 * Treat an entry as spam if it matches the ip block list
 *
 * @param bool $is_spam
 * @param array $form
 * @param array $entry
 *
 * @return bool
 */
function rsgf_filter_spam_entry( $is_spam, $form, $entry ) {
	// Get the IP address of the entry
	$ip = rsgf_get_ip();
	
	// Get the list of blocked IP addresses
	$blocked_ips = rsgf_get_ip_block_list();
	
	// Check if the IP address is in the blocked list
	if ( in_array( $ip, $blocked_ips ) ) {
		// Should we delete the blocked entry?
		if ( apply_filters('rsgf/delete_blocked_entries', false) ) {
			GFAPI::delete_entry( $entry['id'] );
		}
		
		$is_spam = true;
	}
	
	return $is_spam;
}
add_filter( 'gform_entry_is_spam', 'rsgf_filter_spam_entry', 10, 3 );