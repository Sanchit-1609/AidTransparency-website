<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TitledHtml
 *
 * @author csweet
 */
class IATI_Metabox_TitledHtml
{

    /**
     * Prefix to use saving this postmeta
     * @var string
     */
    protected $_prefix = null;


    /**
     * Number of boxes created.
     * @var int
     */
    protected $_number = null;

    /**
     *
     * @var name of the template these metaboxes should appear on
     */
    protected $_template = null;



    /**
     *
     * @param string $prefix
     * @param int $number
     * @param string $template
     */
    public function __construct($prefix, $number, $template)
    {
        $this->_prefix = $prefix;
        $this->_number = $number;
        $this->_template= $template;
    }


    public function configureMetaBoxes()
    {
        if(is_admin() && current_user_can('edit_post') ){
            $post_id = ((int)$_GET['post']) ? $_GET['post'] : $_POST['post_ID'] ;
            $template_file = get_post_meta($post_id,'_wp_page_template',TRUE);

            switch ($template_file) {
                case 'template-resources.php':
                    break;

                default:
                    break;
            }

            $titledhtml = new IATI_Metabox_TitledHtml();

        }
    }



    public function createMetabox($post, $metabox)
    {
        global $post;
        $custom = get_post_custom($post->ID);
        $number = $metabox['args']['number'];

        echo '<input type="hidden" name="resources_noncename" id="resources_noncename" value="' . wp_create_nonce('resources') . '" />';
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


    public function addMetaBox()
    {
        $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
        $template_file = get_post_meta($post_id, '_wp_page_template', TRUE);

        //Check the page we are on
        if ($template_file == $this->_template) {
            for ($i = 1; $i <= $this->_number; $i++) {
                add_meta_box("resource$i", "Resource $i", "create_resource_box", 'page', 'normal', 'high', array ('number' => $i));
            }
        }
    }


    public function saveData($post_ID)
    {

        if (get_post_type($post_ID) != "page") {
            return $post_id;
        }
        if (!wp_verify_nonce($_POST["{$this->_prefix}_noncename"], $this->_prefix)) {
            return $post_id;
        }

        // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;

        // Check permissions
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return $post_id;
        } else {
            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }

        for ($index = 0; $index < $this->_number; $index++) {
            update_post_meta($post_ID, "{$this->_prefix}_box1_title", $_POST["{$this->_prefix}_box1_title"]);
            update_post_meta($post_ID, "{$this->_prefix}_box1_content", $_POST["{$this->_prefix}_box1_content"]);
        }
    }

}
