<?php
/**
 * Uninstall cleanup for OneApp Universal Payment Handler.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'oneapp_public_key' );
delete_option( 'oneapp_form_title' );
delete_option( 'oneapp_form_desc' );
delete_option( 'oneapp_form_btn' );
