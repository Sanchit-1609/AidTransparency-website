<?php

function create_resource_box($post, $metabox)
{
    global $post;
    $custom = get_post_custom($post->ID);
    $number = $metabox['args']['number'];

    echo '<input type="hidden" name="resources_noncename" id="resources_noncename" value="'.wp_create_nonce('resources').'" />';
?>
	<div id="resources_nuggets_table_container<?php echo $number; ?>" style="margin: 15px 0 0 0;">
		<table id="resources_nuggets_table<?php echo $number; ?>" width="100%" cellspacing="5px">
			<tr valign="top">
				<td style="width: 20%;"><label for="resources_box<?php echo $number; ?>_title">Resource Menu Title: </label></td>
				<td>
					<input type="text" id="resources_box<?php echo $number; ?>_title" name="resources_box<?php echo $number; ?>_title" value="<?php echo $custom["resources_box{$number}_title"][0]; ?>" size="40" />
				</td>
			</tr>
			<tr valign="top">
				<td style="width: 20%;"><label for="resources_box<?php echo $number; ?>_content">Resource Content: </label></td>
				<td>
                                    <?php wp_editor($custom["resources_box{$number}_content"][0], "resources_box{$number}_content"); ?>
				</td>
			</tr>
		</table>
	</div>
<?php
}

function resources_page_add_custom_box() {

	$post_ID = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
	$template_file = get_post_meta($post_ID,'_wp_page_template',TRUE);

        //Check the page we are on
	if ( $template_file == 'template-resources.php' ) {
            $numberBoxes = 6;
            for ($i = 1; $i <= $numberBoxes; $i++) {
                add_meta_box("resource$i", "Resource $i", "create_resource_box", 'page', 'normal', 'high', array( 'number' => $i) );
            }
	}

}


function resources_page_save_postdata($post_ID) {

	if(get_post_type($post_ID) != "page") { return $post_ID; }
	if (!wp_verify_nonce($_POST['resources_noncename'], 'resources')) { return $post_ID; }

	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return $post_ID;

	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_ID ) )
		return $post_ID;
	} else {
	if ( !current_user_can( 'edit_post', $post_ID ) )
		return $post_ID;
	}

        $numberBoxes = 6;
        for ($i = 1; $i <= $numberBoxes; $i++) {
            update_post_meta($post_ID, "resources_box{$i}_title", $_POST["resources_box{$i}_title"]);
            update_post_meta($post_ID, "resources_box{$i}_content", $_POST["resources_box{$i}_content"]);

        }


}

//brings it all together
add_action('add_meta_boxes_page', 'resources_page_add_custom_box');
add_action('save_post', 'resources_page_save_postdata');
