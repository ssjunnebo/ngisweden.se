<?php

// Shortcodes for content to go on the homepage

function ngisweden_method_tabs($atts_raw){

  // Shortcode attribute defaults
  $atts = shortcode_atts( array(
    'type' => 'applications'
  ), $atts_raw);

  $output = '<div class="ngisweden-method-tabs tax-'.$atts['type'].'">';

  // Is this a taxonomy or a custom post type?
  $is_cpt = false;
  $all_custom_post_types = get_post_types( array ( '_builtin' => FALSE ) );
  if (!empty($all_custom_post_types) && in_array($atts['type'], $all_custom_post_types )){
    $is_cpt = true;
  }
  $is_tax = false;
  $all_custom_taxonomies = get_taxonomies( array ( '_builtin' => FALSE ) );
  if (!empty($all_custom_post_types) && in_array($atts['type'], $all_custom_taxonomies )){
    $is_tax = true;
  }



  $buttons = [];
  if($is_cpt){

    // Get only top-level posts
    $posts = get_posts( array(
      'post_type' => $atts['type'],
      'post_parent' => 0,
      'posts_per_page' => -1
    ) );

    foreach($posts as $post){

      $default_icon = $atts['type'] == 'bioinformatics' ? 'laptop-code.svg' : 'tools.svg';

      // Simple vars / defaults
      $button = [
        'name' => $post->post_title,
        'description' => trim(strip_tags($post->post_excerpt)),
        'link' => get_post_permalink($post),
        'icon' => get_stylesheet_directory().'/includes/icons/fontawesome-svgs/solid/'.$default_icon,
      ];

      // Get the icon
//      $post_type = get_post_type_object(get_post_type($post));
//      get_post_meta($post->ID, '_technologies', true);
//      $icon_meta_key = $atts['type'].'_icon';
//      $term_meta = get_option( $post_type->labels->singular_name."_page_".$post->ID );
//      if(isset($term_meta[$icon_meta_key])){
//        $a_icon = get_stylesheet_directory().'/'.$term_meta[$icon_meta_key];
//        if(file_exists($a_icon) && is_file($a_icon)){
//          $button['icon'] = $a_icon;
//        }
//      }

      $buttons[] = $button;

    }

  } else if($is_tax){

    // Get only top-level terms
    $terms = get_terms( array(
      'taxonomy' => $atts['type'],
      'hide_empty' => false,
      'parent' => 0,
      'posts_per_page' => -1
    ) );

    foreach($terms as $term){

      // Simple vars / defaults
      $button = [
        'name' => $term->name,
        'description' => trim(strip_tags(term_description($term->term_id, $atts['type']))),
        'link' => get_term_link($term->slug, $atts['type']),
        'icon' => get_stylesheet_directory().'/includes/icons/fontawesome-svgs/solid/flask.svg',
      ];

      // Get the icon
      $tax_labels = get_taxonomy_labels(get_taxonomy($atts['type']));
      $singular_slug = strtolower($tax_labels->singular_name);
      $icon_meta_key = $singular_slug.'_icon';
      $term_meta = get_option( $singular_slug."_page_".$term->term_id );
      if(isset($term_meta[$icon_meta_key])){
        $a_icon = get_stylesheet_directory().'/'.$term_meta[$icon_meta_key];
        if(file_exists($a_icon) && is_file($a_icon)){
          $button['icon'] = $a_icon;
        }
      }

      $buttons[] = $button;

    }
  }

  // Render the buttons
  foreach($buttons as $button){
    $tooltip = '';
    if($button['description'] && strlen($button['description'])){
      $tooltip = 'data-toggle="tooltip" data-delay=\'{ "show": 1000, "hide": 0 }\' title="'.$button['description'].'"';
    }
    $output .= '<div class="ngisweden-method-tab">
      <a href="'.$button['link'].'" class="app-link" '.$tooltip.'>
        <span class="application-icon">'.file_get_contents($button['icon']).'</span>
        '.$button['name'].'
      </a>
    </div>';
  }

  $output .= '</div>'; // .ngisweden-method-tabs
  return $output;
}
add_shortcode('ngisweden_tabs', 'ngisweden_method_tabs');
