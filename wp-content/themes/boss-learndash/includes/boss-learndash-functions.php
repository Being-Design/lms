<?php

/**
 * Output LearnDash Payment buttons
 *
 * @uses learndash_get_function()
 * @uses sfwd_lms_has_access()
 *
 * @param  id|obj 	$course course id or WP_Post course object
 * @return string   output of payment buttons
 */
function boss_edu_payment_buttons( $course ) {
    if ( is_numeric( $course ) ) {
        $course_id = $course;
        $course = get_post( $course_id );
    } else if ( ! empty( $course->ID ) ) {
        $course_id = $course->ID;
    } else {
        return '';
    }

    $user_id = get_current_user_id();

    if ( $course->post_type != 'sfwd-courses' ) {
        return '';
    }

    $meta = get_post_meta( $course_id, '_sfwd-courses', true );
    $course_price_type = @$meta['sfwd-courses_course_price_type'];
    $course_price = @$meta['sfwd-courses_course_price'];
    $course_no_of_cycles = @$meta['sfwd-courses_course_no_of_cycles'];
    $course_price = @$meta['sfwd-courses_course_price'];
    $custom_button_url = @$meta['sfwd-courses_custom_button_url'];

    // format the Course price to be proper XXX.YY no leading dollar signs or other values.
    if ( $course_price_type == 'paynow' ) {
        if ( $course_price != '' ) {
            $course_price = preg_replace( "/[^0-9.]/", '', $course_price );
            $course_price = number_format( floatval( $course_price ), 2, '.', '' );
        }
    }

    //$courses_options = learndash_get_option( 'sfwd-courses' );

    //if ( ! empty( $courses_options ) ) {
    //	extract( $courses_options );
    //}

    $paypal_settings = LearnDash_Settings_Section::get_section_settings_all( 'LearnDash_Settings_Section_PayPal' );
    if ( ! empty( $paypal_settings ) ) {
        $paypal_settings['paypal_sandbox'] = $paypal_settings['paypal_sandbox'] == 'yes' ? 1 : 0;
    }

    if ( sfwd_lms_has_access( $course->ID, $user_id ) ) {
        return '';
    }

    $button_text = LearnDash_Custom_Label::get_label( 'button_take_this_course' );

    if ( ! empty( $course_price_type ) && $course_price_type == 'closed' ) {

        // Replace "Take this Course" button text with "$10 Purchase this Course"
        $product_id = url_to_postid( $custom_button_url );
        if ( class_exists( 'WooCommerce' ) && $product = wc_get_product( $product_id ) ) {
            $button_text = sprintf( __( '%1$s%2$s - Purchase This Course', 'boss-learndash' ), get_woocommerce_currency_symbol(), $product->get_price() );
        }

        if ( empty( $custom_button_url) ) {
            $custom_button = '';
        } else {
            if ( ! strpos( $custom_button_url, '://' ) ) {
                $custom_button_url = 'http://'.$custom_button_url;
            }

            $custom_button = '<a class="btn-join" href="'.$custom_button_url.'" id="btn-join">'. $button_text .'</a>';
        }

        $payment_params = array(
            'custom_button_url' => $custom_button_url,
            'post' => $course
        );

        /**
         * Filter a closed course payment button
         *
         * @since 2.1.0
         *
         * @param  string  $custom_button
         */
        return 	apply_filters( 'learndash_payment_closed_button', $custom_button, $payment_params );

    } else if ( ! empty( $course_price ) ) {
        include_once( LEARNDASH_LMS_PLUGIN_DIR. 'includes/vendor/paypal/enhanced-paypal-shortcodes.php' );

        $paypal_button = '';

        if ( ! empty( $paypal_settings['paypal_email'] ) ) {

            $post_title = str_replace(array('[', ']'), array('', ''), $course->post_title);

            if ( empty( $course_price_type ) || $course_price_type == 'paynow' ) {
                $shortcode_content = do_shortcode( '[paypal type="paynow" amount="'. $course_price .'" sandbox="'. $paypal_settings['paypal_sandbox'] .'" email="'. $paypal_settings['paypal_email'] .'" itemno="'. $course->ID .'" name="'. $post_title .'" noshipping="1" nonote="1" qty="1" currencycode="'. $paypal_settings['paypal_currency'] .'" rm="2" notifyurl="'. $paypal_settings['paypal_notifyurl'] .'" returnurl="'. $paypal_settings['paypal_returnurl'] .'" imagewidth="100px" pagestyle="paypal" lc="'. $paypal_settings['paypal_country'] .'" cbt="'. __( 'Complete Your Purchase', 'learndash' ) . '" custom="'. $user_id. '"]' );
                if (!empty( $shortcode_content ) ) {
                    $paypal_button = wptexturize( '<div class="learndash_checkout_button learndash_paypal_button">'. $shortcode_content .'</div>');
                }

            } else if ( $course_price_type == 'subscribe' ) {
                $course_price_billing_p3 = get_post_meta( $course_id, 'course_price_billing_p3',  true );
                $course_price_billing_t3 = get_post_meta( $course_id, 'course_price_billing_t3',  true );
                $srt = intval( $course_no_of_cycles );

                $shortcode_content = do_shortcode( '[paypal type="subscribe" a3="'. $course_price .'" p3="'. $course_price_billing_p3 .'" t3="'. $course_price_billing_t3 .'" sandbox="'. $paypal_settings['paypal_sandbox'] .'" email="'. $paypal_settings['paypal_email'] .'" itemno="'. $course->ID .'" name="'. $post_title .'" noshipping="1" nonote="1" qty="1" currencycode="'. $paypal_settings['paypal_currency'] .'" rm="2" notifyurl="'. $paypal_settings['paypal_notifyurl'] .'" returnurl="'. $paypal_settings['paypal_returnurl'] .'" imagewidth="100px" pagestyle="paypal" lc="'. $paypal_settings['paypal_country'] .'" cbt="'. __( 'Complete Your Purchase', 'learndash' ) .'" custom="'. $user_id .'" srt="'. $srt .'"]' );

                if (!empty( $shortcode_content ) ) {
                    $paypal_button = wptexturize( '<div class="learndash_checkout_button learndash_paypal_button">'. $shortcode_content .'</div>' );
                }
            }
        }

        $payment_params = array(
            'price' => $course_price,
            'post' => $course,
        );

        /**
         * Filter PayPal payment button
         *
         * @since 2.1.0
         *
         * @param  string  $paypal_button
         */
        $payment_buttons = apply_filters( 'learndash_payment_button', $paypal_button, $payment_params );

        if ( ! empty( $payment_buttons ) ) {

            // Rack up "Take this Course" button label with the Course Price
            $label_button_text  = LearnDash_Custom_Label::get_label( 'button_take_this_course' );
            $price_button_text  = sprintf( __( '%1$s - Purchase This Course', 'boss-learndash' ), $payment_params['price'] );

            if ( ( !empty( $paypal_button ) ) && ( $payment_buttons != $paypal_button ) ) {

                // Replace "Take this Course" button text with "$10 Purchase this Course"
                $payment_buttons    = str_replace( $label_button_text, $price_button_text, $payment_buttons );

                $button = 	'';
                $button .= 	'<div id="learndash_checkout_buttons_course_'. $course->ID .'" class="learndash_checkout_buttons">';
                $button .= 		'<input id="btn-join-'. $course->ID .'" class="btn-join btn-join-'. $course->ID .' button learndash_checkout_button" data-jq-dropdown="#jq-dropdown-'. $course->ID .'" type="button" value="'. $button_text .'" />';
                $button .= 	'</div>';

                global $dropdown_button;
                $dropdown_button .= 	'<div id="jq-dropdown-'. $course->ID .'" class="jq-dropdown jq-dropdown-tip checkout-dropdown-button">';
                $dropdown_button .= 		'<ul class="jq-dropdown-menu">';
                $dropdown_button .= 		'<li>';
                $dropdown_button .= 			str_replace($button_text, __('Use Paypal', 'learndash'), $payment_buttons);
                $dropdown_button .= 		'</li>';
                $dropdown_button .= 		'</ul>';
                $dropdown_button .= 	'</div>';

                return apply_filters( 'learndash_dropdown_payment_button', $button );

            } else {
                // Replace "Take this Course" button text with "$10 Purchase this Course"
                $payment_buttons    = str_replace( $label_button_text, $price_button_text, $payment_buttons );
                return	'<div id="learndash_checkout_buttons_course_'. $course->ID .'" class="learndash_checkout_buttons">'. $payment_buttons .'</div>';
            }
        }
    } else {
        $join_button = '<div class="learndash_join_button"><form method="post">
							<input type="hidden" value="'. $course->ID .'" name="course_id" />
							<input type="hidden" name="course_join" value="'. wp_create_nonce( 'course_join_'. get_current_user_id() .'_'. $course->ID ) .'" />
							<input type="submit" value="'.$button_text.'" class="btn-join" id="btn-join" />
						</form></div>';

        $payment_params = array(
            'price' => '0',
            'post' => $course,
            'course_price_type' => $course_price_type
        );

        /**
         * Filter Join payment button
         *
         * @since 2.1.0
         *
         * @param  string  $join_button
         */
        $payment_buttons = apply_filters( 'learndash_payment_button', $join_button, $payment_params );
        return $payment_buttons;
    }

}