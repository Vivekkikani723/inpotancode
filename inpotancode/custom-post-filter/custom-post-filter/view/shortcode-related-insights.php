<?php

/**
 * ======================================
 * Shortcode to display related insight
 * Shortcode : [related_insights]
 * ======================================
 */

if (!function_exists('related_insights_shortcode')) {

    function related_insights_shortcode()
    {
        $related_insights = new CustomPostFilter();

        $posttypes =  $related_insights->getFilteredPublicPostTypes();

        // Get the current post id
        $post_id = get_the_ID();

        ob_start();
        $args = array(
            'post_type' =>  $posttypes,
            'post_status' => 'publish',
            'posts_per_page' => 3,
            'orderby' => 'date',
            'order' => 'DESC',
            'post__not_in' => array($post_id) // Exclude the current post
        );

        $related_posts = new WP_Query($args);

?>
        <div class="related-insights">
            <?php if ($related_posts->have_posts()) : ?>
                <div class="filter-post">
                    <div class="posts">
                        <?php
                        while ($related_posts->have_posts()) :  $related_posts->the_post();

                            $post_type = get_post_type(); // Get post type
                            $post_type_name = get_post_type_object($post_type)->labels->name == "Posts" ? 'Blogs' : get_post_type_object($post_type)->labels->name;

                            $posttype_taxonomy = $related_insights->getTaxonomySlugsByPostType($post_type);
                            if (is_array($posttype_taxonomy) && !empty($posttype_taxonomy)) {
                                $posttype_taxonomy = $posttype_taxonomy[0];
                            }

                            $categories = get_the_terms(get_the_ID(), $posttype_taxonomy); // Get post categories

                            $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); // Get post featured image
                            $post_title = get_the_title(); // Get post title
                            $post_content = get_the_excerpt(); // Get post content
                            $post_link = get_the_permalink(); // Get post link
                        ?>
                            <div class="post-box">
                                <div>
                                    <a href="<?php echo $post_link; ?>"> <img src="<?php echo $featured_image[0]; ?>"></a>
                                </div>
                                <div>
                                    <div class="blog-type"><?php echo $post_type_name; ?></div>
                                    <?php if (!empty($categories)) : ?>
                                        <div class="blog-cat">
                                            <?php
                                            $category_names = array();
                                            foreach ($categories as $category) {
                                                $category_names[] = $category->name;
                                            }
                                            echo implode(', ', $category_names);
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php echo $post_link; ?>">
                                    <h3 class="heading-post"><?php echo $post_title; ?></h3>
                                </a>
                                <p class="des"><?php echo $post_content; ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
<?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}

add_shortcode('related_insights', 'related_insights_shortcode');
