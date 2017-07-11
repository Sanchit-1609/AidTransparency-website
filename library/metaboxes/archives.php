<?php
/* * ******************* archive box 1 */


function create_archive_box($post, $metabox)
{
    global $post;
    $custom = get_post_custom($post->ID);
    $number = $metabox['args']['number'];

    echo '<input type="hidden" name="archives_noncename" id="archives_noncename" value="' . wp_create_nonce('archives') . '" />';
    ?>
    <div id="archives_nuggets_table_container<?php echo $number;?>" style="margin: 15px 0 0 0;">
        <table id="archives_nuggets_table<?php echo $number;?>" width="100%" cellspacing="5px">
            <tr valign="top">
                <td style="width: 20%;"><label for="archives_box<?php echo $number;?>_title">Archive Menu Title: </label></td>
                <td>
                    <input type="text" id="archives_box<?php echo $number;?>_title" name="archives_box<?php echo $number;?>_title" value="<?php echo $custom["archives_box{$number}_title"][0]; ?>" size="40" />
                </td>
            </tr>
            <tr valign="top">
                <td style="width: 20%;"><label for="archives_box<?php echo $number;?>_content">Archive Content: </label></td>
                <td>
                    <?php wp_editor($custom["archives_box{$number}_content"][0], "archives_box{$number}_content"); ?>
                </td>
            </tr>
        </table>
    </div>
    <?php
}



/* * ******************* end of archive box 3 */

function archives_page_add_custom_box()
{

    //$post_ID = array_key_exists('post', $_GET) ? $_GET['post'] : $_POST['post_ID'];
    // Set Post ID
    if (array_key_exists('post', $_GET)){
        $post_ID = $_GET['post'];
    } elseif(array_key_exists('post_ID', $_POST)) {
        $post_ID = $_POST['post_ID'];
    } else {
        $post_ID = NULL;
    }
    $template_file = get_post_meta($post_ID, '_wp_page_template', TRUE);

    //global $template_fields;

    if ($template_file == 'template-archives.php') {
        $numberBoxes = 3;
        for ($i = 1; $i <= $numberBoxes; $i++) {
            add_meta_box("archive$i", "Archive $i", "create_archive_box", 'page', 'normal', 'high', array( 'number' => $i) );
        }
    }
}


function archives_page_save_postdata($post_ID)
{

    if (get_post_type($post_ID) != "page") {
        return $post_ID;
    }
    if (!wp_verify_nonce($_POST['archives_noncename'], 'archives')) {
        return $post_ID;
    }

    // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_ID;

    // Check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_ID))
            return $post_ID;
    } else {
        if (!current_user_can('edit_post', $post_ID))
            return $post_ID;
    }

    $numberBoxes = 6;
    for ($i = 1; $i <= $numberBoxes; $i++) {
        update_post_meta($post_ID, "archives_box{$i}_title", $_POST["archives_box{$i}_title"]);
        update_post_meta($post_ID, "archives_box{$i}_content", $_POST["archives_box{$i}_content"]);
    }
}



add_action('admin_init', 'archives_page_add_custom_box');
add_action('save_post', 'archives_page_save_postdata');
