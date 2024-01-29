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
                                                    <div class="image-zoom">
                                                        <a id="openModalBtn" class="show-image-btn-<?php echo $inner_loop; ?>">
                                                            <img src="http://anderst30.sg-host.com/wp-content/uploads/2023/12/zoom-icon.svg" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="slick_slider_image js-gallery-popup-<?php echo $inner_loop; ?>">
                                                        <img src="<?php echo $image; ?>">
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="image-gallery">
                                            <?php foreach ($images as $image) : ?>
                                                <div class="slider-for">
                                                    <div><img src="<?php echo $image; ?>"></div>
                                                    <!-- <div class="thumb-counter"></div> -->
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="plantegninger">
                                            <a href="<?php echo $plantegninger; ?>" data-lightbox="js-gallery-popup img-<?php echo $inner_loop; ?>">
                                                <img src="http://anderst30.sg-host.com/wp-content/uploads/2023/12/real-estate-dimensions-block.svg" alt="" class="plantegninger-img">
                                                Plantegninger
                                                <img src="<?php echo $plantegninger; ?>" alt="Image" class="plantegninger-none">
                                            </a>
                                        </div>
                                    </div>


                                </div>
                            </div>
                    <?php
                            $inner_loop++;
                            $default_active = false;
                        endif;
                    endwhile;
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

                        let propertiesSliderSec = document.querySelector(".properties-slider-sec");

                        buttons.forEach(function(button) {
                            button.addEventListener("click", function() {
                                buttons.forEach(function(btn) {
                                    btn.classList.remove("active");
                                });
                                this.classList.add("active");
                                var dataTarget = this.getAttribute("data-target");
                                propertiesSliderSec.className = "fl-row fl-row-full-width fl-row-bg-none fl-node-j26o4quf3nmv fl-row-default-height fl-row-align-center properties-slider-sec " + dataTarget;
                            });
                        });
                    });

                    function initializeSlider(index) {
                        var carousel = $('.tabcontent.active .slider-nav');
                        carousel.slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: false,
                            fade: true,
                            adaptiveHeight: true,
                            infinite: false,
                            asNavFor: '.tabcontent.active .image-gallery'
                        });

                        $('.tabcontent.active .image-gallery').slick({
                            slidesToShow: 6,
                            slidesToScroll: 1,
                            asNavFor: '.tabcontent.active .slider-nav',
                            dots: false,
                            centerMode: false,
                            focusOnSelect: true,
                            infinite: false,
                            responsive: [{
                                    breakpoint: 768,
                                    settings: {
                                        slidesToShow: 6
                                    }
                                },
                                {
                                    breakpoint: 480,
                                    settings: {
                                        slidesToShow: 6
                                    }
                                }
                            ]
                        });

                        // updateThumbCounter($('.tabcontent.active .image-gallery'), 0, $('.tablinks.active').index());

                        $('.image-gallery').on('init reInit afterChange', function(event, slick, currentSlide) {
                            // Pass the active tab index to updateThumbCounter
                            updateThumbCounter(slick, currentSlide, $('.tablinks.active').index());
                        });
                        $('.tabcontent.active .slider-nav').slickLightbox({
                            src: 'src',
                            itemSelector: `.js-gallery-popup-${index} img`, // Use the index to target the correct tab content
                            background: 'rgba(0, 0, 0, .7)'
                        });

                        // $('.tabcontent.active .slider-nav').slickLightbox().on({
                        //     'shown.slickLightbox': function() {
                        //         $("body").css({
                        //             overflow: "hidden",
                        //             height: "100vh"
                        //         });
                        //     },
                        //     'hide.slickLightbox': function() {
                        //         $("body").css({
                        //             overflow: "auto",
                        //             height: "auto"
                        //         });
                        //     },
                        // });

                        const handleMegnifyImage = () => {
                            $(`.slider-nav .slick-track .slick-current.slick-active .js-gallery-popup-${index} img`).click();
                        }
                        $(`.show-image-btn-${index}`).on('click', handleMegnifyImage);
                    }

                    function updateThumbCounter(slick, currentSlide, activeTabIndex) {
                        const visibleSlidesCount = slick.options.slidesToShow;
                        const activeSlideIndex = currentSlide || 0;

                        if (visibleSlidesCount === 6) {
                            $('.thumb-counter').remove();

                            const remainingSlides = slick.slideCount - activeSlideIndex - visibleSlidesCount + 1;
                            if (remainingSlides > 0) {
                                const appendCounterDiv = $(`<div class="thumb-counter">+${remainingSlides}</div>`);
                                $(`.tabcontent:eq(${activeTabIndex}) .image-gallery .slick-slide.slick-active:eq(5)`)
                                    .css('opacity', 1)
                                    .append(appendCounterDiv);
                            }
                        }
                    }

                    initializeSlider(0);

                    lightbox.option({
                        "resizeDuration": 200,
                        "wrapAround": true
                    });

                    /* See More Button script */
                    const content = document.querySelector("#post-content .fl-rich-text");
                    const button = document.querySelector("#read-more");

                    const MIN_CONTENT_LENGTH = 200;
                    const contentText = content.innerText.trim();
                    const isContentLongEnough = contentText.split(/\s+/).length >= MIN_CONTENT_LENGTH;

                    if (isContentLongEnough) {
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
                    } else {
                        // If content is too short, hide the button
                        button.style.display = "none";
                    }

                });
            </script>
<?php
        }
    }
}
