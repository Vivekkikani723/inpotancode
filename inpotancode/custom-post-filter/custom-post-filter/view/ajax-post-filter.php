<?php

/**
 * ===============================
 *  Ajax code for filtering posts
 * ===============================
 */

add_action('wp_ajax_filter_post_by_ajax', 'filter_post_by_ajax');
add_action('wp_ajax_nopriv_filter_post_by_ajax', 'filter_post_by_ajax'); // For non-logged-in user
function filter_post_by_ajax()
{
    $paged = isset($_POST['paged']) ? $_POST['paged'] : 1;

    $filterClass = new CustomPostFilter;

    $getPosts = $filterClass->getFilteredPublicPostTypes();

    $search = $_POST['search'];
    $selectedPostType = isset($_POST['selectedPostType']) && !empty($_POST['selectedPostType']) ? $_POST['selectedPostType'] : $getPosts;
    $selectedPostTaxonomy = isset($_POST['selectedPostTaxonomy']) ? $_POST['selectedPostTaxonomy'] : [];

    $args = [
        'paged' => $paged,
        'post_type' => $selectedPostType,
        'posts_per_page' => 9,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'desc',
    ];

    if ( isset( $search ) &&  $search != '' ) {
        $args['s'] = sanitize_text_field( $search );
    }
    
    $args['tax_query'] = $filterClass->getTaxonomyBySelectedPostType($selectedPostType, $selectedPostTaxonomy);

    // pr($args,1);
    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) : ?>
        <?php
        while ($query->have_posts()) : $query->the_post();

            $post_type = get_post_type(); // Get post type
            $post_type_name = get_post_type_object($post_type)->labels->name == "Posts" ? 'Blogs' : get_post_type_object($post_type)->labels->name;

            if ($post_type == 'post') {

                $get_taxonomy = 'category';
            } else {

                $get_taxonomy = $filterClass->getTaxonomySlugsByPostType($post_type);
                $get_taxonomy = !empty($get_taxonomy) ? $get_taxonomy[0] : '';
            }

            $categories = get_the_terms(get_the_ID(), $get_taxonomy); // Get post categories

            $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); // Get post featured image
            $post_title = get_the_title(); // Get post title
            $post_content = get_the_content(); // Get post content
            $post_link = get_the_permalink(); // Get post link
        ?>
            <div class="post-box">
                <div>
                    <a href="<?php echo $post_link; ?>"><img src="<?php echo $featured_image[0]; ?>"></a>
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
                <p class="des"><?php echo  wp_trim_words($post_content, 30, '...'); ?></p>
            </div>
        <?php endwhile;
    else :
        echo '<p>No posts found.</p>';
    endif;

    $html = ob_get_clean();
    // <!-- Pagination -->
    ob_start();
    if ($query->max_num_pages > 1) : ?>
        <?php
        echo paginate_links(array(
            'total' => $query->max_num_pages,
            'current' => $paged
        ));
        ?>
<?php endif;
    $pagination = ob_get_clean();
    wp_send_json(['html' => $html, 'pagination' => $pagination]);
}
