<?php
/**
 * SweetApple Generic theme functions
 * @author Clive Sweeting, sweet-apple.co.uk <info@sweet-apple.co.uk>
 */

/**
 * Check .htaccess to see what state we are in
 */
if( getenv("HTTP_APPLICATION_ENVIRONMENT") == "development") {
    define("SWEETAPPLE_DEBUG", true);
    //Stuff we only want to showing during development...
    add_action('wp_footer', 'sweetapple_show_developer_tools', 1000);
}else{
    define("SWEETAPPLE_DEBUG", false);
}

/**
 * Print out various debud and developer info
 */
function sweetapple_show_developer_tools()
{
    print "<div class='debug'>";
    sweetapple_show_template();
    sweetapple_show_queries();
    print "</div>";
}


/**
 * Simple message logging to FirePHP console...
 * @param string $message
 */
function sweetapple_log( $message )
{
    if( SWEETAPPLE_DEBUG ) {
         /** Sets up FirePHP debugging */
        include_once("FirePHPCore2/fb.php");
        if( function_exists('fb') ){
            fb($message);
        }
    }
}

/**
 * Prints the template used under the footer
 * @global string $template
 */
function sweetapple_show_template()
{
    global $template;
    print "<div class='notice'>";
    print "Template Name <strong>$template</strong>";
    print "</div>";
}

/**
 * Prints out queries. Relies on adding define('SAVEQUERIES', true); to wp-config.php
 * @global wpdb $wpdb
 */
function sweetapple_show_queries()
{
    global $wpdb;
    print "<div class='entry'>";
    print_r($wpdb->queries);
    print "</div>";
}

/**
 * Checks is a specific file has been modified, alert the user and sends an email to site administrator
 */
function sweetapple_file_tripwire( $path )
{
    $tripwireOptionPath = 'sweetapple_tripwire';
    //Make sure the option exists..
    if (false === get_option($tripwireOptionPath)) {
        add_option($tripwireOptionPath,'','','no');
    }
    $tripWireOptions = get_option($tripwireOptionPath);

    $fileHash = null;
    $filePath = ABSPATH . $path;
    //Check if the hashed has already been stored, if not, save the hash.
    if( !is_array($tripWireOptions) || !$tripWireOptions['registered_files'][$path] ){
        $fileHash = md5_file( $filePath, false);
        $tripWireOptions['registered_files'][$path] = $fileHash;
        update_option($tripwireOptionPath, $tripWireOptions);
    }
    //Get the previously stored and current file hashes...
    $fileHash = $tripWireOptions['registered_files'][$path];
    $currentFileHash = md5_file( $filePath, false);
    //If they don't match, show an alert, send an email, clear the option so it doesn't keep reappearing.
    if($fileHash != $currentFileHash ) {
        //Add the path to the files into the array of files than have triggered the tripwire
        $tripWireOptions['tripwired_files'][] = $path;
        //Update the stored fileHash so we don't keep triggering the tripwire
        $tripWireOptions['registered_files'][$path] = $currentFileHash;
        //Store the changes
        update_option($tripwireOptionPath, $tripWireOptions);
    }

    //If we have any files that have triggerd the tripwire, show the alert...
    if( count( $tripWireOptions['tripwired_files'] ) ) {
        if( getenv("HTTP_APPLICATION_ENVIRONMENT") != "development") {
            wp_mail(get_option('admin_email'), "IMPORTANT: $filePath modifed", "The file $path on " . get_site_url() . " has been modifed by a Wordpress update.\nThis file was explicitly modified to provide custom functionality on your Wordpress website. Please request an updated version from your web developer.");
        }
        wp_enqueue_script('tinymce-warning', THEME_SCRIPTS . '/tripwire-warning.js', array('jquery-ui-dialog'), sweetapple_get_cachebuster() );
        wp_enqueue_style('wp-jquery-ui-dialog');
    }

}

/**
 * Clears any warning in the tripwire queue...
 */
function sweetapple_file_tripwire_clear($path = null)
{
    $tripwireOptionPath = 'sweetapple_tripwire';
    $tripWireOptions = get_option($tripwireOptionPath);
    if($path){
        unset($tripWireOptions[$path]);
    }else{
        $tripWireOptions = null;
    }
    update_option($tripwireOptionPath, $tripWireOptions);
}



function sweetapple_file_tripwire_menu() {
    add_menu_page('Wordpress Tripwire Report', 'Tripwire Report', 'manage_options', 'sweetapple_tripwire_menu', 'sweetapple_file_tripwire_options');
}

function sweetapple_file_tripwire_options() {
    if (!current_user_can('manage_options'))  {
        wp_die( __('You do not have sufficient permissions to access this page.') );
    }

?>
<div class="wrap">
        <h1>Tripwire warnings</h1>
        <p>What are Tripwire warnings? In some cases your Wordpress website may have have modifications to the core Wordpress files, or very specific functionality that may be disrupted by a Wordpress update.</p>
        <p>The warnings shown below are because of changes made to specific files that your developer has set to trigger a Tripwire. They do not necessarily mean that you will have a problem using your Wordpress site, but the Developer wants to warn you to be cautious.</p>
    </div>

<?php
    //Get the current values stored in the Tripwire...
    $tripwireOptionPath = 'sweetapple_tripwire';
    $tripWireOptions = get_option($tripwireOptionPath);
    $files_tripwired = $tripWireOptions['tripwired_files'];
    $formURL = get_option('siteurl') . '/wp-admin/admin.php?page=sweetapple_tripwire_menu';

    $hidden_field_name = 'sweetapple_tripwire_report_hidden';
    $tripwired_file_names = 'tripwired_files_names';

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        foreach ($_POST[ $tripwired_file_names ] as $value) {
            //Check if the value exists in the array, if so find the index and unset...
            $index = array_search( $value, $tripWireOptions['tripwired_files']);
            if($index !== false) {
               unset($tripWireOptions['tripwired_files'][$index]);
            }
        }
        update_option($tripwireOptionPath, $tripWireOptions);
        //Redirect to same page. Must set noheader=true to the form action for this to work, otherwise redirect fails as headers will already have been sent
        wp_redirect( $formURL );
        exit;
    }else{
        // Fixes a problem is the form data is invalid
        if (isset($_GET['noheader'])){
            require_once(ABSPATH . 'wp-admin/admin-header.php');
        }
    }

    if( count( $tripWireOptions['tripwired_files']) ) :
?>
    <h2>Clear Warnings</h2>
    <p>Below are the files that have triggered a tripwire warning. Tick those you wish to close the warnings for, and click "Clear Tripwire Warnings"</p>
    <form name="form1" method="post" action="<?php echo $formURL; ?>&noheader=true">
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row">Modified Files</th>
                     <td>
                    <?php foreach ($tripWireOptions['tripwired_files'] as $key => $value) : ?>

                        <label for="<?php esc_attr_e($tripwired_file_names . $key); ?>">
                            <input name="<?php echo $tripwired_file_names; ?>[]" id="<?php esc_attr_e($tripwired_file_names .$key); ?>" type="checkbox" value="<?php esc_attr_e($value); ?>" />
                            The file <strong><?php esc_attr_e($value); ?></strong> has been modified.
                        </label>
                         <br />
                    <?php endforeach; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Clear Tripwire warnings') ?>" />
        </p>
    </form>
<?php else : ?>
    <h2>No Warnings</h2>
    <p>Good news! Your Wordpress website hasn't triggered any Tripwires, so everything is hunky-dory, fine, trouble-free, pas de problem!</p>
<?php endif;

}
//add_action('admin_menu', 'sweetapple_file_tripwire_menu');


/**
 * Correct problems with some themes (most notably roots) returning relative paths to template_directory_uri
 */
function sweetapple_absolute_template_directory_uri()
{
    //Check to see if roots is doing anything weird...
    $template_url = get_template_directory_uri();
    if(strpos($template_url, 'http://') === false) {
        $template_url = get_bloginfo('url');
    }
    return $template_url;
}


/**
 * Add Javascript object to head to expose themePath and ajaxUrl variables to javascripts
 */
function sweetapple_localize_scripts( $name, $scriptHandle = 'jquery')
{
     if (!is_admin()) {

        // Add the paths to the themeRoot and the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
        // Access via JavaScript through {$name} object literal. {$name}.themePath and {$name}.ajaxUrl
        wp_localize_script( $scriptHandle , $name, array(
            'themePath' => get_template_directory_uri(),
            'ajaxUrl' => admin_url( 'admin-ajax.php' )
            )
        );
     }
}

/**
 * Print a script tag to the head with a JavaScript variable containing url to the theme
 * @return string
 */
function sweetapple_print_javascript_themepath()
{
    print "<script>var themePath = '" . sweetapple_absolute_template_directory_uri() . "/';</script>" .PHP_EOL;
}


function sweetapple_print_facebook_image_src()
{
    print '<link rel="image_src" href="' . sweetapple_absolute_template_directory_uri() . '/facebook.gif" />' .PHP_EOL;
}

/**
 * Get a query string parameter to add for cache busting
 * @return string
 */
function sweetapple_get_cachebuster()
{
    $theme  = wp_get_theme();
    return $theme->Version;
}
function sweetapple_print_cachebuster()
{
    print sweetapple_get_cachebuster();
}


/**
 * Shows a favicon from the Template/favicon.ico file
 */
function sweetapple_blog_favicon() {
	echo '<link rel="Shortcut Icon" type="image/x-icon" href="'. sweetapple_absolute_template_directory_uri() .'/favicon.ico" />' . PHP_EOL;
}
add_action('wp_head', 'sweetapple_blog_favicon');


/**
 * Prints out the content of the custom_header theme option in wp_head
 * @return null|string
 */
function sweetapple_print_custom_head() {
    print sweetapple_get_theme_option('custom_head');
}
add_action('wp_head', 'sweetapple_print_custom_head');


/**
 * Prints out the content of the custom_footer theme option in wp_footer
 * @return null|string
 */
function sweetapple_print_custom_footer() {
    print sweetapple_get_theme_option('custom_footer');
}
add_action('wp_footer', 'sweetapple_print_custom_footer');



/**
 * Shows a custom log on the Login screen from Template/images/logo-admin.png file
 */
function sweetapple_login_logo()
{
	echo '<style type="text/css">
	h1 a { background-image: url('.sweetapple_absolute_template_directory_uri().'/images/logo-admin.png) !important; }
	</style>';
}
add_action('login_head', 'sweetapple_login_logo');


/**
 * Make pages inherit the closest ancestor page template
 */
function sweetapple_inherit_parent_template()
{
    global $post;

    if( !get_option('sweetapple_inherit_template') ) return;
    //Is this going to be more efficient, bearing in mind reported bugs?
    //$ancestors = get_post_ancestors($post);

    //Does this page have a custom template assigned?
    if( is_page() && sweetapple_is_subpage() ){
        $parent_template = sweetapple_find_ancestor_template($post->ID);

        if($parent_template) {
           if ( file_exists(TEMPLATEPATH . DIRECTORY_SEPARATOR . $parent_template) ) {
                include( TEMPLATEPATH . DIRECTORY_SEPARATOR . $parent_template);
                exit;
            }
        }
    }else{
       return;
    }
}
add_action('template_redirect', 'sweetapple_inherit_parent_template');

/**
 * Recursively finds the ancestor template of a page
 * @param int $id
 * @return string|null
 */
function sweetapple_find_ancestor_template( $id )
{
    if( $id === 0 ) {
        return null;
    }
    $template = get_post_meta($id, '_wp_page_template', true);
    if( ($template == false) || ($template == "default") ) {
        $page = get_page( $id );
        return sweetapple_find_ancestor_template( $page->post_parent);
    }else{
        return $template;
    }
}


/**
 * Clean up the Wordpress head
 */
function sweetapple_clean_head()
{
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
}
add_action('init', 'sweetapple_clean_head');

/**
 * Clean up the Wordpress Admin UI a little
 */
function sweetapple_remove_dashboard_widgets()
{
	remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
	remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
	remove_meta_box('dashboard_primary', 'dashboard', 'normal');
	remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
}
add_action('admin_init', 'sweetapple_remove_dashboard_widgets');


/**
 * Adds first and last classes to wp_list_pages
 * @param string $output
 * @return string
 */
function sweetapple_alpha_omega_page_lists($output) {
    $output= preg_replace('/page-item/', ' first-page-item page-item', $output, 1);
    $output=substr_replace($output, " last-page-item page-item", strripos($output, "page-item"), strlen("page-item"));
    return $output;
}
//add_filter('wp_list_pages', 'sweetapple_alpha_omega_page_lists');


/**
 * Adds first and last classes to wp_list_categories
 * @param string $output
 * @return string
 */
function sweetapple_alpha_omega_category_lists($output) {
    $output= preg_replace('/cat-item/', ' first-cat-item cat-item', $output, 1);
    $output=substr_replace($output, " last-cat-item cat-item", strripos($output, "cat-item"), strlen("cat-item"));
    return $output;
}
//add_filter('wp_list_categories', 'sweetapple_alpha_omega_category_lists');

/**
 * Adds first and last classes to wp_nav_menu
 * @param string $output
 * @return type
 */
function sweetapple_alpha_omega_wp_nav_menu($output) {
//  $output = preg_replace('/class="menu-item/', 'class="first-menu-item menu-item', $output, 1);
//  $output = substr_replace($output, 'class="last-menu-item menu-item', strripos($output, 'class="menu-item'), strlen('class="menu-item'));
//  return $output;
}
//add_filter('wp_nav_menu', 'sweetapple_alpha_omega_wp_nav_menu');


/**
 * Checks if a page is a subpage
 * @global  $post
 * @return int|boolean
 */
function sweetapple_is_subpage()
{
    global $post;                                 // load details about this page
    if ( is_page() && ( $post->post_parent > 0) ) {      // test to see if the page has a parent
       $parentID = $post->post_parent;        // the ID of the parent is this
       return $parentID;                      // return the ID
    }
    return false;
};


/**
 * Get the ID of the oldest ancestor for this post (which may be itself
 * @param type $object
 * @return int
 */
function sweetapple_get_oldest_ancestor_id( $object )
{
    global $post;
    $ancestors = get_post_ancestors($post);
    return ( $ancestors) ? end($ancestors) : $object->ID;
}


/**
 *
 * @global $post
 * @param int $id
 * @return int|boolean
 */
function sweetapple_is_page_or_child( $id )
{
    global $post;

    if( $id == $post->ID ) { return $id;}

    if( $id == $post->post_parent) {
        return $id;
    }
    return false;
}

/**
 * Check if a page has children
 * @global $post $post
 * @return boolean
 */
function sweetapple_has_subpages()
{
    global $post;
    //Save the DB a few hits...
    if( !is_page()) {return false;}
    //Does the page have any children?  Must require submenu
    $subpages = get_pages( array( 'child_of' => $post->ID ) );
    return ( count($subpages) > 0 ) ? true : false;
}


/**
 * Checks is a page requires a subnavigation menu
 * @return boolean
 */
function sweetapple_has_submenu()
{
    global $post;
    //Save the DB a few hits...
    if( !is_page()) {return false;}
    //Does the current page have a parent? Must require submenu
    if( $post->post_parent != 0) { return true;}
    //Does the page have any children?  Must require submenu
    $subpages = get_pages( array( 'child_of' => $post->ID ) );
    return ( count($subpages) > 0 ) ? true : false;
}


/**
 * Finds all child pages or pages of the parent. Sorts by custom field
 * @global $post $post
 * @param boolean $ancestors
 * @return string
 */
function sweetapple_get_this_child_page_ids( $ancestors = false)
{
    global $post;

    if(sweetapple_is_subpage ()) {
        //Does it have child pages?
        if( sweetapple_has_subpages() ) {
            $page_root_id = $post->ID;
        }else{
            // Need to show the subpages of the parent
            $page_root_id = $post->post_parent;
        }
    }else{
        $page_root_id = $post->ID;
    }

    $args = array(
        'child_of'      => $page_root_id,
        'parent'        => $page_root_id,
        'sort_column'   => 'menu_order'
    );
    $pages = get_pages( $args );

    if( count($pages) == 0) {
        return false;
    }

    $page_ids = array();
    $ancestor_ids = array();
    $page_ids_to_find = array();

    foreach( $pages as $page){
        $page_ids[] = $page->ID;
    }

    //Add ancestors to page id array
    if($ancestors){
        $ancestor_ids = get_ancestors($post->ID, 'page');
    }
    $page_ids_to_find = array_merge( $ancestor_ids, $page_ids);
    return implode(",", $page_ids_to_find);
}



/**
 * Gets the ids of any sibling pages...
 * @global $post $post
 * @return array
 */
function sweetapple_get_sibling_page_ids( $id = null )
{
    global $post;
    $targetpage = null;

    if( $id == null ) {
        $targetpage = $post;
    }else{
        $targetpage = get_page($id);
    }

    $pages = get_pages(array(
        'child_of'  =>  $targetpage->post_parent,
        'parent'    =>  $targetpage->post_parent,
        'exclude'   =>  $targetpage->ID
    ));

    $page_ids = array();

    foreach ($pages as $page) {
        $page_ids[] = $page->ID;
    }
    $page_ids = ( count($page_ids) > 0) ? $page_ids : null;
    return $page_ids;
}



/**
 * Finds all child pages, optional returning ancestor pages as well. Sorts by custom field
 * @global $post $post
 * @param boolean $ancestors
 * @return string
 */
function sweetapple_get_child_page_ids( $id = null, $get_top_level = false)
{
    global $post;
    $targetpage = null;

    if( $id == null ) {
        $targetpage = $post;
    }else{
        $targetpage = get_page($id);
    }

    if(sweetapple_is_subpage ()) {
        //Does it have child pages?
        if( sweetapple_has_subpages() ) {
            $page_root_id = $targetpage->ID;
        }else{
            // Need to show the subpages of the parent
            $page_root_id = $targetpage->post_parent;
        }
    }else{
        $page_root_id = $targetpage->ID;
    }

    $pages = array();
    // We positively want all top level pages or we are a child page
    if( $get_top_level || ($page_root_id != 0) ){

        $args = array(
            'child_of'      => $page_root_id,
            'parent'        => $page_root_id,
            'sort_column'   => 'menu_order'
        );
        $pages = get_pages($args);
    }

//    if( count($pages) == 0) {
//        return false;
//    }

    $page_ids = array();
    foreach( $pages as $page){
        $page_ids[] = $page->ID;
    }

    return $page_ids;
}


/**
 * Prints out a submenu, with an optional title as the first list item, and optionally showing ancestors
 * @param string $title
 * @param boolean $ancestors
 */
function sweetapple_nav_page_submenu( $title = null, $ancestors = false)
{
    $page_ids = sweetapple_get_this_child_page_ids( $ancestors );
    $list_title = "";
    if(!empty( $title) ){
        $list_title = "<li class='list_title'>$title</li>" . PHP_EOL;
    }

    if( $page_ids !== false) {
        print "<ul class='submenu'>" . PHP_EOL;
        print $list_title;
        sweetapple_nav_submenu($page_ids);
        print "</ul>" . PHP_EOL;
    }
}


/**
 * Output the page ids as li
 * @global  $post
 * @param string $ids
 * @return <type>
 */
function sweetapple_nav_submenu( $page_ids )
{
    //get Child pages of the parent!
    return wp_list_pages( array (
        'title_li'  => "",
        'include'   => $page_ids,
        'sort_column'  => 'menu_order',
        'echo'         => false,
    ));
}

/**
 * Returns first and last names to add as classes to columns
 * @param int $position
 * @param int $rowCount
 * @return string
 */
function sweetapple_get_row_class($position, $colCount, $classes = array('first','last') ) {
    $rowClass = "";
    $mod = $position % $colCount;
    switch ($mod) {
        case 1:
            $rowClass = $classes[0];
            break;
        case 0:
            $rowClass = $classes[1];
            break;
        default:
            break;
    }
    return $rowClass;
}

/**
 * Add the value of the post_theme_color custom field  as a class on the body
 * @param array $classes
 * @return array
 */
function sweetapple_css_color( $classes )
{
    global $post;
    $bodyColor = null;

    //Find all the ancestors and check if they have a colour set
    $metaColor = get_post_meta( $post->ID, 'post_theme_color', true);
    if( strlen($metaColor) > 0 ) {
        $bodyColor = $metaColor;
    }else {
        $pageIDs = get_ancestors( $post->ID, 'page' );
        foreach ($pageIDs as $pageID) {
            $metaColor = get_post_meta( $pageID, 'post_theme_color', true);
            if( strlen($metaColor) > 0 ) {
                $bodyColor = $metaColor;
                break;
            }
        }
    }
    // add the post meta data to the $classes array
    $classes[] = $bodyColor;
    // return the $classes array
    return $classes;
}
add_filter( "body_class", "sweetapple_css_color" );


/**
 * Add social sharing functionality to any post
 * @param int $id
 */
function sweetapple_social_sharing($id) {
    $permalink = get_permalink( $id );
    $blogname = home_url();
?>
<span class="social-sharing">
<span class="entry-utility-prep entry-utility-prep-social-links">Share on:</span>
<a href="http://www.facebook.com/share.php?u=<?php echo $permalink; ?>" title="Share this page on Facebook" target="_blank" rel="nofollow"><img src="<?php echo THEME_IMAGES .'/facebook-16x16.png';?>" alt="Facebook icon" height="16" width="16"/></a>
<a href="http://twitter.com/share/?via=sweetappleuk&amp;text=<?php echo urlencode( "I'm liking this post on $blogname..."); ?>&amp;url=<?php echo $permalink; ?>" title="Share this page on Twitter" target="_blank"  rel="nofollow"><img src="<?php echo THEME_IMAGES .'/twitter-16x16.png'; ?>" alt="Twitter icon" height="16" width="16"/></a>
<a href="http://digg.com/submit?url=<?php echo $permalink; ?>" title="Share this page on Digg" target="_blank"  rel="nofollow"><img src="<?php echo THEME_IMAGES .'/digg-16x16.png'; ?>" alt="Digg icon" height="16" width="16"/></a>
<a href="http://reddit.com/submit?url=<?php echo $permalink; ?>" title="Share this page on Reddit" target="_blank"  rel="nofollow"><img src="<?php echo THEME_IMAGES .'/reddit-16x16.png'; ?>" alt="Reddit icon" height="16" width="16"/></a>
</span>
<?php
}


/**
 * Prints out any Custom CSS defined
 */
function sweetapple_print_custom_css()
{
    $custom_css = sweetapple_get_theme_option('custom_css');

    if($custom_css) :
?>
<style  type="text/css">
    <?php echo $custom_css; ?>
</style>
<?php endif;
}


/**
 * Prints out Google Analytics code
 * @param string $profile_id
 */
function sweetapple_print_analytics( $profile_id = null )
{
    if( getenv("HTTP_APPLICATION_ENVIRONMENT") != "production") return;

    if( !$profile_id ) {
        $options = get_option('sweetapple_theme_options');
        if($options['google_analytics_id']){
            $profile_id = $options['google_analytics_id'];
        }
    }

    if( !empty($profile_id) ) :
?>
<script type="text/javascript">
//<![CDATA[
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo $profile_id; ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
//]]>
</script>
<?php
    endif;
}
add_action('wp_footer', 'sweetapple_print_analytics', 999);

/**
 * Gets all gallery images associated with an id, ordered
 * @param int $id
 * @return array|null
 */
function sweetapple_get_gallery_images($id, $order = 'menu_order ID')
{
    $photos = null;

    if(is_numeric($id) ){
        $args = array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => $order);
        $photos = get_children( $args);
        return $photos;
    }

    //Check if it's a array of ids...
    $ids = array_map( 'trim', explode(',', $id) );
    if(is_array($ids) ){
        $args = array(
            'numberposts'     => 5,
            'offset'          => 0,
            'include'         => $id,
            'exclude'         => $exclude,
            'orderby'         => null,
            'post_type'       => 'attachment',
            'post_mime_type' => 'image',
            'post_status'     => 'i'
        );
        //Filter does seem to run on this query. Maybe because we are using attachment post type?
//       add_filter('posts_orderby', sweetapple_enforce_specific_order(implode(',', $ids) ),0,1);
//       $photos = get_posts($args);
//       remove_filter('posts_orderby', 'sweetapple_enforce_specific_order');

        $orderedPhotos = array();
        $photos = get_posts($args);

        foreach ($ids as $id) {
            foreach ($photos as $photo) {
                if( $photo->ID == $id ){
                    $orderedPhotos[$id] = $photo;
                }
            }
        }
        return $orderedPhotos;
    }

    return null;

}


/**
 * Returns specific images by the their ids
 * @param string|array $ids
 * @return string|array
 */
function sweetapple_get_specific_images($ids)
{
    $photos = null;

    if( is_string($ids) ){
        $ids = explode( ',', $ids);
    }
    $ids = array_map( 'trim', $ids);

    $args = array(
        'numberposts'     => 5,
        'offset'          => 0,
        'include'         => implode(',', $ids),
        'orderby'         => null,
        'post_type'       => 'attachment',
        'post_mime_type' => 'image',
        'post_status'     => 'i'
     );

    $photos = get_posts($args);

    if( count($photos) ){
        $orderedPhotos = array();
        //Sort the results by the supplied order...
        foreach ($ids as $id) {
            foreach ($photos as $photo) {
                if( $photo->ID == $id ){
                    $orderedPhotos[$id] = $photo;
                }
            }
        }
        return $orderedPhotos;
    }
    return null;

}

/**
 * Allows us to sort posts by a predetermined order, for example if specifiying the ids to find...
 * @global wpdb $wpdb
 * @param string $orderby
 * @return string
 */
function sweetapple_enforce_specific_order($orderby) {
    global $wpdb;
    return "FIND_IN_SET(".$wpdb->posts.".ID, '{$orderby}') ASC";
}


/**
 * Recursively searches down pages for gallery images
 * @param $id
 * @return array|bool
 */
function sweetapple_get_gallery_images_recursive( $id )
{
    if( $id == 0) return;
    $photos = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') );
    if( count($photos) > 0) {
       return $photos;
    }else{
        $post = get_post($id);
        return sweetapple_get_gallery_images_recursive($post->post_parent);
    }
}


function sweetapple_get_default_gallery( $photos )
{
    //Get first item in associative array...
    $largeKey = key($photos);
    $largePhoto = $photos[$largeKey];
    reset($photos);
    $output = "";
    $output .= "<div class='custom-gallery default'>";
    $output .= "<div class='large'>";
    $output .= wp_get_attachment_image($largePhoto->ID, "gallery-large", false, array( 'id' => 'large-' .$largeKey ));
    $output .= "</div>";

    if( count($photos) > 1 ) {

    //The thumbnails
    $output .= "<div class='thumbs'>";
    foreach ($photos as $photo) {

        $large = wp_get_attachment_image_src($photo->ID, "gallery-large");

        $output .= "<div class='thumb'>";
        $output .= "<a href='" . $large[0] . "' title='View larger image' class='gallery'>";
        $output .=  wp_get_attachment_image($photo->ID, "gallery-small", false, array( 'class' => 'thumbnail', 'data-target' => 'large-' .$largeKey, 'data-id' => $photo->ID ) );
        $output .= "</a>";
        $output .= "</div>";
    }
    $output .= "</div><!-- //.thumbs -->";
    $output .= "</div><!-- //.custom-gallery.default -->";

    //Javascript to manage gallery...
    $output .= "<script type='text/javascript'>";
    $output .= "jQuery(document).ready(function($){". PHP_EOL;
    $output .= "$('a.gallery').click( function(){". PHP_EOL;
    $output .= "var img = $(this).children('img');". PHP_EOL;
    $output .= "var target = img.attr('data-target');". PHP_EOL;
    $output .= "$('#' + target).attr('src',this.href) ;". PHP_EOL;
    $output .= "return false;". PHP_EOL;
    $output .= "});". PHP_EOL;
    $output .= "});". PHP_EOL;
    $output .= "</script>". PHP_EOL;

    }// end if more than 1

    return $output;
}


/**
 * Create a lightbox stype gallery
 * @param array $photos
 * @return string
 */
function sweetapple_get_lightbox_gallery( $photos )
{

    //Get first item in associative array...
    $largeKey = key($photos);
    $largePhoto = $photos[$largeKey];
    reset($photos);
    $output = "";
    $output .= "<div class='custom-gallery lightbox'>";

    $relGroup = mt_rand();

    if( count($photos) > 1 ) {

    //The thumbnails
    $output .= "<div class='thumbs'>";
    $position = 1;
    foreach ($photos as $photo) {

        $large = wp_get_attachment_image_src($photo->ID, "gallery-lightbox-large");
        $altMeta = get_post_meta($photo->ID, '_wp_attachment_image_alt', true);
        $alt = strlen($altMeta) ? $altMeta: "";

        $output .= "<div class='thumb'>";
        $output .= "<a href='" . $large[0] . "' title='$alt' class='fancybox' rel='fancybox-$relGroup'>";
        $output .=  wp_get_attachment_image($photo->ID, "gallery-lightbox-small", false, array( 'class' => 'thumbnail', 'data-target' => 'large-' .$largeKey ) );
        $output .= "</a>";
        $output .= "</div>";
        $position++;
    }
    $output .= "</div><!-- //.thumbs -->";
    $output .= "</div><!-- //.custom-gallery.lightbox -->";

    //Javascript to manage gallery...
    $output .= "<script type='text/javascript'>";
    $output .= "jQuery(document).ready(function($){". PHP_EOL;
    $output .= "$('a.fancybox').fancybox({". PHP_EOL;
    $output .= "'transitionIn'	:	'elastic',". PHP_EOL;
    $output .= "'transitionOut'	:	'elastic',". PHP_EOL;
    $output .= "'speedIn'	:	'600',". PHP_EOL;
    $output .= "'speedOut'	:	'200',". PHP_EOL;
    $output .= "'overlayColor'	:	'#fff',". PHP_EOL;
    $output .= "'overlayOpacity':	'0.7',". PHP_EOL;
    $output .= "'overlayShow'	:	'true',". PHP_EOL;
    $output .= "});". PHP_EOL;
    $output .= "});". PHP_EOL;
    $output .= "</script>". PHP_EOL;

    }// end if more than 1

    return $output;
}


/**
 * Create a lightbox stype gallery
 * @param array $photos
 * @return string
 */
function sweetapple_get_filmstrip_gallery( $photos )
{
    //Get first item in associative array...
    $largeKey = key($photos);
    $largePhoto = $photos[$largeKey];
    reset($photos);
    $output = "";
    $output .= "<div id='rg-gallery' class='custom-gallery filmstrip' style='width: 100%; max-width: 700px;'>";

    if( count($photos) > 1 ) {
    $output .= "<ul id='gallery-image-filmstrip' class='elastislide-list'>" . PHP_EOL;
    $position = 1;
    foreach ($photos as $photo) {

        $large = wp_get_attachment_image_src($photo->ID, "full-width");
        $altMeta = get_post_meta($photo->ID, '_wp_attachment_image_alt', true);
        $alt = strlen($altMeta) ? $altMeta: "";

        $output .= "<li data-preview='" . $large[0] . "'>";
        $output .= "<a href='#' title='$alt'>" . PHP_EOL;
        $output .=  wp_get_attachment_image($photo->ID, "gallery-small", false, array( 'class' => 'thumbnail') ) . PHP_EOL;
        $output .= "</a>";
        $output .= "</li>" . PHP_EOL;
        $position++;
    }
    $output .= "</ul>" . PHP_EOL;
    //Large image placeholder...
    $previewImage = wp_get_attachment_image($largePhoto->ID, "full-width", false, array('id' => 'gallery-preview') );
    $output .= "<div id='gallery-image-preview'>";
    $output .= $previewImage;
    $output .= "</div>";
    $output .= "</div><!-- //.custom-gallery.filmstrip -->";
    }// end if more than 1

    return $output;
}


/**
 * Create a lightbox stype gallery
 * @param array $photos
 * @return string
 */
function sweetapple_get_filmstrip_gallery_complex( $photos )
{
    wp_enqueue_script('jquery-template', THEME_SCRIPTS . '/jquery.tmpl.min.js', array('jquery'), null);
    wp_enqueue_script('jquery-easing', THEME_SCRIPTS . '/jquery.easing.1.3.js', array('jquery-template'), '1.3.1');
    wp_enqueue_script('jquery-elasticslide', THEME_SCRIPTS . '/jquery.elastislide.js', array('jquery-easing'), null);

    //Get first item in associative array...
    $largeKey = key($photos);
    $largePhoto = $photos[$largeKey];
    reset($photos);
    $output = "";
    $output .= "<div id='rg-gallery' class='custom-gallery filmstrip'>";

    $relGroup = mt_rand();

    if( count($photos) > 1 ) {

    //The thumbnails
    $output .= "<div class='rg-thumbs thumbs'>";
    $output .= "<!-- Elastislide Carousel Thumbnail Viewer -->";
    $output .= "<div class='es-carousel-wrapper'>";
    $output .= "<div class='es-nav'>";
    $output .= "<span class='es-nav-prev'>Previous</span>";
    $output .= "<span class='es-nav-next'>Next</span>";
    $output .= "</div>";
    $output .= "<div class='es-carousel'>";
    $output .= "<ul>";
    $position = 1;
    foreach ($photos as $photo) {

        $large = wp_get_attachment_image_src($photo->ID, "gallery-lightbox-large");
        $altMeta = get_post_meta($photo->ID, '_wp_attachment_image_alt', true);
        $alt = strlen($altMeta) ? $altMeta: "";

        $output .= "<li class='thumb'>";
        $output .= "<a href='" . $large[0] . "' title='$alt'>";
        $output .=  wp_get_attachment_image($photo->ID, "gallery-lightbox-small", false, array( 'class' => 'thumbnail', 'data-large' => 'large-' .$largeKey ) );
        $output .= "</a>";
        $output .= "</li>";
        $position++;
    }
    $output .= "</ul>";
    $output .= "</div><!-- //.es-carousel -->";
    $output .= "</div><!-- //.es-carousel-wrapper -->";
    $output .= "</div><!-- //.thumbs -->";
    $output .= "<!-- End Elastislide Carousel Thumbnail Viewer -->";
    $output .= "</div><!-- //.custom-gallery.filmstrip -->";

    //jQuery template to manage gallery...
    $output .= '<script id="img-wrapper-tmpl" type="text/x-jquery-tmpl">' .PHP_EOL;
    $output .= '<div class="rg-image-wrapper">' .PHP_EOL;
    $output .= '{{if itemsCount > 1}}' .PHP_EOL;
    $output .= '<div class="rg-image-nav">' .PHP_EOL;
    $output .= '<a href="#" class="rg-image-nav-prev">Previous Image</a>' .PHP_EOL;
    $output .= '<a href="#" class="rg-image-nav-next">Next Image</a>' .PHP_EOL;
    $output .= '</div>' .PHP_EOL;
    $output .= '<div class="rg-image"></div>' .PHP_EOL;
    $output .= '<div class="rg-loading"></div>' .PHP_EOL;
    $output .= '<div class="rg-caption-wrapper">' .PHP_EOL;
    $output .= '<div class="rg-caption" style="display:none;">' .PHP_EOL;
    $output .= '<p></p>' .PHP_EOL;
    $output .= '</div>' .PHP_EOL;
    $output .= '</div>' .PHP_EOL;
    $output .= '</div>' .PHP_EOL;
    $output .= '</script>';


    }// end if more than 1

    return $output;
}



/**
 * Get an option set through the theme options
 * @param string $name
 */
function sweetapple_get_theme_option($name) {
    $options = get_option('sweetapple_theme_options', sweetapple_get_default_theme_options());
    if(array_key_exists($name, $options)){
        return $options[$name];
    }
    return null;
}


/**
 *
 * @param string $url
 * @param string $text
 * @param array $attr
 * @param boolean $external
 * @return string
 */
function sweetapple_get_link($url, $text, $attr, $external = true)
{
    $attrStr = "";
    foreach ($attr as $key => $value) {
        $attrStr .= "$key='$value' ";
    }
    if($external){
        $attrStr .= " target='_blank'";
    }

    $link = "<a href='{$url}' {$attrStr}>$text</a>";
    return $link;
}

function sweetapple_get_icon_link($url, $text, $attr, $external = true)
{
    $attrStr = "";
    foreach ($attr as $key => $value) {
        $attrStr .= "$key='$value' ";
    }
    if($external){
        $attrStr .= " target='_blank'";
    }

    $link = "<a href='$url' $attrStr><img src='{THEME_IMAGES}/icon--.png' alt=''></a>";
    return $link;
}


function sweetapple_print_social_media_link($name, $body)
{
    if($url = sweetapple_get_theme_option($name)){
        print sweetapple_get_link($url, $body, array('class' => $name) );
    }
}

function sweetapple_print_social_links()
{
?>
<ul class="social-media-links">
<?php if(sweetapple_get_theme_option('facebook_url_social')) : ?>
    <li>
        <?php sweetapple_print_social_media_link('facebook_url_social','Like us on Facebook') ?>
    </li>
<?php endif; ?>
<?php if(sweetapple_get_theme_option('twitter_url_social')) : ?>
    <li>
        <?php sweetapple_print_social_media_link('twitter_url_social', 'Follow us on Twitter') ?>
    </li>
<?php endif; ?>
<?php if(sweetapple_get_theme_option('linkedin_url_social')) : ?>
    <li>
        <?php sweetapple_print_social_media_link('linkedin_url_social', 'Join us on LinkedIn'); ?>
    </li>
<?php endif; ?>
<?php if(sweetapple_get_theme_option('youtube_url_social')) : ?>
<li>
<?php sweetapple_print_social_media_link('youtube_url_social',__('Watch us on Youtube')); ?>
</li>
<?php endif; ?>
</ul>
<?php
}

function sweetapple_print_footer_address()
{
?>
<p>
    <?php print sweetapple_get_theme_option('contact_address'); ?><br/>
</p>
<?php
sweetapple_print_social_links();
}


/************* CUSTOM LOGIN PAGE *****************/

// calling your own login css so you can style it
function sweetapple_login_css() {
	/* I couldn't get wp_enqueue_style to work :( */
	echo '<link rel="stylesheet" href="' . get_stylesheet_directory_uri() . '/library/css/login.css">';
}

// changing the logo link from wordpress.org to your site
function sweetapple_login_url() {  return home_url(); }

// changing the alt text on the logo to show your site name
function sweetapple_login_title() { return get_option('blogname'); }

// calling it only on the login page
add_action('login_head', 'sweetapple_login_css');
add_filter('login_headerurl', 'sweetapple_login_url');
add_filter('login_headertitle', 'sweetapple_login_title');


/************* ADMIN CUSTOMIZATION *****************/


//Add some custom text to the footer of the Wordpress Admin. Taken from the styles.css definition
function sweetapple_custom_admin_footer() {
    $theme  = wp_get_theme();
    echo "<span id='footer-thankyou'>Developed by <a href='{$theme->AuthorURI}' target='_blank'>{$theme->Author}</a> for <a href='{$theme->get('ThemeURI')}'>{$theme->get('ThemeURI')}</a></span>.";
}

// adding it to the admin area
add_filter('admin_footer_text', 'sweetapple_custom_admin_footer');


/**
 * Filter callback to add image sizes to Media Uploader
 *
 * WP 3.3 beta adds a new filter 'image_size_names_choose' to
 * the list of image sizes which are displayed in the Media Uploader
 * after an image has been uploaded.
 *
 * See image_size_input_fields() in wp-admin/includes/media.php
 *
 * @uses get_intermediate_image_sizes()
 *
 * @param $sizes, array of default image sizes (associative array)
 * @return $new_sizes, array of all image sizes (associative array)
 * @author Ade Walker http://www.studiograsshopper.ch
 */
function sweetapple_display_image_size_names_muploader( $sizes ) {

	$new_sizes = array();

	$added_sizes = get_intermediate_image_sizes();

	// $added_sizes is an indexed array, therefore need to convert it
	// to associative array, using $value for $key and $value
	foreach( $added_sizes as $key => $value) {
            if(strpos($value, "gallery-") === false ){
                $new_sizes[$value] = ucwords( str_replace('-', ' ', $value) );
            };
	}

	// This preserves the labels in $sizes, and merges the two arrays
	$new_sizes = array_merge( $new_sizes, $sizes );

	return $new_sizes;
}
add_filter('image_size_names_choose', 'sweetapple_display_image_size_names_muploader', 11, 1);



/**
 *  Warning to show this is a dev site
 */
function sweetapple_print_dev_warning()
{
    if(sweetapple_get_theme_option('development_warning')) :
        $warningText = sweetapple_get_theme_option('development_warning_text');
        if( strlen($warningText) <= 0 ){
            $warningText = "This is a development site that may contain factual or other errors. Please check back soon for a live version.";
        }
        ?>
    <div id="site-notice"><?php echo $warningText; ?></div>
    <?php
    endif;
}
