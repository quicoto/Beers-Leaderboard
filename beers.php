<?php

/*
Plugin Name: Beers
Plugin URI: http://php.quicoto.com
 * Description: This adds the a beer post type and shortcode for leaderboards.
 * Version: 1.0
 * Author: quicoto
 * Author URI: http://php.quicoto.com
*/

// Register Custom Post Type
function beer_post_type() {

	$labels = array(
		'name'                  => _x( 'Beers', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Beer', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Beers', 'text_domain' ),
		'name_admin_bar'        => __( 'Beer', 'text_domain' ),
		'archives'              => __( 'Beer Archives', 'text_domain' ),
		'attributes'            => __( 'Beer Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Beer:', 'text_domain' ),
		'all_items'             => __( 'All Beers', 'text_domain' ),
		'add_new_item'          => __( 'Add New Beer', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Beer', 'text_domain' ),
		'edit_item'             => __( 'Edit Beer', 'text_domain' ),
		'update_item'           => __( 'Update Beer', 'text_domain' ),
		'view_item'             => __( 'View Beer', 'text_domain' ),
		'view_items'            => __( 'View Beers', 'text_domain' ),
		'search_items'          => __( 'Search Beer', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into Beer', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Beer', 'text_domain' ),
		'items_list'            => __( 'Beers list', 'text_domain' ),
		'items_list_navigation' => __( 'Beers list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter Beers list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Beer', 'text_domain' ),
		'description'           => __( 'Untapped Beer scores', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'revisions', 'custom-fields' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-star-filled',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'beer', $args );

}
add_action( 'init', 'beer_post_type', 0 );

/*
AVAILABLE META FIELDS
From: https://github.com/quicoto/Untappd-WordPress-Import/blob/master/beer_import.php

  - beer_style
  - bid
  - count (times drinked)
  - rating_score
  - recent_created_at
*/

// Add Shortcode
function beers_shortcode() {
  /*
    Last Drinked
  */
  $args = array(
    'posts_per_page' => 5,
    'order' => 'DESC',
    'orderby'   => 'modified',
    'post_type' => 'beer'
  );

  $beers = get_posts( $args );
  echo "<h2>Last drinked</h2>";
  echo "<ul>";
    foreach ( $beers as $post ) : setup_postdata( $post );
      echo '<li>';
        echo '<a href="' . get_the_permalink($post). '">' . get_the_title($post) . '</a>';

        $score = get_post_meta( $post->ID, 'rating_score', true );
        if (!$score) {
          $score = "Unknown";
        }

        $count = get_post_meta( $post->ID, 'count', true );
        if (!$count) {
          $count = "Unknown";
        }

        echo " (Score: " . $score . ", Drinked: " . $count . ")";
      echo '</li>';
    endforeach;
  echo "</ul>";
  wp_reset_postdata();


  /*
    Most Drinked
  */
  $args = array(
    'posts_per_page' => 5,
    'order' => 'DESC',
    'orderby'   => 'meta_value_num',
	  'meta_key'  => 'count',
    'post_type' => 'beer'
  );

  $beers = get_posts( $args );

  echo "<h2>Most Drinked</h2>";
  echo "<ul>";
    foreach ( $beers as $post ) : setup_postdata( $post );
      echo '<li>';
        echo '<a href="' . get_the_permalink($post). '">' . get_the_title($post) . '</a>';

        $score = get_post_meta( $post->ID, 'rating_score', true );
        if (!$score) {
          $score = "Unknown";
        }

        $count = get_post_meta( $post->ID, 'count', true );
        if (!$count) {
          $count = "Unknown";
        }

        echo " (Score: " . $score . ", Drinked: " . $count . ")";
      echo '</li>';
    endforeach;
  echo "</ul>";
  wp_reset_postdata();

  /*
    Best Rated
  */
  $args = array(
    'posts_per_page' => 5,
    'meta_query' => array(
        'relation' => 'AND',
        'score_clause' => array(
            'key' => 'rating_score',
            'type'    => 'NUMERIC',
            'compare' => 'EXISTS',
        ),
        'count_clause' => array(
            'key' => 'count',
            'type'    => 'NUMERIC',
            'compare' => 'EXISTS',
        )
    ),
    'orderby' => array(
      'score_clause' => 'DESC',
      'count_clause' => 'DESC',
    ),
    'post_type' => 'beer'
  );

  $beers = get_posts( $args );

  echo "<h2>Best Rated</h2>";
  echo "<ul>";
    foreach ( $beers as $post ) : setup_postdata( $post );
      echo '<li>';
        echo '<a href="' . get_the_permalink($post). '">' . get_the_title($post) . '</a>';

        $score = get_post_meta( $post->ID, 'rating_score', true );
        if (!$score) {
          $score = "Unknown";
        }

        $count = get_post_meta( $post->ID, 'count', true );
        if (!$count) {
          $count = "Unknown";
        }

        echo " (Score: " . $score . ", Drinked: " . $count . ")";
      echo '</li>';
    endforeach;
  echo "</ul>";
  wp_reset_postdata();

  /*
    All Beers
  */
  $args = array(
    'posts_per_page' => -1,
    'order' => 'ASC',
    'orderby'   => 'title',
    'post_type' => 'beer'
  );

  $beers = get_posts( $args );

  echo "<h2>All ever tasted</h2>";
  echo "<ul>";
    foreach ( $beers as $post ) : setup_postdata( $post );
      echo '<li>';
        echo '<a href="' . get_the_permalink($post). '">' . get_the_title($post) . '</a>';

        $score = get_post_meta( $post->ID, 'rating_score', true );
        if (!$score) {
          $score = "Unknown";
        }

        $count = get_post_meta( $post->ID, 'count', true );
        if (!$count) {
          $count = "Unknown";
        }

        echo " (Score: " . $score . ", Drinked: " . $count . ")";
      echo '</li>';
    endforeach;
  echo "</ul>";
  wp_reset_postdata();

}
add_shortcode( 'beers', 'beers_shortcode' );