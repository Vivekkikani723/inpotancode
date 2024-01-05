<?php

/**
 * dispaly housing type using
 * shortcode
 * [housing_types]
 */
if (!function_exists('discover_housing_types')) {
    function discover_housing_types()
    {
        ob_start();
        while (have_rows('discover_the_housing_types')) : the_row();
            if (have_rows('housing_typing_details')) :
?>

                <div class="housing-section">
                    <div class="tab">
                        <?php
                        $default_active = true; // Set default_active to true for the first button
                        $loop = 0;
                        while (have_rows('housing_typing_details')) : the_row();
                            $housing_type_title = get_sub_field('housing_type_title');
                            if (trim($housing_type_title)) {
                        ?>
                                <button class="tablinks <?php echo $default_active ? 'active' : ''; ?>" data-target="title-<?php echo $loop; ?>">
                                    <?php echo $housing_type_title; ?>
                                </button>
                        <?php
                                $default_active = false; // Set default_active to false after the first button is created
                                $loop++;
                            }
                        endwhile;
                        ?>
                    </div>

                    <?php
                    $default_active = true; // Reset default_active for tabcontents
                    $inner_loop = 0;
                    while (have_rows('housing_typing_details')) : the_row();
                        $housing_type_title = get_sub_field('housing_type_title');
                        if (trim($housing_type_title)) :
                            $title = get_sub_field('title');
                            $sub_title = get_sub_field('sub_title');
                            $apartment_description = get_sub_field('apartment_description');
                            $see_housing_title = get_sub_field('see_housing_url')['title'];
                            $see_housing_url = get_sub_field('see_housing_url')['url'];
                            $images = get_sub_field('images'); // Assuming 'images' is the name of your gallery field
                            $plantegninger = get_sub_field('plantegninger')
                    ?>
                            <div class='tabcontent <?php echo $default_active ? 'active' : ''; ?>' id='title-<?php echo $inner_loop; ?>'>
                                <div class="slider-content">
                                    <div class="right-side-text">
                                        <h5 class="sub-title"><?php echo $sub_title; ?></h5>
                                        <h2 class="title"><?php echo $title; ?></h2>
                                        <p class="description"><?php echo nl2br($apartment_description); ?></p>
                                        <div class="See-housing-btn">
                                            <a href="<?php echo $see_housing_url; ?>" class="green-btn"><?php echo $see_housing_title; ?></a>
                                        </div>
                                    </div>

                                    <div class="slider-container">
                                        <div class="slider-nav">
                                            <?php
                                            foreach ($images as $image) : ?>
                                                <div class="slider slider-single ">
                                                    <div class="image-zoom js-gallery-popup">
                                                        <a href="<?php echo $image; ?>">
                                                            <img src="http://anderst30.sg-host.com/wp-content/uploads/2023/12/zoom-icon.svg" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="slick_slider_image js-gallery-popup">
                                                        <img src="<?php echo $image; ?>">
                                                    </div>
                                                </div>
                                            <?php endforeach;
                                            ?>
                                        </div>

                                        <div class="image-gallery">
                                            <?php foreach ($images as $image) : ?>
                                                <div class="slider-for">
                                                    <div><img src="<?php echo $image; ?>"></div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="plantegninger">
                                            <a href="<?php echo $plantegninger; ?>" data-lightbox="js-gallery-popup img-<?php echo $inner_loop; ?>">
                                                <img src="http://anderst30.sg-host.com/wp-content/uploads/2023/12/real-estate-dimensions-block.svg" alt="">
                                                Plantegninger
                                                <img src="<?php echo $plantegninger; ?>" alt="Image">
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                    <?php
                            $inner_loop++;
                            $default_active = false; // Set default_active to false after the first tabcontent is created
                        endif;
                    endwhile;
                    // $loop++; // Increment $loop after processing each set of housing_typing_details
                    ?>
                </div>

            <?php
            endif;
        endwhile;
        return ob_get_clean();
    }
}

add_shortcode('housing_types', 'discover_housing_types');


/**
 * javscript
 */

add_action('wp_footer', 'housing_type_tab_script');

if (!function_exists('housing_type_tab_script')) {
    function housing_type_tab_script()
    {
        if (is_singular('property')) {
            ?>
            <script>
                jQuery(document).ready(function($) {

                    /* housing type tab script*/
                    var buttons = document.querySelectorAll('.tablinks');
                    var tabcontents = document.querySelectorAll('.tabcontent');

                    buttons.forEach(function(button, index) {
                        button.addEventListener('click', function() {
                            // Remove 'active' class from all buttons
                            buttons.forEach(function(btn) {
                                btn.classList.remove('active');
                            });

                            // Add 'active' class to the clicked button
                            this.classList.add('active');

                            // Hide all tabcontents
                            tabcontents.forEach(function(tabcontent) {
                                tabcontent.classList.remove('active');
                            });

                            // Show the corresponding tabcontent
                            tabcontents[index].classList.add('active');

                            // Reinitialize slick slider for the active tabcontent
                            initializeSlider(index);
                        });
                    });

                    // Initial slider initialization
                    initializeSlider(0);

                    function initializeSlider(index) {
                        // Slick slider script
                        var $carousel = $('.tabcontent.active .slider-nav');
                        $carousel.slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: false,
                            fade: true,
                            adaptiveHeight: true,
                            asNavFor: '.tabcontent.active .image-gallery'
                        });

                        $('.tabcontent.active .image-gallery').slick({
                            slidesToShow: 4,
                            slidesToScroll: 1,
                            asNavFor: '.tabcontent.active .slider-nav',
                            dots: false,
                            centerMode: false,
                            focusOnSelect: true,
                            variableWidth: true
                        });

                        $('.slider-nav').slickLightbox({
                            src: 'src', // Use 'href' instead of 'src'
                            itemSelector: '.js-gallery-popup img',
                            background: 'rgba(0, 0, 0, .7)'
                        });
                    }

                    // Call the function with the appropriate index
                    initializeSlider(0); // You can pass the desired index as an argument


                    lightbox.option({
                        "resizeDuration": 200,
                        "wrapAround": true
                    });

                    var tabButtons = document.querySelectorAll(".tablinks");
                    var propertiesSliderSec = document.querySelector(".properties-slider-sec");

                    tabButtons.forEach(function(button) {
                        button.addEventListener("click", function() {
                            // Remove active class from all tablinks
                            tabButtons.forEach(function(btn) {
                                btn.classList.remove("active");
                            });

                            // Add active class to the clicked tablink
                            this.classList.add("active");

                            // Get the data-target value of the clicked tab
                            var dataTarget = this.getAttribute("data-target");

                            // Add the data-target name to properties-slider-sec
                            propertiesSliderSec.className = "fl-row fl-row-full-width fl-row-bg-none fl-node-j26o4quf3nmv fl-row-default-height fl-row-align-center properties-slider-sec " + dataTarget;
                        });
                    });
                });

                /* See More Button script */
                jQuery(document).ready(function($) {
                    const content = document.querySelector("#post-content .fl-rich-text");
                    const button = document.querySelector("#read-more");

                    let isContentVisible = false;

                    button.addEventListener("click", function(event) {
                        event.preventDefault();
                        const targetIEl = button.querySelector("a > i");
                        const targetSpanEl = button.querySelector("a > span");

                        const isContentVisibleClass = isContentVisible ?
                            "ua-icon-chevron-small-down" :
                            "ua-icon-chevron-small-up";

                        targetIEl.classList.remove(
                            "ua-icon-chevron-small-up",
                            "ua-icon-chevron-small-down"
                        );

                        targetIEl.classList.add(isContentVisibleClass);

                        targetSpanEl.innerText = !isContentVisible ? 'Se mindre' : 'Se mere';

                        content.style.maxHeight = isContentVisible ? "660px" : "none";

                        isContentVisible = !isContentVisible;
                    });
                });
            </script>

<?php
        }
    }
}
