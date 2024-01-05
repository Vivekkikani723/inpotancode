<?php
/*
Plugin Name: Map Plugin
Description: Display a Google Map with property markers.
Version: 1.0
*/

class Property_Map_Plugin
{
    private $markers = array(); // Store markers data for the map

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_google_maps')); // Enqueue Google Maps script
        add_shortcode('property_map', array($this, 'property_map_shortcode')); // Shortcode to display map
        add_action('wp_footer', array($this, 'property_map_script')); // Generate map script
    }

    /**
     * Enqueue Google Maps script
     */
    public function enqueue_google_maps()
    {
        wp_enqueue_script(
            'google-maps',
            "https://maps.googleapis.com/maps/api/js?key=AIzaSyCNlI0WbLLCtZweQrWJw67NsFY0HcYmtVY&callback=initPropertyMap",
            array(),
            null,
            false
        );
    }

    /**
     * Shortcode to display the Google map
     *
     * @return string Html return
     */
    public function property_map_shortcode()
    {
        // Query to fetch properties
        $properties_query = new WP_Query(array(
            'post_type' => 'map',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        // Collect location data for markers
        if ($properties_query->have_posts()) {
            while ($properties_query->have_posts()) {
                $properties_query->the_post();
                $property_location = get_post_meta(get_the_ID(), 'property_location_google_map', true);

                // If location data exists, create marker information
                if ($property_location) {
                    $this->markers[] = array(
                        'lat' => $property_location['lat'],
                        'lng' => $property_location['lng'],
                        'title' => get_the_title(),
                        'permalink' => get_permalink(),
                    );
                }
            }
            wp_reset_postdata();
        }

        // Return HTML for map container
        ob_start();
?>
        <div id="map" style="height: 400px;"></div>
    <?php
        return ob_get_clean();
    }

    /**
     * Generate script for Google Map with markers
     */
    public function property_map_script()
    {
        $map_markers = json_encode($this->markers); // Encode markers data to JSON

        // Output JavaScript for initializing Google Map with markers
    ?>
        <script type="text/javascript">
            function initPropertyMap() {
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 10,
                    center: {
                        lat: 56.2639,
                        lng: 9.5018,
                    }
                });

                var customIcon = {
                    url: '/wp-content/uploads/2023/12/google_marker.png',
                };

                var markers = <?php echo $map_markers; ?>

                var openInfoWindow = null;

                // Create markers on the map
                markers.forEach(function(markerInfo) {
                    var marker = new google.maps.Marker({
                        position: {
                            lat: markerInfo.lat,
                            lng: markerInfo.lng
                        },
                        map: map,
                        title: markerInfo.title,
                        icon: customIcon,
                    });

                    var infowindow = new google.maps.InfoWindow({
                        content: '<h3>' + markerInfo.title + '</h3><p><a href="' + markerInfo.permalink + '" class="read-more-button">Read More</a></p>',
                    });

                    // Show info window on marker click
                    marker.addListener('click', function() {
                        if (openInfoWindow) {
                            openInfoWindow.close();
                        }

                        infowindow.open(map, marker);
                        openInfoWindow = infowindow;
                    });
                });
            }

            window.addEventListener("load", initPropertyMap);
        </script>
<?php
    }
}

$property_map_plugin = new Property_Map_Plugin();
