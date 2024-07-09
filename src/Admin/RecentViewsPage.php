<?php
namespace Xgenious\PopupBuilder\Admin;

class RecentViewsPage {
    public function render_page() {
        $popup_id = isset($_GET['popup_id']) ? intval($_GET['popup_id']) : 0;
        $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        $per_page = 20;

        global $wpdb;
        $table_name = $wpdb->prefix . 'xgenious_popup_analytics';

        $total_items = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE popup_id = %d", $popup_id));
        $total_pages = ceil($total_items / $per_page);

        $offset = ($paged - 1) * $per_page;

        $recent_views = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE popup_id = %d ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $popup_id, $per_page, $offset
        ));

        ?>
        <div class="wrap">
            <h1>Recent Views for: <?php echo get_the_title($popup_id); ?></h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>IP</th>
                    <th>Country</th>
                    <th>Browser</th>
                    <th>OS</th>
                    <th>Device</th>
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
                        <td><?php echo $view->os; ?></td>
                        <td><?php echo $view->device; ?></td>
                        <td><?php echo $view->page_url; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <?php
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $paged
                    ));
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
}