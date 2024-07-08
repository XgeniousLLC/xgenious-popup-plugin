<?php
namespace Xgenious\PopupBuilder\Includes;

class Settings {
    public static function get_setting($popup_id, $key, $default = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'xgenious_popup_settings';
        $value = $wpdb->get_var($wpdb->prepare(
            "SELECT setting_value FROM $table_name WHERE popup_id = %d AND setting_key = %s",
            $popup_id, $key
        ));
        return $value !== null ? $value : $default;
    }

    public static function update_setting($popup_id, $key, $value) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'xgenious_popup_settings';
        $wpdb->replace(
            $table_name,
            array(
                'popup_id' => $popup_id,
                'setting_key' => $key,
                'setting_value' => $value
            ),
            array('%d', '%s', '%s')
        );
    }
}