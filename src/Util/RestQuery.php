<?php

namespace WPDev\Util;

use WPDev\Theme;

class RestQuery
{
  static function register(string $route, $options = [])
  {
    \add_filter(Theme::REST_NAMESPACE . '/' . $route, function ($params) use ($options) {
      return self::query($params, $options);
    });

    \add_action('rest_api_init', function () use ($route) {
      \register_rest_route(Theme::REST_NAMESPACE, $route, [
        'method' => 'GET',
        'callback' => function ($data) use ($route) {
          $params = [];
          foreach ($data->get_params() as $key => $value)
            $params[$key] = urldecode($value);
          return self::run_registered_query($route, $params);
        },
        'permission_callback' => function () {
          return true;
        },
      ]);
    });
  }

  static function run_registered_query($route, $params)
  {
    return \apply_filters(Theme::REST_NAMESPACE . '/' . $route, $params);
  }

  private static function query(array $params, array $options)
  {
    @[
      'post-type' => $post_type,
      'serialize-post-callback' => $serialize_post_callback,
      'posts-per-page' => $posts_per_page,
      'fields-included-in-search' => $fields_included_in_search,
    ] = $options;

    if (!$posts_per_page) @$posts_per_page = $params['posts-per-page'];
    if (!$posts_per_page) $posts_per_page = 10;

    @$page = $params['page'];
    @$search_term = $params['search'];
    @$term_slug = $params['term-slug'];

    if ($search_term && $fields_included_in_search) self::include_fields_in_search($fields_included_in_search, $search_term);

    $query_params = [
      'post_type' => $post_type,
      'posts_per_page' => $posts_per_page,
      'offset' => $page ? $page * $posts_per_page + 1 : 0,
      's' => $search_term,
      'category_name' => @$params['category'],
      'tax_query' => $term_slug ? [[
        'taxonomy' => 'post_tag',
        'field' => 'slug',
        'terms' => $term_slug,
      ]] : null,
    ];
    if (empty($query_params['s'])) {
      $query_params['orderby'] = 'date';
      $query_params['order'] = 'DESC';
    }

    $query = new \WP_Query($query_params);
    $items = [];
    foreach ($query->posts as $post)
      $items[] = $serialize_post_callback
        ? $serialize_post_callback($post)
        : self::serialize_post($post);

    return $items;
  }

  private static function include_fields_in_search(array $fields, string $search_term)
  {
    \add_filter('posts_join', function ($join) {
      global $wpdb;

      $join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";

      return $join;
    });

    \add_filter('posts_groupby', function ($groupby) {
      global $wpdb;

      $groupby = "$wpdb->posts.ID";

      return $groupby;
    });

    \add_filter('posts_search', function ($search_sql) use ($fields, $search_term) {
      global $wpdb;

      $old_or = "OR ({$wpdb->posts}.post_content LIKE '{$wpdb->placeholder_escape()}{$search_term}{$wpdb->placeholder_escape()}')";
      $new_or = $old_or . " OR ({$wpdb->postmeta}.meta_value LIKE '{$wpdb->placeholder_escape()}{$search_term}{$wpdb->placeholder_escape()}' AND {$wpdb->postmeta}.meta_key IN (" . implode(
        ', ',
        array_map(function ($field) {
          return "'$field'";
        }, $fields)
      ) . "))";
      $search_sql = str_replace($old_or, $new_or, $search_sql);

      $search_sql = str_replace(" ORDER BY ", " GROUP BY $wpdb->posts.ID ORDER BY ", $search_sql);

      return $search_sql;
    });
  }

  private static function serialize_post(\WP_Post $post)
  {
    return [
      'title' => $post->post_title,
      'url' => \get_permalink($post),
      'date' => (new \DateTime($post->post_date))->format('d/m/Y'),
      'description' => $post->post_excerpt,
    ];
  }
}
