<?php
namespace Xgenious\PopupBuilder\Includes;

use GeoIp2\Database\Reader;
use WhichBrowser\Parser;

class Display {
    private $geoip_reader;
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_record_popup_view', array($this, 'record_popup_view'));
        add_action('wp_ajax_nopriv_record_popup_view', array($this, 'record_popup_view'));

        $this->geoip_reader = new Reader(XGENIOUS_POPUP_PATH . 'data/GeoLite2-Country.mmdb');
    }

    public function enqueue_styles() {
        wp_enqueue_style('xgenious-popup-public', XGENIOUS_POPUP_URL . 'assets/public/css/popup-public.css', array(), XGENIOUS_POPUP_VERSION, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script('xgenious-popup-public', XGENIOUS_POPUP_URL . 'assets/public/js/popup-public.js', array('jquery'), XGENIOUS_POPUP_VERSION, true);
        wp_localize_script('xgenious-popup-public', 'xgenious_popup', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('xgenious_popup_nonce')
        ));
    }

    public function display_popup() {
        $popups = $this->get_active_popups();

        if (empty($popups)) {
            error_log('No active popups found');
            return;
        }

        foreach ($popups as $popup) {
            $this->render_popup($popup);
        }
    }

    private function get_active_popups() {
        $args = array(
            'post_type' => 'xgenious_popup',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );

        $popup_query = new \WP_Query($args);
        return $popup_query->posts;
    }

    private function render_popup($popup) {
        $popup_id = $popup->ID;
        $content = $this->get_popup_content($popup_id);
        $settings = $this->get_popup_settings($popup_id);

        if ($this->should_display_popup($settings)) {
            ?>
            <div id="xgenious-popup-<?php echo esc_attr($popup_id); ?>"
                 class="xgenious-popup"
                 data-popup-id="<?php echo esc_attr($popup_id); ?>"
                 data-delay="<?php echo esc_attr($settings['delay']); ?>"
                 data-auto-close="<?php echo $settings['close_automatically'] ? 'true' : 'false'; ?>"
                 data-auto-close-time="<?php echo esc_attr($settings['auto_close_time']); ?>"
                 data-end-time="<?php echo esc_attr($settings['end_time']); ?>"
                 style="display:none;">
                <div class="xgenious-popup-overlay"></div>
                <div class="xgenious-popup-content">
                    <button class="xgenious-popup-close">&times;</button>
                    <?php echo $content; ?>
                    <?php if ($settings['close_automatically']): ?>
                        <div class="xgenious-popup-progress-bar">
                            <div class="xgenious-popup-progress"></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <script>
                jQuery(document).ready(function($) {
                    var popupId = '<?php echo esc_js($popup_id); ?>';
                    var delay = <?php echo esc_js($settings['delay']); ?>;
                    var autoClose = <?php echo $settings['close_automatically'] ? 'true' : 'false'; ?>;
                    var autoCloseTime = <?php echo esc_js($settings['auto_close_time']); ?>;
                    var endTime = '<?php echo esc_js($settings['end_time']); ?>';

                    setTimeout(function() {
                        if (new Date() < new Date(endTime) || !endTime) {
                            var $popup = $('#xgenious-popup-' + popupId);
                            $popup.show();
                            $.ajax({
                                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                type: 'POST',
                                data: {
                                    action: 'record_popup_view',
                                    popup_id: popupId,
                                    nonce: '<?php echo wp_create_nonce('record_popup_view_nonce'); ?>'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        console.log('Success:', response.data);
                                    } else {
                                        console.error('Error:', response.data);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('AJAX error:', status, error);
                                }
                            });

                            if (autoClose && autoCloseTime > 0) {
                                var $progress = $popup.find('.xgenious-popup-progress');
                                $progress.animate({
                                    width: '100%'
                                }, autoCloseTime * 1000, 'linear', function() {
                                    $popup.hide();
                                });
                            }
                        }
                    }, delay * 1000);

                    $('#xgenious-popup-' + popupId + ' .xgenious-popup-close').on('click', function() {
                        $('#xgenious-popup-' + popupId).hide();
                    });
                });
            </script>
            <?php
        }
    }

    private function get_popup_content($popup_id) {
        $popup = get_post($popup_id);
        $content = do_shortcode($popup->post_content);

        // If using Elementor
        if (class_exists('\Elementor\Plugin')) {
            $content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($popup_id);
        }

        return $content;
    }



    private function should_display_popup($settings) {
        // Check display conditions
        if (!empty($settings['end_time']) && new \DateTime() > new \DateTime($settings['end_time'])) {
            return false;
        }

        // Check display conditions
        if ($settings['display_on'] === 'all') {
            return true;
        } elseif ($settings['display_on'] === 'specific' && is_singular()) {
            $current_page_id = get_the_ID();
            return in_array($current_page_id, (array)$settings['specific_pages']);
        }

        return false;
    }

    private function get_popup_settings($popup_id) {
        $defaults = array(
            'delay' => 0,
            'display_on' => 'all',
            'specific_pages' => array(),
            'end_time' => '',
            'close_automatically' => false,
            'auto_close_time' => 0,
        );

        $settings = array(
            'delay' => get_post_meta($popup_id, '_popup_delay', true),
            'display_on' => get_post_meta($popup_id, '_popup_display_on', true),
            'specific_pages' => get_post_meta($popup_id, '_popup_specific_pages', true),
            'end_time' => get_post_meta($popup_id, '_popup_end_time', true),
            'close_automatically' => get_post_meta($popup_id, '_popup_close_automatically', true) === 'on',
            'auto_close_time' => get_post_meta($popup_id, '_popup_auto_close_time', true),
        );

        return wp_parse_args($settings, $defaults);
    }

    public function record_popup_view() {
        if (!check_ajax_referer('record_popup_view_nonce', 'nonce', false)) {
            wp_send_json_error('Nonce verification failed');
            return;
        }

        $popup_id = isset($_POST['popup_id']) ? intval($_POST['popup_id']) : 0;

        if (!$popup_id) {
            wp_send_json_error('Invalid popup ID');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'xgenious_popup_analytics';

        $visitor_ip = $this->get_client_ip();
        $current_time = current_time('mysql');
        $last_24_hours = date('Y-m-d H:i:s', strtotime('-24 hours'));

        // Check for existing view in last 24 hours
        $existing_view = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE popup_id = %d AND visitor_ip = %s AND created_at > %s",
            $popup_id, $visitor_ip, $last_24_hours
        ));

        $is_unique = $existing_view ? 0 : 1;

        $visitor_info = $this->get_visitor_info();

        $result = $wpdb->insert(
            $table_name,
            array(
                'popup_id' => $popup_id,
                'visitor_ip' => $visitor_info['ip'],
                'visitor_country' => $visitor_info['country'],
                'page_url' => isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : '',
                'user_agent' => $visitor_info['user_agent'],
                'browser' => $visitor_info['browser'],
                'os' => $visitor_info['os'],
                'device' => $visitor_info['device'],
                'is_unique' => 1, // You might want to implement logic to determine if it's a unique view
                'created_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s')
        );


        if ($result === false) {
            wp_send_json_error('Database insert failed: ' . $wpdb->last_error);
        } else {
            wp_send_json_success('Data inserted successfully. Insert ID: ' . $wpdb->insert_id);
        }
    }
    private function get_visitor_info() {
        $ip = $this->get_client_ip();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        try {
            $record = $this->geoip_reader->country($ip);
            $country = $record->country->isoCode;
        } catch (\Exception $e) {
            $country = 'Unknown';
        }

        $parser = new Parser($user_agent);

        return [
            'ip' => $ip,
            'country' => $country,
            'user_agent' => $user_agent,
            'browser' => $parser->browser->name ?? 'Unknown',
            'os' => $parser->os->name ?? 'Unknown',
            'device' => $this->get_device_type($parser),
        ];
    }
    private function get_device_type($parser) {
        if ($parser->isType('mobile')) {
            return 'Mobile';
        } elseif ($parser->isType('tablet')) {
            return 'Tablet';
        } elseif ($parser->isType('desktop')) {
            return 'Desktop';
        } else {
            return 'Other';
        }
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

    private function get_visitor_country() {
        // Implement your logic to get visitor country
        // You might want to use a geolocation service or database
        return 'Unknown';
    }

}