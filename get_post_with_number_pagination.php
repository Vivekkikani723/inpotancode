<?php
if (!function_exists('custom_trim_content')) {
  function custom_trim_content($content, $limit, $append = '...')
  {
    // Remove HTML tags from the content
    $content = strip_tags($content);

    // Trim leading and trailing spaces
    $content = trim($content);

    // Check if the content length exceeds the limit
    if (strlen($content) > $limit) {
      // Trim the content to the specified character limit
      $content = substr($content, 0, $limit);

      // Trim any trailing spaces after cutting off
      $content = rtrim($content);

      // Append the ellipsis or any other text
      $content .= $append;
    }

    // Return the trimmed content
    return $content;
  }
}


add_shortcode('resources_and_posts', 'resource_and_posts_display_shortcode');
if (!function_exists('resource_and_posts_display_shortcode')) {

  function resource_and_posts_display_shortcode()
  {
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    $args = [
      "post_type" => [
        'post',
        'resource'
      ],
      "post_status" => "publish",
      "posts_per_page" => 10,
      "orderby" => 'date',
      "order" => 'DESC',
      "paged" => $paged
    ];

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) : ?>
      <div class="post-resource-box">
        <?php while ($query->have_posts()) : $query->the_post();
          $featuredImage = get_the_post_thumbnail_url(get_the_ID(), 'full') !== false ? get_the_post_thumbnail_url(get_the_ID(), 'full') : wp_get_attachment_image_url(2790);
          $content = custom_trim_content(get_the_content(), 300);
        ?>
          <div class="post-resource-thumb">
            <a href="<?php the_permalink(); ?>">
              <img src="<?php echo $featuredImage; ?>">
            </a>
          </div>
          <div class="post-resource-disc">
            <div class="post-resource-title">
              <h3>
                <a href="">
                  <?php the_title(); ?>
                </a>
              </h3>
            </div>
            <div class="ost-resource-content">
              <?php echo $content; ?>
            </div>
            <div class="post-read-more">
              <a class="btn" href="<?php the_permalink(); ?>">
                <span>Read More</span>
              </a>
            </div>
          </div>
        <?php endwhile;
        // pagination 
        $pagination = paginate_links(array(
          'total'   => $query->max_num_pages,
          'current' => max(1, get_query_var('paged')),
          'format'  => '?paged=%#%',
          'prev_text' => __('&laquo; Previous', 'textdomain'),
          'next_text' => __('Next &raquo;', 'textdomain'),
        ));
        ?>
      </div>
      <div class="pagination clearfix">
        <?php echo $pagination; ?>
      </div>
    <?php wp_reset_postdata();
    else : ?>
      <div>No posts found.</div>
    <?php endif;
    return ob_get_clean();
  }
}

add_shortcode('resource_page_sidebar', 'resources_page_sidebar_shortcode');
if (!function_exists('resources_page_sidebar_shortcode')) {
  function resources_page_sidebar_shortcode()
  {
    $taxonomies_array = ['resources-category', 'category'];
    $terms = get_terms(array(
      'taxonomy'   => $taxonomies_array,
      'hide_empty' => false,
    ));

    ob_start();
    $resource_term_html = '';
    $post_term_html = '';
    if (!empty($terms) && !is_wp_error($terms)) {
      foreach ($terms as $term) {
        $term_link = get_term_link($term->term_id);
        $term_html = <<<HTML
					<li>
						<a href="$term_link">$term->name</a>
					</li>
				HTML;

        if ($term->taxonomy == "resources-category") {
          $resource_term_html .= $term_html;
        } else {
          $post_term_html .= $term_html;
        }
      } ?>

      <div class="post-resource-sidebar">
        <div class="links-box">
          <h2>Resource Categories</h2>
          <ul>
            <?php echo $resource_term_html; ?>
          </ul>
        </div>
        <div class="links-box">
          <h2>Post Categories</h2>
          <ul>
            <?php echo $post_term_html; ?>
          </ul>
        </div>
      </div>
<?php }

    return ob_get_clean();
  }
}
