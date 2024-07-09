<?php
namespace Xgenious\PopupBuilder\Admin;

class AnalyticsPage {
    public function render_analytics_page() {
        if (isset($_GET['popup_id'])) {
            $this->render_single_popup_analytics($_GET['popup_id']);
        } else {
            $this->render_all_popups_analytics();
        }
    }

    private function render_all_popups_analytics() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'xgenious_popup_analytics';

        $analytics = $wpdb->get_results("
            SELECT 
                popup_id, 
                COUNT(*) as view_count, 
                COUNT(DISTINCT visitor_ip) as unique_views
            FROM $table_name
            GROUP BY popup_id
        ");

        ?>
        <div class="wrap">
            <h2>Popup Analytics Overview</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                <tr>
                    <th>Popup</th>
                    <th>Total Views</th>
                    <th>Unique Views</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($analytics as $row): ?>
                    <tr>
                        <td><?php echo get_the_title($row->popup_id); ?></td>
                        <td><?php echo $row->view_count; ?></td>
                        <td><?php echo $row->unique_views; ?></td>
                        <td>
                            <a href="<?php echo add_query_arg('popup_id', $row->popup_id); ?>" class="button button-secondary">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    private function render_single_popup_analytics($popup_id) {
        $date_range = isset($_GET['date_range']) ? $_GET['date_range'] : 'all';
        $start_date = $this->get_start_date($date_range);
        $end_date = current_time('Y-m-d H:i:s');

        global $wpdb;
        $table_name = $wpdb->prefix . 'xgenious_popup_analytics';
        $link_clicks_table = $wpdb->prefix . 'xgenious_popup_link_clicks';


        $where_clause = $wpdb->prepare("WHERE popup_id = %d", $popup_id);
        if ($start_date) {
            $where_clause .= $wpdb->prepare(" AND created_at BETWEEN %s AND %s", $start_date, $end_date);
        }

        $analytics = $wpdb->get_row("
            SELECT 
                COUNT(*) as view_count, 
                COUNT(DISTINCT visitor_ip) as unique_views,
                GROUP_CONCAT(DISTINCT visitor_country) as countries,
                GROUP_CONCAT(DISTINCT browser) as browsers,
                GROUP_CONCAT(DISTINCT os) as operating_systems,
                GROUP_CONCAT(DISTINCT device) as devices
            FROM $table_name
            $where_clause
        ");

        $recent_views = $wpdb->get_results("
            SELECT *
            FROM $table_name
            $where_clause
            ORDER BY created_at DESC
            LIMIT 10
        ");

        $device_data = $wpdb->get_results("
            SELECT device, COUNT(*) as count
            FROM $table_name
            $where_clause
            GROUP BY device
        ");

        $browser_data = $wpdb->get_results("
            SELECT browser, COUNT(*) as count
            FROM $table_name
            $where_clause
            GROUP BY browser
        ");

        $daily_views = $wpdb->get_results("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM $table_name
            $where_clause
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");

        $link_clicks = $wpdb->get_results($wpdb->prepare(
            "SELECT link_url, COUNT(*) as click_count
         FROM $link_clicks_table
         WHERE popup_id = %d
         GROUP BY link_url
         ORDER BY click_count DESC",
            $popup_id
        ));

        $total_link_clicks = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $link_clicks_table WHERE popup_id = %d",
            $popup_id
        ));

        ?>
        <div class="wrap">
            <h2>Analytics for: <?php echo get_the_title($popup_id); ?></h2>
            <a href="<?php echo remove_query_arg('popup_id'); ?>" class="button button-secondary">Back to Overview</a>
            <a href="<?php echo admin_url('admin.php?page=xgenious-popup-recent-views&popup_id=' . $popup_id); ?>" class="button button-secondary">View All Recent Views</a>

            <h3>Date Range</h3>
            <form method="get">
                <input type="hidden" name="page" value="xgenious-popup-builder">
                <input type="hidden" name="tab" value="analytics">
                <input type="hidden" name="popup_id" value="<?php echo $popup_id; ?>">
                <select name="date_range" onchange="this.form.submit()">
                    <option value="all" <?php selected($date_range, 'all'); ?>>All Time</option>
                    <option value="today" <?php selected($date_range, 'today'); ?>>Today</option>
                    <option value="yesterday" <?php selected($date_range, 'yesterday'); ?>>Yesterday</option>
                    <option value="last7days" <?php selected($date_range, 'last7days'); ?>>Last 7 Days</option>
                    <option value="last30days" <?php selected($date_range, 'last30days'); ?>>Last 30 Days</option>
                    <option value="thisyear" <?php selected($date_range, 'thisyear'); ?>>This Year</option>
                </select>
            </form>

            <h3>Summary</h3>
            <table class="wp-list-table widefat fixed striped">
                <tr>
                    <th>Total Views</th>
                    <td><?php echo $analytics->view_count; ?></td>
                </tr>
                <tr>
                    <th>Unique Views</th>
                    <td><?php echo $analytics->unique_views; ?></td>
                </tr>
                <tr>
                    <th>Countries</th>
                    <td><?php echo str_replace(',', ', ', $analytics->countries); ?></td>
                </tr>
                <tr>
                    <th>Browsers</th>
                    <td><?php echo str_replace(',', ', ', $analytics->browsers); ?></td>
                </tr>
                <tr>
                    <th>Operating Systems</th>
                    <td><?php echo str_replace(',', ', ', $analytics->operating_systems); ?></td>
                </tr>
                <tr>
                    <th>Devices</th>
                    <td><?php echo str_replace(',', ', ', $analytics->devices); ?></td>
                </tr>
            </table>
            <h3>Link Click Analytics</h3>
            <p>Total Link Clicks: <?php echo $total_link_clicks; ?></p>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                <tr>
                    <th>Link URL</th>
                    <th>Click Count</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($link_clicks as $click): ?>
                    <tr>
                        <td><?php echo esc_url($click->link_url); ?></td>
                        <td><?php echo $click->click_count; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Recent Views [last 10]</h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>IP</th>
                    <th>Country</th>
                    <th>Browser</th>
                    <th>Device</th>
                    <th>OS</th>
                    <th>Page URL</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($recent_views as $view): ?>
                    <tr>
                        <td><?php echo $view->created_at; ?></td>
                        <td><?php echo $view->visitor_ip; ?></td>
                        <td><?php echo $view->visitor_country; ?></td>
                        <td><?php echo $view->browser; ?></td>
                        <td><?php echo $view->device; ?></td>
                        <td><?php echo $view->os; ?></td>
                        <td><?php echo $view->page_url; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div style="display: flex; justify-content: space-between;">
                <div style="width: 45%;">
                    <h3>Device Distribution</h3>
                    <canvas id="deviceChart"></canvas>
                </div>
                <div style="width: 45%;">
                    <h3>Browser Distribution</h3>
                    <canvas id="browserChart"></canvas>
                </div>
            </div>

            <div style="margin-top: 30px;">
                <h3>Daily Views</h3>
                <canvas id="dailyViewsChart"></canvas>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Device Chart
                    new Chart(document.getElementById('deviceChart'), {
                        type: 'pie',
                        data: {
                            labels: <?php echo json_encode(wp_list_pluck($device_data, 'device')); ?>,
                            datasets: [{
                                data: <?php echo json_encode(wp_list_pluck($device_data, 'count')); ?>,
                                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                            }]
                        }
                    });

                    // Browser Chart
                    new Chart(document.getElementById('browserChart'), {
                        type: 'pie',
                        data: {
                            labels: <?php echo json_encode(wp_list_pluck($browser_data, 'browser')); ?>,
                            datasets: [{
                                data: <?php echo json_encode(wp_list_pluck($browser_data, 'count')); ?>,
                                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                            }]
                        }
                    });

                    // Daily Views Chart
                    new Chart(document.getElementById('dailyViewsChart'), {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode(wp_list_pluck($daily_views, 'date')); ?>,
                            datasets: [{
                                label: 'Daily Views',
                                data: <?php echo json_encode(wp_list_pluck($daily_views, 'count')); ?>,
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgb(54, 162, 235)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
            </script>
        </div>
        <?php
    }
    private function get_start_date($date_range) {
        $now = current_time('Y-m-d H:i:s');
        switch ($date_range) {
            case 'today':
                return date('Y-m-d 00:00:00', strtotime($now));
            case 'yesterday':
                return date('Y-m-d 00:00:00', strtotime('-1 day', strtotime($now)));
            case 'last7days':
                return date('Y-m-d H:i:s', strtotime('-7 days', strtotime($now)));
            case 'last30days':
                return date('Y-m-d H:i:s', strtotime('-30 days', strtotime($now)));
            case 'thisyear':
                return date('Y-01-01 00:00:00', strtotime($now));
            default:
                return null;
        }
    }
}