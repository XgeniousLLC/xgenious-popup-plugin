<?php
namespace Xgenious\PopupBuilder\Includes;

class Analytics {
    public function record_popup_view() {
        check_ajax_referer('xgenious_popup_nonce', 'nonce');

        $popup_id = isset($_POST['popup_id']) ? intval($_POST['popup_id']) : 0;
        $page_url = isset($_POST['page_url']) ? esc_url_raw($_POST['page_url']) : '';

        if ($popup_id && $page_url) {
            $ip = $this->get_client_ip();
            $country = $this->get_visitor_country($ip);
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

            global $wpdb;
            $table_name = $wpdb->prefix . 'xgenious_popup_analytics';

            $last_24_hours = date('Y-m-d H:i:s', strtotime('-24 hours'));
            $existing_view = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table_name WHERE popup_id = %d AND visitor_ip = %s AND created_at > %s",
                $popup_id, $ip, $last_24_hours
            ));

            $is_unique = $existing_view ? 0 : 1;

            $wpdb->insert(
                $table_name,
                array(
                    'popup_id' => $popup_id,
                    'visitor_ip' => $ip,
                    'visitor_country' => $country,
                    'page_url' => $page_url,
                    'user_agent' => $user_agent,
                    'is_unique' => $is_unique,
                    'created_at' => current_time('mysql')
                ),
                array('%d', '%s', '%s', '%s', '%s', '%d', '%s')
            );

            wp_send_json_success();
        } else {
            wp_send_json_error('Invalid data');
        }
    }

    public function get_popup_views($popup_id, $start_date = null, $end_date = null) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'xgenious_popup_analytics';

        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE popup_id = %d", $popup_id);

        if ($start_date && $end_date) {
            $query .= $wpdb->prepare(" AND created_at BETWEEN %s AND %s", $start_date, $end_date);
        }

        return $wpdb->get_results($query);
    }

    public function get_unique_views_count($popup_id, $start_date = null, $end_date = null) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'xgenious_popup_analytics';

        $query = $wpdb->prepare("SELECT COUNT(DISTINCT visitor_ip) FROM $table_name WHERE popup_id = %d AND is_unique = 1", $popup_id);

        if ($start_date && $end_date) {
            $query .= $wpdb->prepare(" AND created_at BETWEEN %s AND %s", $start_date, $end_date);
        }

        return $wpdb->get_var($query);
    }

    private function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return '0.0.0.0';
    }

    private function get_visitor_country($ip) {
        // This is a placeholder. In a real-world scenario, you'd use a geolocation service or database.
        // For demonstration purposes, we're returning a dummy value.
        return 'UN'; // Unknown
    }
}