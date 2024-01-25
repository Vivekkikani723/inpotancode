<?php

/**
 * ============================================
 * Shortcode for post filtering functionality
 * Shortcode : [post_filter]
 * ============================================
 */
add_shortcode('post_filter', 'post_filter_form_shortcode');

if (!function_exists('post_filter_form_shortcode')) {
    function post_filter_form_shortcode()
    {

        $filterClass = new CustomPostFilter();

        // Get Search Params
        list($selectedPostTypes, $selectedCategories, $search) = $filterClass->getDataFromURL();

        // Get Post and Taxonomy
        list($customPostTypes, $postCategories) = $filterClass->getPostWithTaxonomy();

        // Remove "Uncategorized" category
        $postCategories = array_filter($postCategories, function ($category) {
            return $category->slug !== 'uncategorized';
        });

        $query = $filterClass->getPostsData();

        ob_start();

?>
        <div class="filter-sec">
            <div class="filter-type">
                <ul>
                    <li>
                        <form method="get" id='custom-search'>
                            <input type="hidden" name="ajax_url" value="<?php echo admin_url('admin-ajax.php'); ?>">
                            <button type="submit" class="search-icon">Search</button>
                            <input type="search" name="search" id="search-input" placeholder="Type your subject..." value="<?php echo esc_attr($search); ?>">
                            <?php
                            if (!empty($selectedPostTypes)) {
                                echo "<input type='hidden' name='posttype' value='" . implode(',', $selectedPostTypes) . "'>";
                            }

                            if (!empty($selectedCategories)) {
                                echo "<input type='hidden' name='category' value='" . implode(',', $selectedCategories) . "'>";
                            }
                            ?>
                        </form>
                    </li>
                    <li class="show-all">
                        <div>
                            <button id="show-all-button" id="lang_1" name="languages">Show all</button>
                        </div>
                    </li>

                    <?php foreach ($customPostTypes as $post_type) :
                        $posttype_lable = get_post_type_object($post_type)->labels->name == "Posts" ? 'Blogs' : get_post_type_object($post_type)->labels->name;

                        $isChecked = '';

                        if (!empty($selectedPostTypes)) {
                            if (in_array($post_type, $selectedPostTypes)) {
                                $isChecked = 'checked';
                            }
                        }
                    ?>
                        <li class="show-all-type">
                            <div>
                                <input class="input__checkbox" id="<?php echo $post_type; ?>" name="post_types" type="checkbox" value="<?php echo $post_type; ?>" <?php echo $isChecked; ?>>
                                <label for="<?php echo $post_type; ?>"><?php echo $posttype_lable; ?></label>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="filter-cat">
                <div>
                    <p>Filter By Technologie: </p>
                </div>
                <div class="filter-type-cat">
                    <ul>
                        <?php foreach ($postCategories as $category) : ?>
                            <li class="category">
                                <input class="input__checkbox" id="<?php echo $category->slug; ?>" name="categories" type="checkbox" value="<?php echo $category->slug; ?>" <?php echo in_array($category->slug, $selectedCategories) ? 'checked' : ''; ?>>
                                <label for="<?php echo $category->slug; ?>"><?php echo $category->name; ?></label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php if ($query->have_posts()) : ?>
                <div class="filter-post">
                    <div class="posts">
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
                                        <div class="blog-cat"><?php //echo $category[0]->name; 
                                                                ?>
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
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($query->max_num_pages > 1) : ?>
                        <div class="pagination">
                            <?php
                            $paged = 1;
                            echo paginate_links(array(
                                'total' => $query->max_num_pages,
                                'current' => $paged,
                            ));
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php
            else :
                echo '<p>No posts found.</p>';
            endif;
            ?>
        </div>

<?php

        wp_reset_postdata();
        return ob_get_clean();
    }
}

function pr($data, $exit = false)
{
    echo "<pre>";
    print_r($data);
    if ($exit) {
        echo "</pre>";
        exit;
    }
    echo "</pre>";
}
