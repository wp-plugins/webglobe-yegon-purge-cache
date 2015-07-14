<?php
/*
Plugin Name: WY Purge Cache
Description: Purges the nginx proxy cache when you publish or update a post or page.
Version: 1.0
Author: Webglobe - Yegon s.r.o.
*/

function wypurge_cache($post_id) {
	$urls = array();

	$link = get_permalink($post_id);
	$parse = parse_url($link);
	array_push($urls, $parse[scheme].'://'.$parse[host].'/purge'.$parse[path]);

	$home_page = home_url();
	$parse_home = parse_url($home_page);
	$home_page_url = $parse[scheme].'://'.$parse[host].'/purge';
	if ($parse_home[path] != '') { 
		$home_page_url = $home_page_url.$parse_home[path].'/';
	} else {
		$home_page_url = $home_page_url.'/';
	}
	array_push($urls, $home_page_url);


	if ( get_option('show_on_front') == 'page' ) {
		$posts_page = get_option('page_for_posts');
		$posts_page_link = get_permalink($posts_page);
		$parse_posts = parse_url($posts_page_link);
		array_push($urls, $parse_posts[scheme].'://'.$parse_posts[host].'/purge'.$parse_posts[path]);
	}

	array_push($urls, $home_page_url.'feed/');
	array_push($urls, $home_page_url.'comments/feed/');

	foreach (array_unique($urls) as $uri) {
		wp_remote_get($uri);
	};
}

add_action('edit_post', 'wypurge_cache');
