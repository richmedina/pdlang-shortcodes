<?php 
/**
 * Plugin Name: xPDLang Shortcodes
 * Plugin URI: 
 * Description: Specialized shortcodes for use with PDLang
 * Author: CLT
 * Version: 1.0
 */
//pdlang-shortcodes.php

//[pd_dateblock]
add_shortcode( 'pd_dateblock', 'pd_dateblock_func' );
function pd_dateblock_func($atts, $content = null) {
	global $post;
	$post = get_post($post->ID);
	setup_postdata($post);
	$s = get_field('start_date');
	$d = getdate(strtotime($s));
	$day = $d['mday'];
	$mon = substr($d['month'], 0, 3);
	$output .= "<div class='date-block-top'>{$mon}</div><div class='date-block-bottom'>{$day}</div>";
	return $output;
}

//[pd_presenters]
add_shortcode( 'pd_presenters', 'pd_presenters_func' );
function pd_presenters_func($atts, $content = null) {
	global $post;
	$post = get_post($post->ID);
	setup_postdata($post);	
	$posts = get_field('presenters__authors_relation');
	$output = '';
	if( $posts ) {
	    foreach( $posts as $p) {
	    	$name = get_field('full_name', $p->ID);
	    	$affiliation = get_field('affiliation', $p->ID);
	    	$position = get_field('position', $p->ID);
	    	$link = get_permalink($p->ID);
	    	$output .= "<div><a href='{$link}'> {$name}</a> ({$position}), {$affiliation} </div>";
	    }
	}

	return $output;
}

//[pd_related_materials]
add_shortcode('pd_related_materials', 'pd_related_materials_func');
function pd_related_materials_func($atts, $content = null) {
	global $post;
	$post = get_post($post->ID);
	setup_postdata($post);
	$output = "";
	// $file = get_field('materials');
	// if( $file ) {
 //    	$url = wp_get_attachment_url( $file );
 //    	var_dump($file);
	// }
 //    <a href="php echo esc_html($url);" >Download File</a>

	$materials = get_field('materials');
	
	if( $materials ) {
	    foreach( $materials as $p) {
			$file = get_field('upload_material', $p->ID);
			$link = $file['url'];
			$stype = $file['subtype'];
			$title = $file['title'];
			$output .= "<a href='{$link}'>{$title}</a> ({$stype})";
		}
	}
	return $output;
}

//[pd_title]
add_shortcode( 'pd_title', 'get_the_title' );

//[pd_tags]
add_shortcode('pd_tags', 'pd_tags_func');
function pd_tags_func($atts, $content = null) {
	global $post;
	$post = get_post($post->ID);
	// setup_postdata($post);
	$output = "";	
	$terms = get_the_terms($post->ID, 'experience_tags', 'Topics ', ', ');
	if( $terms ) {
	    foreach( $terms as $p) {
	    	$name = $p->name;
	    	$link = get_term_link($p);
	    	$output .= "<a href='{$link}'>{$name}</a>";
	    }
	}

	
	return $output;
}

