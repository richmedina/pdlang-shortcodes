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
	// $file = get_field('materials');
	// if( $file ) {
 //    	$url = wp_get_attachment_url( $file );
 //    	var_dump($file);
	// }
 //    <a href="php echo esc_html($url);" >Download File</a>

	$materials = get_field('materials');
	if( $materials ) {
	    foreach( $materials as $p) {
			$link = $p->supplementary_material_url;
			$title = $p->post_title;
			$output .= "<a href='{$link}'>{$title}</a>";
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
			$output .= "<a href='{$link}' target='_blank'>{$title}</a>";
			if ($idx === $len - 2) $output .= " & ";
            else if ($idx < $len -1) $output .= ", ";   	

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

//[pd_related_exps]
// add_shortcode('pd_related_exps', 'pd_related_exps_func');
// function pd_related_exps_func($atts, $content = null) {
// 	global $post;
// 	var_dump($post->title);
// 	$args = array(
// 	    // 'numberposts'   	=> -1,
// 	    'post_type'      	=> array( 'experience'),
// 	    'meta_query'     	=> array('key'=>$post,'in'=>'=','value'=>'presenters__facilitators_relation'),
// 	    'meta_key'       	=> $post,
// 	    // 'orderby'			=> 'meta_value_num',
// 	    // 'order'   			=> 'ASC',
// 	    // 'posts_per_page' 	=> 70,

// 	);
// 	$posts = new WP_Query($args);

// 	if ( $posts->have_posts() ) {
// 	    $output = "hello";
// 	    while ( $posts->have_posts() ) {}
// 	}
// 	return $output;
// }

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

// <article class="card-wrap-row">
// <div class="image"><img src="1.jpg"></div>
// <div class="card">
//   <header class="card-header">
//     <h1 class="card-title">Python Essentials for Studies in human language and technology</h1>
//     <p class="card-meta">
//       <span>Bob Dylan</span>
//       <span>March 31, 2020</span>
//       <span>Info</span>
//     </p>
//   </header>
//   <p class="card-body">
//     Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut labore et dolore magna aliqua.
//   </p>
//   <footer class="card-footer"><span>tag</span> <span>tag</span> <span>tag</span></footer>
// </div>
// </article>
