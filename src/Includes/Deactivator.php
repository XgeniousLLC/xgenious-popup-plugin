<?php
namespace Xgenious\PopupBuilder\Includes;

class Deactivator {
    public static function deactivate() {
        self::remove_scheduled_events();
        // Add any other deactivation tasks here
    }

    private static function remove_scheduled_events() {
        $timestamp = wp_next_scheduled('xgenious_popup_daily_cleanup');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'xgenious_popup_daily_cleanup');
        }
    }

    // Optionally, you can add a method to remove plugin data
    // Be cautious with this, as it will permanently delete data
    /*
    public static function remove_plugin_data() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}xgenious_popup_settings");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}xgenious_popup_analytics");

        delete_option('xgenious_popup_options');
    }
    */
}