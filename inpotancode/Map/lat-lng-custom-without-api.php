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
        // add_action('wp_enqueue_scripts', array($this, 'enqueue_google_maps')); // Enqueue Google Maps script
        add_shortcode('property_map', array($this, 'property_map_shortcode')); // Shortcode to display map
        add_action('wp_footer', array($this, 'property_map_script')); // Generate map script
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

                // Get the repeater field values
                $property_locations = get_field('maps');

                // Check if the repeater field has values
                if ($property_locations) {
                    // Loop through repeater rows
                    foreach ($property_locations as $location) {
                        $lat = $location['lat'];
                        $lng = $location['lng'];
                        $title = get_the_title();
                        $permalink = get_permalink();

                        // If location data exists, create marker information
                        $this->markers[] = array(
                            'lat' => $lat,
                            'lng' => $lng,
                            'title' => $title,
                            'permalink' => $permalink,
                        );
                    }
                }
            }

            wp_reset_postdata();
        }

        // Return HTML for map container
        ob_start();
?>
        <div id="map" style="height: 100vh;"></div>
    <?php
        return ob_get_clean();
    }


    /**
     * Generate script for Leaflet map with markers
     */
    public function property_map_script()
    {
        // Enqueue Leaflet CSS
        wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet/dist/leaflet.css');

        // Enqueue Leaflet JavaScript
        wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet/dist/leaflet.js');

        $map_markers = json_encode($this->markers); // Encode markers data to JSON

        // Output JavaScript for initializing Leaflet map with markers
    ?>
        <script type="text/javascript">
            function initPropertyMap() {
                var map = L.map('map').setView([56.2639, 9.5018], 10);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                var markers = <?php echo $map_markers; ?>;
                var currentPopup; // Variable to store the currently open popup

                for (var i = 0; i < markers.length; i++) {
                    var markerInfo = markers[i];
                    var marker = L.marker([markerInfo.lat, markerInfo.lng]).addTo(map);

                    marker.bindPopup('<h3>' + markerInfo.title + '</h3><p><a href="' + markerInfo.permalink + '" class="read-more-button">Read More</a></p>');

                    marker.on('mouseover', function(e) {
                        // Open the popup for the marker
                        e.target.openPopup();

                        // Close the currently open popup, if any
                        if (currentPopup && currentPopup !== e.target.getPopup()) {
                            currentPopup.closePopup();
                        }

                        // Update the currently open popup
                        currentPopup = e.target.getPopup();
                    });
                }
            }

            // Ensure the script is executed after the map container is loaded
            document.addEventListener("DOMContentLoaded", function() {
                initPropertyMap();
            });

            // Resize map to full height on window resize
            window.addEventListener("resize", function() {
                document.getElementById('map').style.height = window.innerHeight + "px";
                map.invalidateSize(); // Refresh the map to fit the new size
            });
        </script>
<?php
    }
}

$property_map_plugin = new Property_Map_Plugin();
