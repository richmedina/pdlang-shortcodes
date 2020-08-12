<?php 
/**
 * Plugin Name: xPDLang Shortcodes
 * Plugin URI: 
 * Description: Specialized shortcodes for use with PDLang
 * Author: CLT
 * Version: 1.0
 */
//pdlang-shortcodes.php

// Include to add access to the ACF functions. Necessary for querying reverse relationships.
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

function pd_exp_html_blk_sm($p, $show_thumb=true, $show_blurb=false) {
	setup_postdata( $p );
	// $related_exps->the_post();
	// var_dump($p->post_title);
	$title = $p->post_title;
	$link = get_permalink($p->ID);
	$access_link = get_field('url_website', $p->ID);
	$resource_type = get_field('pd_resource', $p->ID);
	$description = get_field('resource_description', $p->ID);
	$description = wp_trim_words($description, 20, ' ...');
	$thumb = get_the_post_thumbnail($p);
	$mod_date = get_the_modified_date('', $p->ID);
	$start_date = get_field('start_date', $p->ID);
	$people = get_field('presenters__facilitators_relation', $p->ID);
	$series = get_the_terms( $p->ID, 'series', 'Part of ', ', ');
	$tags = get_the_terms( $p->ID, 'experience_tags', ' ', ', ');
	
	$html = "<article class='card-wrap-row flip'>";
	$html .= "<div class='card'>";
	$html .= "<header class='card-header'>";
	$html .= "<h4 class='card-title'><a href='{$link}'>{$title}</a></h4>";
	if($access_link) {
		$html .= "<a href='{$access_link}' target='_blank'><span class='label lbl-blu pd_resource_label'>{$resource_type}</span></a>";
	} else {
		$html .= "<span class='label lbl-blu pd_resource_label'>{$resource_type}</span>";
	}
	$html .= "";

    if( $people ) {
		$html .= " by ";
		$len = count($people);
		foreach( $people as $idx => $ppl) {
			$pname = $ppl->post_title;
			$plink = get_permalink($ppl->ID);
			$html .= "<span><a href='{$plink}'>{$pname}</a>";
			if ($idx === $len - 2) $html .= " & ";
			else if ($idx < $len -1) $html .= ", ";
			$html .= "</span>";
		}
    }
    $html .= "<div><time>{$start_date}</time>  <time class='mod-date'>Updated {$mod_date}</time></div>";
	$html .= "</header>";
	
	if( $show_blurb ) $html .= "<div class='card-body'>{$description}</div>";
	
	$html .= "<footer class='card-footer'>";
	if( $series ) {
		$html .= "<div class='tag-series'>Part of ";
		$len = count($series);
	    foreach( $series as $idx => $t) {
	    	$name = $t->name;
	    	$link = get_term_link($t);
	    	$html .= "<a href='{$link}'>{$name}</a>";
	        if ($idx === $len - 2) $html .= " & ";
	        else if ($idx < $len -1) $html .= ", ";
	    }
	    $html .= "</div>";
	}
	if( $tags ) {
		$html .= "<div class='tags'>";
		$len = count($tags);
	    foreach( $tags as $idx => $t) {
	    	$name = $t->name;
	    	$link = get_term_link($t);
	    	$html .= "<a href='{$link}'>{$name}</a>";
	    }
	    $html .= "</div>";
	}	
	$html .= "</footer>"; //END footer
	$html .= "</div>"; //END card
	if( $show_thumb ) $html .= "<aside class='card-wrap-sidebar'><a href='the_permalink()'>{$thumb}</a> </aside>";
	
	$html .= "</article>";

	return $html;
}

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
	$posts = get_field('presenters__facilitators_relation');

	$output = '';
	if( $posts ) {
	    foreach( $posts as $p) {
	    	$name = $p->post_title;
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
	$materials = get_field('materials');
	if( $materials ) {
		$urls = explode(";", $materials);	
	    foreach( $urls as $link) {
			$output .= " [<a href='{$link}'>{$link}</a>] ";
		}
	}
	return $output;
}

//[pd_sponsors]
add_shortcode('pd_sponsors', 'pd_sponsors_func');
function pd_sponsors_func($atts, $content = null) {
	global $post;
	$post = get_post($post->ID);
	setup_postdata($post);
	$output = "";
	$sponsors = get_field('sponsor');
	
	if( $sponsors ) {
		$len = count($sponsors);
	    foreach( $sponsors as $idx => $p) {
			$link = get_permalink($p->ID);;
			$title = $p->post_title;
			$output .= "<a href='{$link}'>{$title}</a>";
			if ($idx === $len - 2) $output .= " & ";
            else if ($idx < $len -1) $output .= ", ";   	

		}
	}
	return $output;
}

//[pd_title]
add_shortcode( 'pd_title', 'get_the_title' );

//[pd_time_meta]
add_shortcode( 'pd_time_meta', 'pd_time_meta_func' );
function pd_time_meta_func($atts, $content = null) {
	global $post;
	$pub = get_the_date();
	$mod = get_the_modified_date();
	$author = get_the_author();
	$output = "<div class='exp-publish-meta'>Published {$pub} by {$author} &bull; Updated {$mod}</div>";
	return $output;
}

//[pd_access_url]
add_shortcode('pd_access_url', 'pd_access_url_func');
function pd_access_url_func($atts, $content = null) {
	global $post;
	setup_postdata($post);
	$url = get_field('url_website');
	$output = "<a href='{$url}' target='_blank'> {$url} </a>";
	return $output;
}

//[pd_tags]
add_shortcode('pd_tags', 'pd_tags_func');
function pd_tags_func($atts, $content = null) {
	global $post;
	$post = get_post($post->ID);
	// setup_postdata($post);
	$output = "<div class='tags'>";	
	$terms = get_the_terms(get_the_ID(), 'experience_tags','', '');
	if( $terms ) {
	    foreach( $terms as $p) {
	    	$name = $p->name;
	    	$link = get_term_link($p);
	    	$output .= "<a href='{$link}'>{$name}</a>";
	    }
	}
	$output .= "</div>";
	return $output;
}

//[pd_series_tags]
add_shortcode('pd_series_tags', 'pd_tags_series_func');
function pd_tags_series_func($atts, $content = null) {
	global $post;
	$post = get_post($post->ID);
	// setup_postdata($post);
	$output = "<div>";	
	$terms = get_the_terms(get_the_ID(), 'series','', '');
	if( $terms ) {
		$len = count($terms);
	    foreach( $terms as $idx => $p) {
	    	$name = $p->name;
	    	$link = get_term_link($p);
	    	$output .= "<a href='{$link}'>{$name}</a>";
	    	if ($idx === $len - 2) $output .= " & ";
            else if ($idx < $len -1) $output .= ", ";   	
	    }
	}
	$output .= "</div>";
	return $output;
}

// [pd_person_exp_relation]
add_shortcode('pd_person_exp_relation', 'pd_person_exp_relation_func');
function pd_person_exp_relation_func($atts, $content = null) {
	global $post;
	$args = array(
	    'numberposts'  	=> -1,
	    'post_type'		=> 'experience',
	    'posts_per_page'=> -1,
	    'meta_query'   	=> array( 
	    	array(
	    		'key'	 =>'presenters__facilitators_relation',
	    		'value'	 =>'"'. $post->ID .'"', 
	    		'compare'=>'LIKE',
	    	)
	    ),
	);
	$related_exps = get_posts($args);
	$output = "";
	if ( $related_exps ) {	    
	    foreach ( $related_exps as $p ) {
	    	$output .= pd_exp_html_blk_sm($p, true, false);
		}
	}

	return $output;
}

//[pd_exps]
add_shortcode('pd_exps', 'pd_exps_func');
function pd_exps_func($atts, $content = null) {
	global $post;
	$output = "";
	while( have_posts()) {
		the_post();
		$img = the_post_thumbnail();
		$title = $post->post_title;
		$presenters = get_field('presenters__authors_relation');
		$blurb = get_field('resource_description');
		$trim = wp_trim_words($blurb, 20, ' ...');
		$tags = the_terms( get_the_ID(), 'experience_tags', '', ', ');

		$output .= '<article class="card-wrap-row">';
		$output .= "<div class='image'><img src='{$img}'></div><div class='card'>";
		$output .= "<header class='card-header'>";
		$output .= "<h1 class='card-title'>{$title}</h1>";
		

		if( $presenters ) {
			$output .= "<p class='card-meta'>";
		    foreach( $presenters as $p) {
		    	$name = get_field('full_name', $p->ID);
		    	$link = get_permalink($p->ID);
		    	$output .= "<span><a href='{$link}'>{$name}</a> </span>";
		    }
		    $output .= "<\p>";
		}	
		
		$output .= "<p class='card-body'>{$trim}</p>";
		$output .= "<footer class='card-footer'>";
		if( $tags ) {
		    foreach( $tags as $p) {
		    	$name = $p->name;
		    	$link = get_term_link($p);
		    	$output .= "<span><a href='{$link}' class=''>{$name}</a><span>";
		    }
		}
		$output .= "</footer>";
		$output .="</article>";
	}
	return $output;
}

