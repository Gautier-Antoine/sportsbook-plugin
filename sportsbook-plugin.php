<?php
/**
 * Plugin Name:       SportsBook Plugin
 * Description:       Add a MetaBox to select sportsBook
 * Version:           0.1.0
 * Author:            Gautier-Antoine
 */

/**
 * Add Content if Meta
 *
 * @param html $content
 * @return html
 */
function add_content( $content ) {
    $html = '';
    $meta = get_post_meta(get_the_ID(), 'sport_book_value', true);
    if ( $meta ) {
        $html .= '
        <!-- wp:cover {"overlayColor":"luminous-vivid-amber","isDark":false} -->
        <div class="wp-block-cover is-light">
            <span aria-hidden="true" class="wp-block-cover__background has-luminous-vivid-amber-background-color has-background-dim-100 has-background-dim"></span>
            <div class="wp-block-cover__inner-container">
                <!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
                <p class="has-text-align-center has-large-font-size">' . $meta . '</p>
                <!-- /wp:paragraph -->
            </div>
        </div>
        <!-- /wp:cover -->';
    }
    $html .= $content;
    return $html;
}
add_filter( 'the_content', 'add_content' );

/**
 * Create MetaBox
 *
 * @return void
 */
function add_metabox() {
    add_meta_box(
        'sportsbooks-settings', 
        'SportsBooks Settings', 
        'metabox_display_callback',
        'page', 
        "side", 
        "high",
    );
}
add_action( 'add_meta_boxes', 'add_metabox' );

/**
 * Get JSON from URL
 *
 * @return Array
 */
function get_array() {
    $url = 'https://www.viscaweb.com/developers/test-front-end/pages/step2-sportsbooks.json';
    $json = file_get_contents($url);
    $obj = json_decode($json);
    return $obj;
}

/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function save_meta_box($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (isset($_POST['sportbook'])) {
        update_post_meta($post_id, 'sport_book_value', sanitize_text_field($_POST['sportbook']));
    }
}
add_action('save_post', 'save_meta_box');

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 * @param string $arg
 * @return html
 */
function metabox_display_callback($post, $arg = '')
{
    $fields = get_array($post);
    $value = get_post_meta($post->ID, 'sport_book_value', true);
    if (!isset($value)) {
        add_post_meta($post->ID, 'sport_book_value', '', true);
    }
    if (!empty($fields)) {
        ?>
        <div class="meta_box">
            <label for="sportbook">Select a SportsBook:</label>
            <select name="sportbook" id="sportbook">
                <?php
                foreach ($fields as $key => $field) {
                    $selected = '';
                    if (isset($value) && $value === $key) {
                        $selected = ' selected';
                    }
                    echo '<option name="' . $key . '" id="' . $key . '"' . $selected . '>' . $field . '</option>';
                }
                ?>
            </select>
        </div>
        <?php
    }
}