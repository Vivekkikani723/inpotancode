<?php

/**
 * dispaly amenities using
 * shortcode
 * [amenities]
 */

if (!function_exists('discover_amenities')) {
    function discover_amenities()
    {
        ob_start();
        if (have_rows('property_amnities')) :
            while (have_rows('property_amnities')) : the_row();
                if (have_rows('amenity_destination')) :
                    while (have_rows('amenity_destination')) : the_row();
                        $select_amenity = get_sub_field('select_amenity');
                        $destination = get_sub_field('destination');
                        $image_url = get_term_meta($select_amenity->term_id, 'taxonomy_thumbnail_id', true);
                        $amnities_image = wp_get_attachment_url($image_url);
?>
                        <div class="amenities-list">
                            <div class="amenities-box">
                                <img src="<?php echo esc_url($amnities_image); ?>">
                                <h5><?php echo $select_amenity->name; ?></h5>
                                <p><?php echo $destination; ?></p>
                            </div>
                        </div>
            <?php
                    endwhile;
                endif;
            endwhile;
        endif;
        return ob_get_clean();
    }
}

add_shortcode('amenities', 'discover_amenities');



/**
 * Display city Filter
 * Shortcode: [filter_cities]
 */
if (!function_exists('filter_cities_shortcode_function')) {
    function filter_cities_shortcode_function()
    {
        $all_cities = get_terms([
            'taxonomy' => 'city',
            'hide_empty' => false
        ]);

        $cities_filter_html = '';
        $cities_filter_posts = '';
        if (!empty($all_cities) && !is_wp_error($all_cities)) :
            foreach ($all_cities as $city) :
                // set parent cities in filter 
                if ($city->parent === 0) {
                    $cities_filter_html .= "<li class='parent-tab' data-filter='{$city->slug}'>{$city->name}</li>";
                    continue;
                }

                $parent_term = get_term_by('term_id', $city->parent, 'city');

                $image_url = get_term_meta($city->term_id, 'taxonomy_thumbnail_id', true) ? get_term_meta($city->term_id, 'taxonomy_thumbnail_id', true) : 2209;
                $city_image = wp_get_attachment_url($image_url);
                $city_link = get_term_link($city->term_id);

                // set child cities in posts
                $cities_filter_posts .= <<<HTML
                    <a class="all-cities" href="{$city_link}" data-target="{$parent_term->slug}">
                    <div class="city-img">
                        <img src="{$city_image}" />
                    </div>
                    <div class="city-name">
                        <h3 class="city-title">{$city->name}</h3>
                    </div>
                </a>
                HTML;

            endforeach;
        endif;

        ob_start();

        if ($cities_filter_html !== '') {
            ?>
            <div class="cities-group">
                <div class="main-heading">
                    <div class="heading-info">
                        <h5 class="">Et udpluk af vores ejendomme</h5>
                        <h2>Attraktive lejemål i større danske byer</h2>
                    </div>
                    <div class="all-cities-tab">
                        <ul class="tab-filters">
                            <li class="parent-tab" data-filter="all">All</li>
                            <?php echo $cities_filter_html; ?>
                        </ul>
                    </div>
                </div>
                <div class="cities-grid">
                    <?php echo $cities_filter_posts; ?>
                </div>
            </div>
        <?php
        }
        $html = ob_get_clean();

        return $html;
    }
}

// Add the shortcode [filter_cities]
add_shortcode('filter_cities', 'filter_cities_shortcode_function');

add_action('wp_footer', function () {
    if (is_page(1748)) {
        ?>
        <script>
            ($ => {
                const filters = $('.tab-filters .parent-tab');
                $('.tab-filters .parent-tab:first-child').addClass('active');
                filters.on('click', function() {
                    filters.addClass('active').filter(this).addClass('active');
                    filters.removeClass('active').filter(this).addClass('active');
                    const dataFilter = $(this).data('filter');
                    if (dataFilter === 'all') {
                        $('.cities-grid .all-cities').show();
                        return;
                    }
                    $('.cities-grid .all-cities').hide().filter(`[data-target="${dataFilter}"]`).show();
                })
            })(jQuery);
        </script>
    <?php
    }
});


/**
 * Display FAQ TAB
 * Shortcode: [faq_tab]
 */
if (!function_exists('faq_tabs')) {
    function faq_tabs()
    {
        global $post;
        $term_list = get_the_terms($post->ID, 'categories');
        if (!$term_list || is_wp_error($term_list)) return '<p>No vaerd-at-vide found.</p>';
        ob_start();
    ?>
        <div class="post-tab">
            <div class="tab">
                <?php
                foreach ($term_list as $faq) :
                    $args = array(
                        'post_type' => 'vaerd-at-vide',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'categories',
                                'field' => 'slug',
                                'terms' => $faq->slug,
                            ),
                        ),
                    );

                    $loop = 0;
                    $posts = get_posts($args);
                    foreach ($posts as $post_data) :
                ?>
                        <div class="tablinks" data-tab="title-<?php echo $loop; ?>">
                            <div class="faq-tab">
                                <div class="d-flex">
                                    <div class="post-icon">
                                        <img src="<?php echo esc_url(get_field('icon', $post_data->ID)); ?>" alt="Icon">
                                    </div>
                                    <h3 class="post-title"><?php echo esc_html($post_data->post_title); ?></h3>
                                </div>
                                <div class="dropdown-arrow">
                                    <img src="http://anderst30.sg-host.com/wp-content/uploads/2023/12/dropdown-arrow.svg" alt="Dropdown Arrow">
                                </div>
                            </div>
                        </div>
                <?php
                        $loop++;
                    endforeach;
                endforeach;
                ?>
            </div>
            <div class="tab-details">
                <?php
                $loop = 0;
                foreach ($posts as $post_data) :
                ?>
                    <div class="faq-tab-mobile" data-tab="title-<?php echo $loop; ?>">
                        <div class="d-flex">
                            <div class="post-icon">
                                <img src="<?php echo esc_url(get_field('icon', $post_data->ID)); ?>" alt="Icon">
                            </div>
                            <h3 class="post-title"><?php echo esc_html($post_data->post_title); ?></h3>
                        </div>
                        <div class="dropdown-arrow">
                            <img src="http://anderst30.sg-host.com/wp-content/uploads/2023/12/dropdown-arrow.svg" alt="Dropdown Arrow">
                        </div>
                    </div>
                    <div class="tabcontent" id="title-<?php echo $loop; ?>">
                        <div class="post-content"><?php echo $post_data->post_content; ?></div>
                        <h5 class="text-black">Kontakt vores ejendomsservice</h5>
                        <div class="contact-info">
                            <h6 class="cities-name">Sjælland | Jylland | Fyn, Rud Kristensen</h6>
                            <div class="contact-detail">
                                <div class="contact-icon">
                                    <img src="http://anderst30.sg-host.com/wp-content/uploads/2023/12/phone-call.svg" alt="Call Icon">
                                </div>
                                <a href="tel:+45 31329542">+45 31329542</a>
                            </div>
                        </div>
                        <div class="send-email-btn">
                            <button>Send email <img src="http://anderst30.sg-host.com/wp-content/uploads/2023/12/green-circle-arrow.svg" alt="Green Circle Arrow"></button>
                        </div>
                    </div>
                <?php
                    $loop++;
                endforeach;
                ?>
            </div>
        </div>
        <script>
            ($ => {
                const tab = $('.tablinks, .faq-tab-mobile');

                $(`[data-tab="title-0"]`).addClass('active');
                $(`#title-0`).show();

                tab.on('click', function() {
                    const dataTab = $(this).data('tab');

                    // Remove 'active' class from all tabs and add it to the clicked tab
                    tab.removeClass('active').filter(`[data-tab="${dataTab}"]`).addClass('active');

                    // Hide all tab contents and show the one corresponding to data_tab_id
                    $('.tabcontent').hide();
                    $(`#${dataTab}`).show();
                });
            })(jQuery);
        </script>


        <?php
        return ob_get_clean();
    }
}
add_shortcode('faq_tab', 'faq_tabs');

/**
 * dispaly Flats Details
 * shortcode
 * [flats_details]
 */

if (!function_exists('flats')) {
    function flats()
    {
        ob_start();


        if (have_rows('available_flat')) :
            while (have_rows('available_flat')) : the_row();
        ?>
                <div class="flats-details">
                    <?php
                    if (have_rows('available_flats')) :
                        while (have_rows('available_flats')) : the_row();
                            $flat_image = get_sub_field('flat_image');
                            $title = get_sub_field('title');
                            $square_meter = get_sub_field('square_meter');
                            $rum = get_sub_field('rum');
                            $move_date = get_sub_field('move_date')  ?  get_sub_field('move_date') : 'Snarest';
                            $energy_class = get_sub_field('energy_class');
                            $monthly_rent = get_sub_field('monthly_rent');
                            $button_url = get_sub_field('button_url');
                    ?>
                            <div class="flats-column">
                                <div class="flat-image">
                                    <img src="<?php echo $flat_image; ?>">
                                </div>
                                <div class="rooms-details">
                                    <h2 class="flat-title"><?php echo $title; ?></h2>

                                    <div class="avalible-room">
                                        <div class="d-flex meter-info">
                                            <img src="http://anderst30.sg-host.com/wp-content/uploads/2024/01/real-estate-dimensions-green.svg">
                                            <div class="rooms-info">
                                                <p>Størrelse</p>
                                                <p class="text-black"><?php echo $square_meter; ?></p>
                                            </div>
                                        </div>
                                        <div class="d-flex meter-info">
                                            <img src="http://anderst30.sg-host.com/wp-content/uploads/2024/01/building-green.svg">
                                            <div class="rooms-info">
                                                <p>Rum</p>
                                                <p class="text-black"><?php echo $rum; ?></p>
                                            </div>
                                        </div>

                                        <div class="d-flex meter-info">
                                            <img src="http://anderst30.sg-host.com/wp-content/uploads/2024/01/calendar-check-green.svg">
                                            <div class="rooms-info">
                                                <p>Indflytningsdato</p>
                                                <p class="text-black"><?php echo $move_date; ?></p>
                                            </div>
                                        </div>

                                        <div class="energy-class">
                                            <img src="<?php echo $energy_class; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="monthly-info">
                                    <a class="green-btn" href="<?php echo $button_url; ?>">Se bolig</a>
                                    <h5 class="mothly-rent"><?php echo $monthly_rent; ?></h5>
                                </div>
                            </div>

                    <?php
                        endwhile;
                    endif;
                    ?>
                </div>
        <?php
            endwhile;
        endif;
        return ob_get_clean();
        ?>
    <?php
    }
}

add_shortcode('flats_details', 'flats');



/**
 * dispaly Contact Form
 * shortcode
 * [contact_form]
 */
/*
if (!function_exists('contact')) {
    function contact()
    {
        ob_start();
    ?>
        <form>
            <div class="housing-agent-sec contact-form">

                <div class="housing-info-box">

                    <!-- ................Start::Antal Rum No................. -->
                    <div class="antal-rum-sec">
                        <h3 class="agent-heading">Please tap on your reason to fill the form</h3>

                        <div class="housing-type-fields room-num-fields">
                            <div class="form-group">
                                <input type="checkbox" id="administration">
                                <label for="administration"><span>Administration</span><img src="images/building-icon.svg"></label>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" id="services">
                                <label for="services"><span>Property services</span><img src="images/building-icon.svg"></label>
                            </div>

                            <div class="form-group">
                                <input type="checkbox" id="rental">
                                <label for="rental"><span>Rental</span><img src="images/building-icon.svg"></label>
                            </div>
                        </div>

                        <div class="housing-type-fields room-num-fields">
                            <div class="form-group">
                                <input type="checkbox" id="others">
                                <label for="others"><span>Others</span></label>
                            </div>
                        </div>

                        <div class="contact-form-part">
                            <div class="w-50">
                                <input type="text" name="" placeholder="Enter your name">
                            </div>
                            <div class="w-50">
                                <input type="number" name="" placeholder="Enter your mobile number">
                            </div>
                            <div class="w-50">
                                <input type="email" name="" placeholder="Enter your email">
                            </div>
                            <div class="w-50">
                                <input type="text" name="" placeholder="Write your message here">
                            </div>
                        </div>
                        <div class="file-info">
                            <div class="w-50">
                                <div class="add-file">
                                    <p class="text-black">Tilføjer filer (maks. 10mb)</p>
                                    <a href="">Tilføj flere filer</a>
                                </div>
                                <div class="file-upload mb-3">
                                    <input id="file1" class="file1" type="file" multiple name="file1[]" />
                                    <div class="kwt-file-container"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group next-btns">
                    <button class="submit-rantal-form" name="submit-rantal-form">
                        <img src="https://anderst30.sg-host.com/wp-content/uploads/2023/12/green-circle-arrow.svg">Indsend</button>
                </div>

            </div>
        </form>

        <script>
            (function($) {
                var customDragandDrop = function(element) {
                    $(element).addClass("kwt-file__input");
                    var element = $(element).wrap(
                        `<div class="kwt-file">
                        <div class="kwt-file__drop-area">
                            <span class='kwt-file__choose-file ${element.attributes.data_btn_text
                        ? "" === element.attributes.data_btn_text.textContent
                            ? ""
                            : "kwt-file_btn-text"
                        : ""
                    }'>${element.attributes.data_btn_text
                        ? "" === element.attributes.data_btn_text.textContent
                            ? `<img src="http://anderst30.sg-host.com/wp-content/uploads/2023/12/phone-call.svg" />`
                            : `${element.attributes.data_btn_text.textContent}`
                        : `<img src="http://anderst30.sg-host.com/wp-content/uploads/2023/12/phone-call.svg" />`
                    }</span>${element.outerHTML}Filnavn<span class="kwt-file__msg"></span><div class="kwt-file__delete"></div></div></div>`
                    );
                    var element = element.parents(".kwt-file");

                    // Add class on focus and drag enter event.
                    element.on("dragenter focus click", ".kwt-file__input", function(e) {
                        $(this).parents(".kwt-file__drop-area").addClass("is-active");
                    });

                    // Remove class on blur and drag leave event.
                    element.on("dragleave blur drop", ".kwt-file__input", function(e) {
                        $(this).parents(".kwt-file__drop-area").removeClass("is-active");
                    });

                    // Show filename and handle file size validation.
                    element.on("change", ".kwt-file__input", function(e) {
                        let filesCount = $(this)[0].files.length;
                        let textContainer = $(this).next(".kwt-file__msg");

                        // Check file size
                        let maxSize = 10 * 1024 * 1024; // 10 MB in bytes

                        for (let i = 0; i < filesCount; i++) {
                            if ($(this)[0].files[i].size > maxSize) {
                                textContainer.text("File size should be 10 MB or less.");
                                $(this).parents(".kwt-file").find(".kwt-file__delete").css("display", "none");
                                // Clear the file input
                                $(this).val(null);
                                return;
                            }
                        }

                        if (filesCount > 0) {
                            let fileNames = [];
                            let totalSize = 0;

                            for (let i = 0; i < filesCount; i++) {
                                fileNames.push($(this)[0].files[i].name);
                                totalSize += $(this)[0].files[i].size;
                            }

                            if (totalSize > maxSize) {
                                textContainer.text("Total size should be 10 MB or less.");
                                $(this).parents(".kwt-file").find(".kwt-file__delete").css("display", "none");
                                // Clear the file input
                                $(this).val(null);
                            } else {
                                textContainer
                                    .text(fileNames.join(", "))
                                    .next(".kwt-file__delete")
                                    .css("display", "inline-block");
                            }
                        } else {
                            textContainer.text("Filnavn");
                            $(this).parents(".kwt-file").find(".kwt-file__delete").css("display", "none");
                        }
                    });

                    // Delete selected file.
                    element.on("click", ".kwt-file__delete", function(e) {
                        let deleteElement = $(this);
                        deleteElement.parents(".kwt-file").find(`.kwt-file__input`).val(null);
                        deleteElement
                            .css("display", "none")
                            .prev(`.kwt-file__msg`)
                            .text("");
                    });
                };

                $.fn.kwtFileUpload = function() {
                    var _this = $(this);
                    $.each(_this, function(index, element) {
                        customDragandDrop(element);
                    });
                    return this;
                };

                // Function to add more file inputs dynamically
                $(".add-more-files").on("click", function() {
                    let container = $(".kwt-file-container");
                    let fileInput = $("<input>").attr({
                        type: "file",
                        class: "kwt-file__input",
                        multiple: "multiple",
                        name: "file1[]"
                    });
                    container.append(fileInput);
                });
            })(jQuery);

            // Plugin initialize
            jQuery(document).ready(function($) {
                $(".file1").kwtFileUpload();
            });
        </script>
<?php
        return ob_get_clean();
    }
}

add_shortcode('contact_form', 'contact');

*/



/**
 * Contact Form file upload js
 */
function add_custom_script()
{
    ?>
    <script type="text/javascript">
        (($) => {
            setTimeout(() => {
                // Function to truncate the file name with a minimum length
                function truncateFileName(fileName, minLength) {
                    return fileName.length <= minLength ? fileName : `${fileName.substr(0, minLength)}...${fileName.substr(-minLength)}`;
                }
    
                // Function to modify file name based on certain conditions
                function modifyFileName(fileName) {
                    // Exclude files with ".html" extension from truncation
                    return fileName.endsWith(".html") ? fileName : truncateFileName(fileName, 5);
                }
    
                // Function to display truncated file names
                function displayTruncatedFileNames() {
                    // Select all elements with the class "dnd-upload-status"
                    $(".dnd-upload-status").find(".dnd-upload-details .name span").each((index, element) => {
                        // Modify the file name directly
                        element.textContent = modifyFileName(element.textContent);
                    });
                }
    
                // Attach the displayTruncatedFileNames function to the change event of the file input with ID "upload-file"
                $("#upload-file").change(displayTruncatedFileNames);
            }, 1000);

        })(jQuery);
    </script>
<?php
}

// Hook the function to the 'wp_footer' action
add_action('wp_footer', 'add_custom_script', 999);
