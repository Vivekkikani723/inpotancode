<?php

/**
 * Class
 * Custom Post Filter
 */

class CustomPostFilter
{

    /**
     * Retrieve a filtered list of available public post types.
     *
     * This function gathers a list of all public post types and then applies
     * an exclusion filter obtained from the excludePostTypes() method.
     *
     * @return array An array of public post types after applying exclusions.
     */
    public function getFilteredPublicPostTypes(): array
    {
        // Retrieve all public post types
        $allPublicPostTypes = get_post_types(array('public' => true));

        // Get post types to allow
        $allowedPostTypes = $this->getPostTypesToAllow();
        
        // Filter the post types array to include only the allowed post types
        $filteredPostTypes = array_filter($allPublicPostTypes, function ($post_type) use ($allowedPostTypes) {
            return in_array($post_type, $allowedPostTypes);
        });

        return $filteredPostTypes;
    }


    /**
     * Retrieve post types to display in a filtered list.
     *
     * This method provides an array of post types that should be allowed
     * from the final list of available public post types.
     *
     * @return array An array of post types to be displayed.
     */
    public function getPostTypesToAllow(): array
    {
        return [
            'post',
            'case',
            'video',
            'white_paper'
        ];
    }

    /**
     * Get Used Terms by Taxonomy.
     *
     * Retrieves terms associated with posts in the specified taxonomy.
     *
     * @param string $taxonomy The taxonomy name.
     * @return array Array of used terms.
     */
    public function getUsedTermsByTaxonomy(string $taxonomy): array
    {
        $terms = get_terms($taxonomy, array('hide_empty' => false));

        if (is_array($terms) && !is_wp_error($terms)) {
            return $terms;
        }

        // Handle the error or return an empty array
        return array();
    }

    /**
     * Retrieve filtered post and category data with taxonomy.
     *
     * This function gathers necessary data for filtering custom post types and categories
     * based on URL parameters. It retrieves custom post types, associated categories,
     * selected post types, and selected categories.
     *
     * @return array An array containing custom post types, post categories, selected post types,
     *               and selected categories.
     */
    public function getPostWithTaxonomy(): array
    {
        // Get custom post types
        $customPostTypes = $this->getFilteredPublicPostTypes();

        // Get post type-taxonomy associations
        $postTypeTaxonomies = $this->getPostWiseTaxonomies();

        $postCategories = [];

        if (!empty($postTypeTaxonomies)) {
            foreach ($postTypeTaxonomies as $key => $postTaxonomy) {
                // Get categories for the post type's taxonomy
                $categories = $this->getUsedTermsByTaxonomy($postTaxonomy);

                if (!empty($categories)) {
                    foreach ($categories as $category) {
                        $postCategories[$category->slug] = $category;
                    }
                }
            }
        }

        return array($customPostTypes, $postCategories);
    }

    /**
     * Retrieve selected post types and categories data from the URL.
     *
     * This function retrieves data for selected post types and categories based on URL parameters.
     * It extracts custom post types and associated categories, filtering them according to URL parameters.
     *
     * @return array An array containing selected post types and selected categories.
     */
    public function getDataFromURL(): array
    {
        // Get Search 
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

        // Get selected post types from URL parameter
        $postTypesParam = isset($_GET['posttype']) ? sanitize_text_field($_GET['posttype']) : '';
        $selectedPostTypes = !empty($postTypesParam) ? explode(',', $postTypesParam) : [];
        
        // Get selected categories from URL parameter
        $categoryParam = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
        $selectedCategories = !empty($categoryParam) ? explode(',', $categoryParam) : [];

        return array($selectedPostTypes, $selectedCategories, $search);
    }

    /**
     * Retrieve taxonomy slugs associated with a specified post type.
     *
     * This method queries and returns an array of taxonomy slugs that are linked to
     * the provided post type slug. It employs the WordPress function get_object_taxonomies().
     * If no taxonomies are found for the given post type, it returns false.
     *
     * @param string $post_type_slug The slug of the post type.
     * @return array|false An array of string taxonomy slugs associated with the post type,
     *                     or false if no taxonomies are found.
     */
    public function getTaxonomySlugsByPostType(string $post_type_slug): array|false
    {
        $taxonomies = get_object_taxonomies($post_type_slug, 'objects');

        if (!empty($taxonomies)) {
            $taxonomy_slugs = array_keys($taxonomies);
            return $taxonomy_slugs;
        }

        return false; // No taxonomies found for the given post type
    }

    /**
     * Get Taxonomies Associated with Each Post Type.
     *
     * This function retrieves taxonomies linked to specific post types, forming an array where
     * keys are post type slugs and values are their associated taxonomy slugs.
     * For the 'post' post type, the taxonomy is set to 'category' by default.
     *
     * @return array An array containing post type slugs as keys and associated taxonomy slugs as values.
     */
    public function getPostWiseTaxonomies()
    {
        $all_post_types = $this->getFilteredPublicPostTypes();
        $postType_taxonomy = [];

        if (!empty($all_post_types)) {
            foreach ($all_post_types as $singlePostType) {

                if ($singlePostType == 'post') {
                    $postType_taxonomy[$singlePostType] = 'category';
                    continue;
                }

                $postType_taxonomy[$singlePostType] = isset($this->getTaxonomySlugsByPostType($singlePostType)[0]) ? $this->getTaxonomySlugsByPostType($singlePostType)[0] : '';
            }
        }

        return $postType_taxonomy;
    }

    /**
     * Get Posts Data.
     *
     * This function retrieves posts data based on the filtered public post types.
     *
     * @return WP_Query An instance of WP_Query containing the retrieved posts.
     */
    public function getPostsData(): WP_Query
    {
        $getposttypeparam = isset($_GET['posttype']) ? sanitize_text_field($_GET['posttype']) : '';

        $getPosts = $getposttypeparam ? $getposttypeparam : $this->getFilteredPublicPostTypes();

        $args = [
            'post_type' => $getPosts,
            'posts_per_page' => 9,
            'post_status' => 'publish',
            'orderby' => 'publish_date',
            'order' => 'desc'
        ];

        // Create a new WP_Query instance with the specified arguments
        $query = new WP_Query($args);
        return $query;
    }

    /**
     * get Taxonomy based on selected post types
     */
    public function getTaxonomyBySelectedPostType(array $postTypes, array $postTerms)
    {
        $isTermFilterd = false;

        list($customPostTypes, $postCategories) = $this->getPostWithTaxonomy();

        if (empty($postTypes)) {
            $postTypes = $customPostTypes;
        }

        $postTaxonomy = $this->getPostWiseTaxonomies();



        if (empty($postTerms)) {
            $postTerms = $postCategories;
        } else {
            $isTermFilterd = true;
        }

        $args = [];
        foreach ($postTypes as $postType) {
            $args['relation'] = 'OR';

            foreach ($postTerms as $key => $postTerm) {
                $term = $isTermFilterd ? $postTerm : $key;
                $args[] = [
                    'taxonomy' => $postTaxonomy[$postType], // taxonomy name
                    'field'    => 'slug',
                    'terms'    => $term,
                ];
            }
        }

        return $args;
    }
}
