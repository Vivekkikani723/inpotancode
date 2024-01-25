<?php

/**
 * display user subscription detail
 */
function user_subscription_detail()
{

    // get subscription level data
    $subscription_details = lampstand_user_subscription_data();

    $level_wise_table = [];

    $the_lampstand_digital          = '';
    $the_lampstand_print_ecclesial  = '';
    $The_lampstand_print_home       = '';


    foreach ($subscription_details as $key => $subscription_detail) {

        foreach ($subscription_detail as $s_key => $subscription_data) {

            $user_id = isset($subscription_data['level_details']->user_id) ? $subscription_data['level_details']->user_id : '';

            $first_name = isset($subscription_data['user_details']->first_name) ? $subscription_data['user_details']->first_name : '';

            $last_name = isset($subscription_data['user_details']->last_name) ? $subscription_data['user_details']->last_name : '';

            $user_email = isset($subscription_data['user_details']->user_email) ? $subscription_data['user_details']->user_email : '';

            $addr1 = isset($subscription_data['user_details']->addr1) ? $subscription_data['user_details']->addr1 : '';

            $addr2 = isset($subscription_data['user_details']->addr2) ? $subscription_data['user_details']->addr2 : '';

            $city = isset($subscription_data['user_details']->city) ? $subscription_data['user_details']->city : '';

            $thestate = isset($subscription_data['user_details']->thestate) ? $subscription_data['user_details']->thestate : '';

            $zip = isset($subscription_data['user_details']->zip) ? $subscription_data['user_details']->zip : '';

            $ihc_country = isset($subscription_data['user_details']->ihc_country) ? $subscription_data['user_details']->ihc_country : '';

            $ecclesia = isset($subscription_data['user_details']->ecclesia) ? $subscription_data['user_details']->ecclesia : '';

            $level_label = isset($subscription_data['level_details']->label) ? $subscription_data['level_details']->label : '';

            $start_time = isset($subscription_data['level_details']->start_time) ? $subscription_data['level_details']->start_time : '';

            $expire_time = isset($subscription_data['level_details']->expire_time) ? $subscription_data['level_details']->expire_time : '';

            $is_expired = isset($subscription_data['level_details']->is_expired) ? $subscription_data['level_details']->is_expired : '';

            $amount_value = isset($subscription_data['order_detail']->amount_value) ? $subscription_data['order_detail']->amount_value : '';

            $status = isset($subscription_data['order_detail']->status) ? $subscription_data['order_detail']->status : '';

            $create_date = isset($subscription_data['order_detail']->create_date) ? $subscription_data['order_detail']->create_date : '';

            // get table data subscription levels wise
            if ($key == 4) {
                $the_lampstand_digital .= '
                    <tr>
                        <td>"' . $user_id . '"</td>
                        <td>"' . $first_name . '"</td>
                        <td>"' . $last_name . '"</td>
                        <td>"' . $user_email . '"</td>
                        <td>"' . $addr1 . '"</td>
                        <td>"' . $addr2 . '"</td>
                        <td>"' . $city . '"</td>
                        <td>"' . $thestate . '"</td>
                        <td>"' . $zip . '"</td>
                        <td>"' . $ihc_country . '"</td>
                        <td>"' . $ecclesia . '"</td>
                        <td>"' . $level_label . '"</td>
                        <td>"' . $start_time . '"</td>
                        <td>"' . $expire_time . '"</td>
                        <td>"' . $is_expired . '"</td>
                        <td>"' . $amount_value . '"</td>
                        <td>"' . $status . '"</td>
                        <td>"' . $create_date . '"</td>
                    </tr>
                ';
            } elseif ($key == 2) {
                $the_lampstand_print_ecclesial .= '
                    <tr>
                        <td>"' . $user_id . '"</td>
                        <td>"' . $first_name . '"</td>
                        <td>"' . $last_name . '"</td>
                        <td>"' . $user_email . '"</td>
                        <td>"' . $addr1 . '"</td>
                        <td>"' . $addr2 . '"</td>
                        <td>"' . $city . '"</td>
                        <td>"' . $thestate . '"</td>
                        <td>"' . $zip . '"</td>
                        <td>"' . $ihc_country . '"</td>
                        <td>"' . $ecclesia . '"</td>
                        <td>"' . $level_label . '"</td>
                        <td>"' . $start_time . '"</td>
                        <td>"' . $expire_time . '"</td>
                        <td>"' . $is_expired . '"</td>
                        <td>"' . $amount_value . '"</td>
                        <td>"' . $status . '"</td>
                        <td>"' . $create_date . '"</td>
                    </tr>
                ';
            } elseif ($key == 3) {
                $The_lampstand_print_home .= '
                    <tr>
                        <td>"' . $user_id . '"</td>
                        <td>"' . $first_name . '"</td>
                        <td>"' . $last_name . '"</td>
                        <td>"' . $user_email . '"</td>
                        <td>"' . $addr1 . '"</td>
                        <td>"' . $addr2 . '"</td>
                        <td>"' . $city . '"</td>
                        <td>"' . $thestate . '"</td>
                        <td>"' . $zip . '"</td>
                        <td>"' . $ihc_country . '"</td>
                        <td>"' . $ecclesia . '"</td>
                        <td>"' . $level_label . '"</td>
                        <td>"' . $start_time . '"</td>
                        <td>"' . $expire_time . '"</td>
                        <td>"' . $is_expired . '"</td>
                        <td>"' . $amount_value . '"</td>
                        <td>"' . $status . '"</td>
                        <td>"' . $create_date . '"</td>
                    </tr>
                ';
            }
        }
    }

    $level_wise_table['The Lampstand Digital'] = $the_lampstand_digital;

    $level_wise_table['The Lampstand Print - Ecclesial'] = $the_lampstand_print_ecclesial;

    $level_wise_table['The Lampstand Print - home'] = $The_lampstand_print_home;

    ob_start();

    // display level wise table
    foreach ($level_wise_table as $key => $table_data) {

?>


        <h4><?php echo $key; ?></h4>
        <div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Address 1</th>
                        <th>Address 2</th>
                        <th>Suburb</th>
                        <th>State</th>
                        <th>Postcode</th>
                        <th>Country</th>
                        <th>Ecclesia</th>
                        <th>Level</th>
                        <th>Start</th>
                        <th>Expiry</th>
                        <th>Expired</th>
                        <th>Paid</th>
                        <th>Status</th>
                        <th>Date Paid</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $table_data; ?>
                </tbody>
            </table>
        </div>

<?php
    }

    $result = ob_get_clean();
    return $result;
}

// add shortcode to display user subscription detail
add_shortcode('display_user_subscription_detail', 'user_subscription_detail');
