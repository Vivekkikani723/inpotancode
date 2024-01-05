<?php
add_action('wp_footer', 'google_map_init', 999);

function google_map_init()
{
  if (is_page(30687) || is_page(725) || is_page(19956)) :

    // infobox html
    echo map_info_box_html();


    if (is_page(19956)) {
      $args = [
        'post_type' => 'wall',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_key' => 'wall_membership_level_name',
        'meta_value' => array('Trade', 'Associate'),
        /* 'tax_query' => [
              [
                  'taxonomy' => 'associate_type',
                  'field' => 'term_id',
                  'terms' => get_terms(['taxonomy' => 'associate_type', 'fields' => 'ids']),
                  'operator' => 'IN',
              ],
          ], */
      ];
    } else {
      $args = [
        'post_type' => 'wall',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => [
          'relation' => 'OR',
          [
            'key'     => 'membertypes',
            'value'   => 'Associate Member',
            'compare' => '!=',
            'type'    => 'CHAR',
          ],
          [
            'key'     => 'membertypes',
            'compare' => 'NOT EXISTS',
          ],
          [
            'key'     => 'membertypes',
            'value'   => '',
            'compare' => '=',
          ],
        ],
      ];
    }

    $wallQuery = new WP_Query($args);

    $wallCoordinates = [];

    if ($wallQuery->have_posts()) :
      while ($wallQuery->have_posts()) : $wallQuery->the_post();

        // get meta data from post
        $lat_long_string  = get_post_meta(get_the_ID(), 'latitude_longitude', true);
        $wall_website     = get_post_meta(get_the_ID(), 'wall_website', true);
        $wall_phone       = get_post_meta(get_the_ID(), 'wall_phone', true);
        $wall_email       = get_post_meta(get_the_ID(), 'wall_email', true);
        $wall_type        = get_post_meta(get_the_ID(), 'wall_type', true);

        if (is_array($wall_type)) {
          $wall_type = implode(', ', $wall_type);
        }

        // default fields
        $wall_name = get_the_title();
        $wall_parmalink = get_the_permalink();

        $map_icon_url = site_url() . '/wp-content/uploads/2023/11/m';

        if (!$lat_long_string) continue;

        // get lat long
        $lat_long_array = explode(',', $lat_long_string);
        $lat = $long = '';
        if (!empty($lat_long_array) && count($lat_long_array) > 1) {
          $lat = $lat_long_array[0];
          $long = $lat_long_array[1];
        }

        $wallCoordinates[] = [
          'lat'             => $lat,
          'lng'             => $long,
          'wall_name'       => $wall_name,
          'wall_phone'      => $wall_phone,
          'wall_email'      => $wall_email,
          'wall_website'    => $wall_website,
          'wall_type'       => $wall_type,
          'wall_permalink'  => $wall_parmalink,
          /* 'map_icon'        => $map_icon_url */
        ];
      endwhile;
    endif;

    if (empty($wallCoordinates)) $wallCoordinates = false;

    $wallCoordinates = json_encode($wallCoordinates);
    $infoICON = site_url() . '/wp-content/uploads/2023/11/more-info.png';
    $websiteICON = site_url() . '/wp-content/uploads/2023/11/global-1-150x150.png';
    $phoneICON = site_url() . '/wp-content/uploads/2023/11/telephone-1-150x150.png';
    $emailICON = site_url() . '/wp-content/uploads/2023/11/mail-1-150x150.png';
?>

    <script>
      (function($) {
        /* MAP MODEL */
        // Get references to the modal and the close button
        const modal = document.getElementById("mapInfoModal");
        const closeModalButton = document.getElementById("closeModal");
        const infoIconURL = '<?php echo $infoICON; ?>';
        const websiteICON = '<?php echo $websiteICON; ?>';
        const phoneICON = '<?php echo $phoneICON; ?>';
        const emailICON = '<?php echo $emailICON; ?>';

        // Function to open the modal
        function openModal(e) {
          let website = selectedWall.wall_website;
          let websiteLabel = website;
          if (!website.startsWith("http")) {
            website = "http://" + website;
          } else {
            websiteLabel = websiteLabel.replace('http://', '');
          }

          // wallType
          const wallType = (selectedWall.wall_type) ? `<div class="wall-type">Wall Type: <span id="wall-type">${selectedWall.wall_type}</span></div>` : '';
          // wallEmail
          const wallEmail = (selectedWall.wall_email) ? `<div class="wall-email"><img src="${emailICON}" class="modal-icon"/><a href="mailto:${selectedWall.wall_email}" id="wall-email">${selectedWall.wall_email}</a></div>` : '';
          // wallPhone
          const wallPhone = (selectedWall.wall_phone) ? `<div class="wall-phone"><img src="${phoneICON}" class="modal-icon"/><a href="tel:${selectedWall.wall_phone}" id="wall-phone">${selectedWall.wall_phone}</a></div>` : '';
          // wallWebsite
          const wallWebsite = (selectedWall.wall_website) ? `<div class="wall-website"><img src="${websiteICON}" class="modal-icon"/><a href="${website}" id="wall-website" target="_blank">${websiteLabel}</a></div>` : '';

          // modal body
          const modalContent = wallType + wallEmail + wallPhone + wallWebsite;

          // footer button
          const footerButton = `<a type="button" id="single-post-link" class="btn btn-primary directions" href="${selectedWall.wall_permalink}">
            <img src="${infoIconURL}" alt="${selectedWall.wall_name}" srcset="">
            More Info
          </a>`;

          modal.querySelector('.modal-body').innerHTML = modalContent;
          modal.querySelector('.modal-footer').innerHTML = footerButton;
          modal.querySelector('.modal-title .title').innerHTML = selectedWall.wall_name;
          // modal.querySelector('#wall-email').text(selectedWall.wall_email).attr('href', "mailto:" + wall_email);
          // modal.querySelector('#wall-phone').text(selectedWall.wall_phone).attr('href', "tel:" + wall_phone);
          // modal.querySelector('#wall-website').text(selectedWall.wall_website).attr('href', website);
          // modal.querySelector('#single-post-link').attr('href', wall_permalink);

          modal.style.display = "flex";
          document.querySelector('body').style.overflow = 'hidden';
        }

        // Function to close the modal
        function closeModal() {
          modal.style.display = "none";
          document.querySelector('body').style.overflow = 'unset';
        }

        // press esc clos modal
        document.addEventListener("keydown", function(event) {
          if (event.key === "Escape") {
            closeModal(); // Call your closeModal function or perform the necessary actions to close the modal
          }
        });

        // Event listener to open the modal
        /* document.addEventListener("click", function(event) {
          if (event.target.id === "showModalButton") {
            openModal();
          }
        }); */

        // Event listener to close the modal when clicking the close button
        closeModalButton.addEventListener("click", closeModal);

        // Event listener to close the modal when clicking outside of the modal
        window.addEventListener("click", function(event) {
          if (event.target === modal) {
            closeModal();
          }
        });
        /* END MAP MAP MODEL */



        let map;
        let markers = [];
        let selectedWall = null;
        const wallCoordinates = JSON.parse(`<?php echo $wallCoordinates; ?>`);

        function initMap() {
          // Define the custom style as an array of map style objects
          const customMapStyle = [{
              "featureType": "all",
              "elementType": "labels",
              "stylers": [{
                "visibility": "simplified"
              }]
            },
            {
              "featureType": "all",
              "elementType": "labels.text",
              "stylers": [{
                "color": "#444444"
              }]
            },
            {
              "featureType": "administrative.country",
              "elementType": "all",
              "stylers": [{
                "visibility": "simplified"
              }]
            },
            {
              "featureType": "administrative.country",
              "elementType": "geometry",
              "stylers": [{
                "visibility": "simplified"
              }]
            },
            {
              "featureType": "administrative.province",
              "elementType": "all",
              "stylers": [{
                "visibility": "off"
              }]
            },
            {
              "featureType": "administrative.locality",
              "elementType": "all",
              "stylers": [{
                  "visibility": "simplified"
                },
                {
                  "saturation": "-100"
                },
                {
                  "lightness": "30"
                }
              ]
            },
            {
              "featureType": "administrative.neighborhood",
              "elementType": "all",
              "stylers": [{
                "visibility": "off"
              }]
            },
            {
              "featureType": "administrative.land_parcel",
              "elementType": "all",
              "stylers": [{
                "visibility": "off"
              }]
            },
            {
              "featureType": "landscape",
              "elementType": "all",
              "stylers": [{
                  "visibility": "simplified"
                },
                {
                  "gamma": "0.00"
                },
                {
                  "lightness": "74"
                }
              ]
            },
            {
              "featureType": "landscape",
              "elementType": "geometry",
              "stylers": [{
                "color": "#ffffff"
              }]
            },
            {
              "featureType": "poi",
              "elementType": "all",
              "stylers": [{
                "visibility": "off"
              }]
            },
            {
              "featureType": "road",
              "elementType": "geometry",
              "stylers": [{
                  "visibility": "simplified"
                },
                {
                  "color": "#ff0000"
                },
                {
                  "saturation": "-15"
                },
                {
                  "lightness": "40"
                },
                {
                  "gamma": "1.25"
                }
              ]
            },
            {
              "featureType": "road",
              "elementType": "labels",
              "stylers": [{
                "visibility": "off"
              }]
            },
            {
              "featureType": "transit",
              "elementType": "labels",
              "stylers": [{
                "visibility": "simplified"
              }]
            },
            {
              "featureType": "transit",
              "elementType": "labels.icon",
              "stylers": [{
                "visibility": "off"
              }]
            },
            {
              "featureType": "transit.line",
              "elementType": "geometry",
              "stylers": [{
                  "color": "#ff0000"
                },
                {
                  "lightness": "80"
                }
              ]
            },
            {
              "featureType": "transit.station",
              "elementType": "geometry",
              "stylers": [{
                "color": "#e5e5e5"
              }]
            },
            {
              "featureType": "water",
              "elementType": "geometry",
              "stylers": [{
                "color": "#efefef"
              }]
            },
            {
              "featureType": "water",
              "elementType": "labels",
              "stylers": [{
                "visibility": "off"
              }]
            }
          ];

          map = new google.maps.Map(document.getElementById("map"), {
            center: new google.maps.LatLng(50.736129, -1.988229),
            zoom: 6,
            mapTypeControl: false,
            zoomControl: true,
            scaleControl: false,
            streetViewControl: false,

            rotateControl: false,
            fullscreenControl: false,
            styles: customMapStyle,
          });

          // const locationButton = document.createElement("button");
          // locationButton.className = "geolocate";
          // locationButton.textContent = "";
          // locationButton.classList.add("custom-map-control-button");
          // map.controls[google.maps.ControlPosition.TOP_RIGHT].push(locationButton);
          // locationButton.addEventListener("click", () => {
          //   // Try HTML5 geolocation.
          //   if (navigator.geolocation) {
          //     navigator.geolocation.getCurrentPosition(
          //       (position) => {
          //         const pos = {
          //           lat: position.coords.latitude,
          //           lng: position.coords.longitude,
          //         };
          //         map.setCenter(pos);
          //         map.setZoom(11);
          //       },
          //       () => {
          //         //handleLocationError(true, infoWindow, map.getCenter());
          //       }
          //     );
          //   } else {
          //     // Browser doesn't support Geolocation
          //     //handleLocationError(false, infoWindow, map.getCenter());
          //   }
          // });


          const bounds = new google.maps.LatLngBounds();
          /* const normalClub = {
            url: "/normal.png",
            size: new google.maps.Size(32, 32),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(16, 16),
          } */

          /* const map_icon_url = {
            url: `/wp-content/uploads/2023/11/m`,
            size: new google.maps.Size(32, 32),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(24, 0),
          } */

          const map_icon_url = {
            url: "/wp-content/uploads/2023/11/new-map-location-.png",
            size: new google.maps.Size(32, 32),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(16, 16),
          }

          if (wallCoordinates.length > 0) {
            for (var i = 0; i < wallCoordinates.length; i++) {
              let entry = wallCoordinates[i];
              var latLng = new google.maps.LatLng(
                entry.lat,
                entry.lng
              );
              bounds.extend(latLng);
              var marker = new google.maps.Marker({
                position: latLng,
                icon: map_icon_url
              });
              marker.addListener("click", function(e) {
                selectedWall = entry;

                openModal();

              });
              markers.push(marker);
            }
          }

          new MarkerClusterer(map, markers, {
            imagePath: "/wp-content/uploads/2023/11/nm",
          });
          map.fitBounds(bounds);

          // document.getElementById("loading").remove();
        }


        $('#mapInfoModal').on('', function(event) {
          let modal = $(this);
          modal.find('.title').text(selectedWall.wall_name);
          let website = selectedWall.website;
          if (!website.startsWith("http")) {
            website = "http://" + website;
          }
          modal.find('.website').text(selectedWall.website).attr('href', website);
          modal.find('.notes').html(selectedWall.description);
          modal.find('.address0').text(selectedWall.address0);
          modal.find('.address1').text(selectedWall.address1);
          modal.find('.town').text(selectedWall.town);
          modal.find('.county').text(selectedWall.county);
          modal.find('.postcode').text(selectedWall.postcode);
          modal.find('.telephone').text(selectedWall.telephone).attr('href', "tel:" + selectedWall.telephone);
          const destination = selectedWall.address0 + "," + selectedWall.address1 + "," + selectedWall.postcode + ", UK";

          const link = "https://www.google.co.uk/maps/dir/?api=1&destination=" + encodeURIComponent(destination);
          modal.find('.directions').attr('href', link);
        })

        window.addEventListener("load", initMap);
      })(jQuery);

      // window.addEventListener("load", initMap);
    </script>
<?php
  endif;
}

function map_info_box_html()
{
  return <<<HTML
  <div class="modal fade" id="mapInfoModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.319 1.319 0 0 0-.37.265.301.301 0 0 0-.057.09V14l.002.008a.147.147 0 0 0 .016.033.617.617 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.619.619 0 0 0 .146-.15.148.148 0 0 0 .015-.033L12 14v-.004a.301.301 0 0 0-.057-.09 1.318 1.318 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465-1.281 0-2.462-.172-3.34-.465-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411z">
                        </path>
                    </svg> <span class="title">Gairloch Leisure Centre</span>
                </h5>
                <button type="button" id="closeModal" class="close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="wall-type">Wall Type: <span id="wall-type"></span></div>
                <div class="wall-email"><a href="" id="wall-email"></a></div>
                <div class="wall-phone"><a href="" id="wall-phone"></a></div>
                <div class="wall-website"><a href="" id="wall-website"></a></div>
            </div>
            <div class="modal-footer">
                <a type="button" id="single-post-link" class="btn btn-primary directions" href="" target="_blank">
                    <img src="" alt="" srcset="">
                    More Info
                </a>
            </div>
        </div>
    </div>
  </div>
  HTML;
}
