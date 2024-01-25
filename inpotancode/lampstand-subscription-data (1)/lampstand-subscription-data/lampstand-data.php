<?php

/**
 *  @since             1.0.0
 * 
 * @wordpress-plugin
 * Plugin Name:        Lampstand Subscription Data 
 * Description:        This plugin allows to get data from a subscription system and display using shortcode.
 * Version:            1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Get user subscription data
function lampstand_user_subscription_data()
{

    // Subscription Levels
    $subscription_levels = [
        '4' => ['subscription_levels_name'  => 'The Lampstand Digital'],
        '2' => ['subscription_levels_name'  => 'The Lampstand Print - Ecclesial'],
        '3' => ['subscription_levels_name'  => 'The Lampstand Print - Home'],
    ];

    // User Data
    $user_data = [];

    foreach ($subscription_levels as $key => $subscription_level) {

        /*
         * Get user ids from the subscription level
         */
        $get_user_id_url = 'https://thelampstand.com.au/?ihc_action=api-gate&ihch=Q5O4VGgYvUOY96pehI0vyKp4Cgvx1mG28e&action=get_level_users&lid=' . $key;

        if (is_wp_error($get_user_id_url)) {
            return false;
        }

        $user_id_body = api_call($get_user_id_url);

        // check if response is empty or not
        if (!empty($user_id_body->response)) {

            foreach ($user_id_body->response as $response) {

                if (!isset($response->username)) {
                    continue;
                }

                // get the user id
                $user_id = $response->user_id;

                /*
                 * get user levels from the user id
                 */
                $get_user_levels_url = 'https://thelampstand.com.au/?ihc_action=api-gate&ihch=Q5O4VGgYvUOY96pehI0vyKp4Cgvx1mG28e&action=get_user_levels&uid=' . $user_id;

                $user_level_body = api_call($get_user_levels_url);

                if (empty($user_level_body->response)) {
                    continue;
                }

                $user_data[$key][$user_id]['level_details'] = $user_level_body->response->{$key};

                /**
                 * get user detail from the user id
                 */
                $get_user_data_url = 'https://thelampstand.com.au/?ihc_action=api-gate&ihch=Q5O4VGgYvUOY96pehI0vyKp4Cgvx1mG28e&action=user_get_details&uid=' . $user_id;

                $user_detail_body = api_call($get_user_data_url);

                if (empty($user_detail_body->response)) {
                    continue;
                }

                $user_data[$key][$user_id]['user_details'] = $user_detail_body->response;

                /*
                 * get order listing from the user id
                 */
                $get_order_listing_url = 'https://thelampstand.com.au/?ihc_action=api-gate&ihch=Q5O4VGgYvUOY96pehI0vyKp4Cgvx1mG28e&action=orders_listing&limit=1&uid=' . $user_id;

                $order_listing_body = api_call($get_order_listing_url);

                if (empty($order_listing_body->response)) {
                    continue;
                }

                $order_ids = [];


                foreach ($order_listing_body->response as $order_response) {
                    // get the order id
                    $order_ids[] = $order_response->id;
                }

                /*
                 * get order data from the order id
                 */
                if (!empty($order_ids)) {

                    foreach ($order_ids as $order_id) {

                        $get_order_data_url = 'https://thelampstand.com.au/?ihc_action=api-gate&ihch=Q5O4VGgYvUOY96pehI0vyKp4Cgvx1mG28e&action=order_get_data&order_id=' . $order_id;

                        $order_data_body = api_call($get_order_data_url);

                        if (empty($order_data_body->response)) {
                            continue;
                        }
                    }
                }

                $user_data[$key][$user_id]['order_detail'] = $order_data_body->response;
            }
        }
    }
    return $user_data;
}

/**
 * GET METHOD API
 * @param String $url API url
 * @return Object $api_response Returning data which is getting my API
 */
function api_call(String $url): Object
{
    $api_call = wp_remote_get($url);

    $api_response_json = wp_remote_retrieve_body($api_call);

    $api_response = json_decode($api_response_json);

    return $api_response;
}

require_once(plugin_dir_path(__FILE__) . 'views/lampstand-subscription-data.php');
