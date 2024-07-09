<?php
namespace Xgenious\PopupBuilder\Includes;

class Activator {
    public static function activate() {
        self::create_tables();
    }

    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $settings_table = $wpdb->prefix . 'xgenious_popup_settings';
        $analytics_table = $wpdb->prefix . 'xgenious_popup_analytics';

        $sql = [];

//        $sql[] = "CREATE TABLE IF NOT EXISTS $settings_table (
//            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
//            popup_id bigint(20) unsigned NOT NULL,
//            setting_key varchar(191) NOT NULL,
//            setting_value longtext NOT NULL,
//            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
//            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//            PRIMARY KEY (id),
//            UNIQUE KEY popup_setting (popup_id, setting_key)
//        ) $charset_collate;";

        $sql[] = "CREATE TABLE IF NOT EXISTS $analytics_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            popup_id bigint(20) unsigned NOT NULL,
            visitor_ip varchar(45) NOT NULL,
            visitor_country varchar(100) NOT NULL,
            device varchar(100) NOT NULL,
            page_url varchar(2083) NOT NULL,
            user_agent varchar(255) NOT NULL,
            browser varchar(50) NOT NULL,
            os varchar(50) NOT NULL,
            is_unique tinyint(1) NOT NULL DEFAULT '1',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY popup_id (popup_id),
            KEY visitor_ip (visitor_ip),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        foreach ($sql as $query) {
            dbDelta($query);
        }
    }
}
