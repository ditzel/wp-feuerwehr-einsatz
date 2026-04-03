<?php
/*
Plugin Name: Feuerwehr Einsatz Management
Description: Ein benutzerdefiniertes Plugin zum Erfassen und Anzeigen von Feuerwehr-Einsätzen über Gutenberg-Blöcke.
Version: 1.0
Author: Daniel Erdmann
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// 1. Activation Hook: Create Table
register_activation_hook(__FILE__, 'ff_einsatz_create_table');
function ff_einsatz_create_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ff_einsatz';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        datum date NOT NULL,
        start time NOT NULL,
        ende time NOT NULL,
        einsatzart varchar(255) NOT NULL,
        ort varchar(255) NOT NULL,
        langtext text NOT NULL,
        pressemitteilung varchar(255) DEFAULT '',
        instagram varchar(255) DEFAULT '',
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// 2. Enqueue Editor & Frontend Assets
add_action('init', 'ff_einsatz_register_blocks');
function ff_einsatz_register_blocks()
{
    // Editor script
    wp_register_script(
        'ff-einsatz-editor-script',
        plugins_url('blocks.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
        filemtime(plugin_dir_path(__FILE__) . 'blocks.js')
    );

    // Frontend & Editor styles
    wp_register_style(
        'ff-einsatz-style',
        plugins_url('style.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'style.css')
    );

    // Register Form Block
    register_block_type('ff/einsatz-form', array(
        'editor_script' => 'ff-einsatz-editor-script',
        'style' => 'ff-einsatz-style',
        'render_callback' => 'ff_einsatz_render_form_block'
    ));

    // Register List Block
    register_block_type('ff/einsatz-list', array(
        'editor_script' => 'ff-einsatz-editor-script',
        'style' => 'ff-einsatz-style',
        'render_callback' => 'ff_einsatz_render_list_block'
    ));
}

// Frontend Script (only enqueue when block used)
add_action('wp_enqueue_scripts', 'ff_einsatz_frontend_scripts');
function ff_einsatz_frontend_scripts()
{
    wp_register_script(
        'ff-einsatz-frontend',
        plugins_url('frontend.js', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'frontend.js'),
        true
    );

    // Pass REST URL and Nonce to frontend
    wp_localize_script('ff-einsatz-frontend', 'ffEinsatz', array(
        'rest_url' => esc_url_raw(rest_url()),
        'nonce' => wp_create_nonce('wp_rest')
    ));
}

// 3. Render Callbacks
function ff_einsatz_render_form_block($attributes, $content)
{
    if (!is_user_logged_in()) {
        return '<p class="ff-error">Sie müssen angemeldet sein, um einen Einsatz zu erfassen.</p>';
    }

    wp_enqueue_script('ff-einsatz-frontend');

    ob_start();
    ?>
    <div class="ff-einsatz-form-container">
        <h3>Neuen Einsatz erfassen</h3>
        <form id="ff-einsatz-form" class="ff-form">
            <div class="ff-form-group">
                <label for="ff_datum">Datum</label>
                <input type="date" id="ff_datum" name="datum" required>
            </div>

            <div class="ff-form-group">
                <label for="ff_start">Startzeit</label>
                <input type="time" id="ff_start" name="start" required>
            </div>

            <div class="ff-form-group">
                <label for="ff_ende">Endzeit</label>
                <input type="time" id="ff_ende" name="ende" required>
            </div>

            <div class="ff-form-group">
                <label for="ff_einsatzart">Einsatzart</label>
                <input type="text" id="ff_einsatzart" name="einsatzart" placeholder="z.B. Brandeinsatz" required>
            </div>

            <div class="ff-form-group">
                <label for="ff_ort">Ort</label>
                <input type="text" id="ff_ort" name="ort" placeholder="Einsatzort" required>
            </div>

            <div class="ff-form-group">
                <label for="ff_langtext">Beschreibung (Langtext)</label>
                <textarea id="ff_langtext" name="langtext" rows="5" required></textarea>
            </div>

            <div class="ff-form-group">
                <label for="ff_pressemitteilung">Pressemitteilung (Link)</label>
                <input type="url" id="ff_pressemitteilung" name="pressemitteilung" placeholder="https://...">
            </div>

            <div class="ff-form-group">
                <label for="ff_instagram">Instagram (Link)</label>
                <input type="url" id="ff_instagram" name="instagram" placeholder="https://instagram.com/...">
            </div>

            <button type="submit" class="ff-submit-btn">Einsatz speichern</button>
            <div id="ff-form-message"></div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}

function ff_einsatz_render_list_block( $attributes, $content ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ff_einsatz';
    
    // Check if table exists (avoids error if block previewed before plugin completely activated)
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        return '<p>Datenbanktabelle noch nicht angelegt.</p>';
    }

    $limit = isset($attributes['limit']) ? intval($attributes['limit']) : 0;
    $showYearFilter = !empty($attributes['showYearFilter']);

    // Handle Year filtering via GET parameter
    $selected_year = isset($_GET['ff_jahr']) ? intval($_GET['ff_jahr']) : 0;

    // Use current year as default if filter is active
    if ($showYearFilter && $selected_year === 0) {
        $selected_year = intval(date('Y'));
    }
    
    $where = "1=1";
    if ( $selected_year > 0 ) {
        $where .= $wpdb->prepare(" AND YEAR(datum) = %d", $selected_year);
    }
    
    $query = "SELECT * FROM $table_name WHERE $where ORDER BY datum DESC, start DESC";
    if ( $limit > 0 ) {
        $query .= $wpdb->prepare(" LIMIT %d", $limit);
    }

    $results = $wpdb->get_results( $query );

    // Get all available years for the dropdown
    $years = [];
    if ( $showYearFilter ) {
        $years_result = $wpdb->get_col("SELECT DISTINCT YEAR(datum) FROM $table_name ORDER BY YEAR(datum) DESC");
        if ($years_result) {
            $years = array_map('intval', $years_result);
        }
        $current_year = intval(date('Y'));
        if (!in_array($current_year, $years)) {
            $years[] = $current_year;
            rsort($years);
        }
    }

    ob_start();
    ?>
    <div class="ff-einsatz-list-container">
        <?php if ( $showYearFilter && !empty($years) ) : ?>
        <div class="ff-filter-container" style="margin-bottom: 20px;">
            <form method="GET" class="ff-year-filter-form">
                <label for="ff_jahr" style="font-weight: 600; margin-right: 10px;">Jahr:</label>
                <select name="ff_jahr" id="ff_jahr" onchange="this.form.submit()" style="padding: 5px 10px; border-radius: 4px; border: 1px solid #ccc;">
                    <?php foreach ($years as $yr) : ?>
                        <option value="<?php echo esc_attr($yr); ?>" <?php selected($selected_year, $yr); ?>><?php echo esc_html($yr); ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <?php endif; ?>

        <table class="ff-einsatz-table">
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Zeit</th>
                    <th>Einsatzart</th>
                    <th>Ort</th>
                    <th>Meldung</th>
                    <th>Presse</th>
                    <th>Instagram</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( $results ) : ?>
                    <?php foreach ( $results as $row ) : ?>
                    <tr>
                        <td><strong><?php echo esc_html( date( 'd.m.Y', strtotime( $row->datum ) ) ); ?></strong></td>
                        <td><?php echo esc_html( date( 'H:i', strtotime( $row->start ) ) ) . ' - ' . esc_html( date( 'H:i', strtotime( $row->ende ) ) ); ?></td>
                        <td><span class="ff-badge"><?php echo esc_html( $row->einsatzart ); ?></span></td>
                        <td><?php echo esc_html( $row->ort ); ?></td>
                        <td><?php echo nl2br( esc_html( $row->langtext ) ); ?></td>
                        <td>
                            <?php if ( ! empty( $row->pressemitteilung ) ) : ?>
                                <a href="<?php echo esc_url( $row->pressemitteilung ); ?>" target="_blank" rel="noopener noreferrer">Link</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( ! empty( $row->instagram ) ) : ?>
                                <a href="<?php echo esc_url( $row->instagram ); ?>" target="_blank" rel="noopener noreferrer" class="ff-insta-link">
                                    <img src="<?php echo esc_url( plugins_url('insta.svg', __FILE__) ); ?>" alt="Instagram" class="ff-insta-icon" style="width: 24px; height: 24px;" />
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7">Keine Einsätze gefunden.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}

// 4. REST API Endpoint
add_action('rest_api_init', function () {
    register_rest_route('ff/v1', '/einsatz', array(
        'methods' => 'POST',
        'callback' => 'ff_einsatz_rest_handler',
        'permission_callback' => 'ff_einsatz_permissions_check',
    ));
});

function ff_einsatz_permissions_check()
{
    return is_user_logged_in(); // Only logged-in users can post
}

function ff_einsatz_rest_handler(WP_REST_Request $request)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ff_einsatz';

    $params = $request->get_json_params() ?: $request->get_body_params();

    $datum = sanitize_text_field($params['datum'] ?? '');
    $start = sanitize_text_field($params['start'] ?? '');
    $ende = sanitize_text_field($params['ende'] ?? '');
    $einsatzart = sanitize_text_field($params['einsatzart'] ?? '');
    $ort = sanitize_text_field($params['ort'] ?? '');
    $langtext = sanitize_textarea_field($params['langtext'] ?? '');
    $pressemitteilung = esc_url_raw($params['pressemitteilung'] ?? '');
    $instagram = esc_url_raw($params['instagram'] ?? '');
    $user_id = get_current_user_id();

    if (empty($datum) || empty($start) || empty($ende) || empty($einsatzart) || empty($ort) || empty($langtext)) {
        return new WP_Error('missing_fields', 'Bitte alle Felder ausfüllen.', array('status' => 400));
    }

    $inserted = $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'datum' => $datum,
            'start' => $start,
            'ende' => $ende,
            'einsatzart' => $einsatzart,
            'ort' => $ort,
            'langtext' => $langtext,
            'pressemitteilung' => $pressemitteilung,
            'instagram' => $instagram,
        ),
        array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    if ($inserted) {
        return rest_ensure_response(array('success' => true, 'message' => 'Einsatz erfolgreich gespeichert!'));
    } else {
        return new WP_Error('db_error', 'Fehler beim Speichern in der Datenbank.', array('status' => 500));
    }
}