<?php

$templatePath = get_stylesheet_directory();

//SOME USEFUL CONSTANTS
define('THEME_LIB', $templatePath . '/library');
define('THEME_CLASSES', THEME_LIB . '/class');
define('THEME_IMAGES', get_stylesheet_directory_uri() . '/library/images');
define('THEME_SCRIPTS', get_stylesheet_directory_uri() . '/library/js');
define('THEME_STYLES', get_stylesheet_directory_uri() . '/library/css');

//Define path to autoloaded classes...
set_include_path(THEME_CLASSES . PATH_SEPARATOR . get_include_path());

require_once(THEME_LIB . '/sweetapple_functions.php'); // generic helper functions
require_once(THEME_LIB . '/sweetapple_theme_options.php'); // generic theme options
require_once(THEME_LIB . '/metaboxes/resources.php'); // generic helper functions
require_once(THEME_LIB . '/metaboxes/archives.php'); // generic helper functions
require_once(THEME_LIB . '/iati_functions.php'); // iati specific theme options



/**
 * Tell WordPress to run aidtransparency_setup() when the 'after_setup_theme' hook is run.
 */
add_action('after_setup_theme', 'aidtransparency_setup');

if (!function_exists('aidtransparency_setup')):

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which runs
     * before the init hook. The init hook is too late for some features, such as indicating
     * support post thumbnails.
     *
     * To override aidtransparency_setup() in a child theme, add your own aidtransparency_setup to your child theme's
     * functions.php file.
     *
     * @uses load_theme_textdomain() For translation/localization support.
     * @uses add_editor_style() To style the visual editor.
     * @uses add_theme_support() To add support for post thumbnails, automatic feed links, and Post Formats.
     * @uses register_nav_menus() To add support for navigation menus.
     * @uses add_custom_background() To add support for a custom background.
     * @uses add_custom_image_header() To add support for a custom header.
     * @uses register_default_headers() To register the default custom header images provided with the theme.
     * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
     *
     * @since Aid Transparency 3.1
     */
    function aidtransparency_setup()
    {

        /* Make Twenty Eleven available for translation.
         * Translations can be added to the /languages/ directory.
         * If you're building a theme based on Twenty Eleven, use a find and replace
         * to change 'aidtransparency' to the name of your theme in all the template files.
         */
        load_theme_textdomain('aidtransparency', THEME_LIB . '/languages');

        $locale = get_locale();
        $locale_file = THEME_LIB . "/languages/$locale.php";
        if (is_readable($locale_file))
            require_once( $locale_file );

        // This theme styles the visual editor with editor-style.css to match the theme style.
        add_editor_style();

        // Add default posts and comments RSS feed links to <head>.
        add_theme_support('automatic-feed-links');

        // This theme uses wp_nav_menu() in one location.
        add_theme_support('menus'); //add support for menus
        register_nav_menu('primary', __('Primary Menu', 'aidtransparency'));

        // Add support for a variety of post formats
//    add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'image' ) );
        // This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
        add_theme_support('post-thumbnails');

        // The next four constants set how Twenty Eleven supports custom headers.
        // Add Twenty Eleven's custom image sizes
        add_image_size('single-thumb', 260, 195, true);
        add_image_size('list-thumb', 150, 112, true);
        add_image_size('list-thumb', 150, 112, true);
        add_image_size('featured-thumb', 190, 119, true);
    }


endif; // aidtransparency_setup

/**
 * Adds widget support to the theme
 */
function aidtransparency_widgets_init() {

    $widgetDefaults = array(
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    );


    register_sidebar(
        array_merge(
            $widgetDefaults,
            array(
                'name' => __( 'Home Page Sidebar', 'aidtransparency' ),
                'id' => 'sidebar-3',
                'description' => __( 'The Home Page widgetized area', 'aidtransparency')
            )
        )
    );

    register_sidebar(
        array_merge(
            $widgetDefaults,
            array(
                'name' => __( 'Page Sidebar', 'aidtransparency' ),
                'id' => 'sidebar-1',
                'description' => __( 'The Sidebar widgetized area', 'aidtransparency')
            )
        )
    );

    register_sidebar(
        array_merge(
            $widgetDefaults,
            array(
                'name' => __( 'Blog Sidebar', 'aidtransparency' ),
                'id' => 'sidebar-2',
                'description' => __( 'The Blog Sidebar widgetized area', 'aidtransparency')
            )
        )
    );

    register_sidebar(
        array_merge(
            $widgetDefaults,
            array(
                'name' => __( 'Footer', 'aidtransparency' ),
                'id' => 'footer-1',
                'description' => __( 'The Footer widgetized area', 'aidtransparency' )
            )
        )
    );
}
add_action( 'widgets_init', 'aidtransparency_widgets_init' );

/**
 * Add javascripts...
 */
function aidtransparency_scripts()
{
    if (!is_admin()) {
        $cachebuster = sweetapple_get_cachebuster();

        //de-registers the default jquery library in favour for the google hosted version.
        //wp_deregister_script('jquery');
        //wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"), false, '1.8.2',true);
        //wp_enqueue_script('jquery');

        wp_enqueue_script('modernizr', THEME_SCRIPTS . '/modernizr-2.6.1.min.js', null, '2.6.1');
        wp_enqueue_script('selectivizr', THEME_SCRIPTS . '/selectivizr.min.js', null, '1.2');
        wp_enqueue_script('jquery-ui', THEME_SCRIPTS . '/jquery-ui-1.7.2.js', array ('jquery'), '1.7.2', true);
        wp_enqueue_script('jquery-scrollTo', THEME_SCRIPTS . '/jquery.scrollTo-1.4.2-min.js', array ('jquery'), '1.4.2', true);
        wp_enqueue_script('jquery-tools', THEME_SCRIPTS . '/jquery.tools.min.js', array ('jquery'), $cachebuster , true);
        wp_enqueue_script('timelinejs', THEME_SCRIPTS . '/storyjs-embed.js', array ('jquery'), '2.0.3 ', true);
        wp_enqueue_script('iatijs', THEME_SCRIPTS . '/iati.js', array ('jquery'), $cachebuster, true);
        wp_enqueue_script('aidtransparency', THEME_SCRIPTS . '/aidtransparency.js', array ('iatijs','timelinejs'), $cachebuster, true);
        wp_enqueue_script('common-js', THEME_SCRIPTS . '/common.js', array ('aidtransparency'), $cachebuster, true);

        sweetapple_localize_scripts('Aidtransparency', 'modernizr');
    }
}
add_action('wp_print_scripts', 'aidtransparency_scripts');


/**
 * Add styles
 */
function aidtransparency_styles()
{
    if (!is_admin()) {
        $cachebuster = sweetapple_get_cachebuster();
        // register main stylesheet
        wp_enqueue_style('main-stylesheet', THEME_STYLES . '/style.css', null, $cachebuster, 'all');
        wp_enqueue_style('jquery-fancybox', THEME_STYLES . '/jquery-ui-1.7.2.css', null, '1.7.2', 'screen');
    }
}add_action('wp_print_styles', 'aidtransparency_styles');


// takes a post date and outputs "X day(s) since"
function days_since( $post = null )
{
    $days_ago = round(( date('U') - get_the_time('U') ) / ( 60 * 60 * 24 ));
    if ($days_ago == 0) {
        $posted = 'Today';
    } elseif ($days_ago == 1) {
        $posted = '1 day ago';
    } else {
        $posted = $days_ago . ' days ago.';
    }
    echo $posted;
}

function aidtransparency_post_image_html($html, $post_id, $post_image_id)
{
    $html = '<a class="post-image-link" href="' . get_permalink($post_id) . '" title="' . esc_attr(get_post_field('post_title', $post_id)) . '" rel="nofollow">' . $html . '</a>';
    return $html;
}add_filter('post_thumbnail_html', 'aidtransparency_post_image_html', 10, 3);



function aidtransparency__excerpt_length($length)
{
    return 20;
}
add_filter('excerpt_length', 'aidtransparency__excerpt_length');


function aidtransparency__excerpt_more($more)
{
    return ' ...';
}
add_filter('excerpt_more', 'aidtransparency__excerpt_more');


/**
 *
 * Print Vimeo video links, can restrict to specific number, depends on frontend JS to call in actual video <iframe>
 * @param null $post
 * @param int $number
 */
function aidtransparency_print_vimeo_videos($post = null, $number = 2)
{
    global $post;
    $vidoesStr = false; //types_render_field("home_page_vimeo", array( 'post_id' => $post->ID, 'raw' => true ) );
    $videoIds = array_map( 'trim', explode(',', $vidoesStr) );//Make into array and trim members
    if(count($videoIds) > 0) :
        $count = 1;
        ?>
    <div class="videos">
        <?php foreach ($videoIds as $videoId) : if( $count > $number ) continue; ?>
        <div class="video <?php print sweetapple_get_row_class($count, 2);?>" >
            <a href="http://vimeo.com/<?php echo $videoId;?>" class="vimeo-video" data-vimeoid="<?php echo $videoId;?>">View this Vimeo video</a>
        </div>
        <?php $count++; endforeach;?>
    </div><!--.videos -->
    <?php endif;
}


/**
 * Get Vimeo Video Data in AJAX format...
 */
function aidtransparency_get_vimeo_data()
{
    $vimeoid = (int) $_POST['vimeoid'];
    $api_endpoint = "http://vimeo.com/api/v2/video/{$vimeoid}.json";
    //Get data via curl
    $curl = curl_init($api_endpoint);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
//    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $response = curl_exec($curl);
    curl_close($curl);
//    $videoJson = json_decode($response);

    header('Content-type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    print $response;
    exit;
}
add_action('wp_ajax_get_vimeo_data', 'aidtransparency_get_vimeo_data');
add_action('wp_ajax_nopriv_get_vimeo_data', 'aidtransparency_get_vimeo_data');



/**
 * Shows contact email in sidebar
 */
function aidtransparency_print_sidebar_contact( $email = null)
{
    $contact = null;
    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        $contact = $email;
    }else{
        $contact = sweetapple_get_theme_option('contact_email');
    }
    if($contact == null){
        return "";
    }else{
        ?>
    <aside id="sidebar_contact_email" class="widget widget_contact_email">
        <h3 class="widget-title"><?php _e('Media Contact'); ?></h3>
        <div class="textcontact">
            <p>Media Contact<br />
                <a href="mailto:<?php echo $contact; ?>"><?php echo $contact; ?></a>
            </p>
        </div>
    </aside>

    <?php
    }
}


/**
 * Filters the output of the tag cloud widget so all tags are same size...
 * @param $args
 * @return array
 */
function aidtransparency_set_tag_cloud_args($args) {
    $args = array(
        'unit'      => 'px',
        'smallest'  => '13px',
        'largest'   => '13px',
        'format'    => 'list',
    );
    return $args;
}
add_filter('widget_tag_cloud_args','aidtransparency_set_tag_cloud_args');



function aidtransparency_print_family_search()
{
    $query = get_search_query();
    ?>
<p><?php _e("If you can't find what you are looking for, click one of the links below to search other websites in the IATI family."); ?></p>
<ul class="icons">
    <li class="icon standard"><a href="http://iatistandard.org/?s=<?php echo $query; ?>" ><strong>Search</strong> IATI Standard</a></li>
    <li class="icon registry"><a href="http://iatiregistry.org/dataset"><strong>Search</strong> IATI Registry</a></li>
    <li class="icon community"><a href="http://discuss.iatistandard.org/categories"><strong>Search</strong> IATI Community</a></li>
</ul>
<?php
}




/**
 * Prints the list showing Events
 */
function aidtransparency_print_events()
{
    $events = new IATI_Event_Collection();
    if( $events->findPosts() > 0 ) :
        ?>
    <ol>
        <?php
        foreach ($events->getPosts() as $event) :
            ?>
            <li><a href="<?php echo get_permalink($event->ID); ?>" title="<?php echo $event->post_title; ?>"><?php echo $event->post_title; ?></a>
                <span class="when"><?php echo $event->getWhere(); ?> </span>
            </li>
            <?php endforeach; ?>
    </ol>
    <?php endif;
}


/**
 * Creates a table of IATI Implementation Events that is converted into TimelinesJS
 */
function aidtransparency_get_timeline($title = 'Aid Transparency', $description = 'A timeline of significant events')
{
    //query the loop and pull out just the IATI Timeline
    $args = array(
        'order'     => 'ASC',
        'orderby'   =>  'date',
        'numberposts'  => 0,
    );
    $timeline = new IATI_Timeline_Collection();
    if($timeline->findPosts($args) > 0) :
?>
<!--    <h2 class="timeline-title">--><?php //echo $title; ?><!--</h2>-->
<!--    <p class="timeline-description">--><?php //echo $description; ?><!--</p>-->
    <table id="timeline-table">
        <thead>
        <tr>
            <th class="date">Date</th>
            <th class="event">Event</th>
            <th class="details">Details</th>
            <th class="link">More Information</th>
            <th class="media">Media</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach( $timeline as $event) :
            /* @var $event IATI_Timeline */
            ?>
        <tr>
            <td class="date" data-startdate="<?php echo $event->getStartDate(); ?>" data-enddate="<?php echo $event->getEndDate(); ?>"><?php echo $event->getStartDate('d/m/Y'); ?></td>
            <td class="event"><?php echo $event->post_title; ?></td>
            <td class="details"><?php echo $event->post_content; ?><br />
                <?php if($event->document): ?>
                    Related Documentation: <a href="<?php echo $event->document; ?>">View</a>
                    <?php endif; ?>
            </td>
            <td class="link"><?php if($event->document) : ?><a href="<?php echo $event->document; ?>">Find out more</a><?php endif; ?></td>
            <td class="media" data-media-url="<?php echo $event->media; ?>" data-media-credit="<?php echo $event->credit; ?>" data-media-caption="<?php echo htmlentities($event->caption); ?>">
                <a href="<?php echo $event->media; ?>" target="_blank">Related Media</a>
                <?php if ($event->credit) { " by " . $event->credit; }  ?>
                <?php if ($event->caption) { "<br />" . $event->caption; }  ?>
            </td>
        </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif;
}


//function aidtransparency_timeline($atts)
//{
//    extract(
//        shortcode_atts( array(
//            'headline' => '',
//            'description'=> ''
//        ), $atts )
//    );
//    //Switch between different rendering functions..
//    $output = "";
//    switch ($type) {
//        case 'management':
//            $output =aidtransparency_print_people_management($orderby);
//            break;
//        case 'team':
//            $output = aidtransparency_print_people_team($orderby);
//            break;
//        default:
//            $output = aidtransparency_print_people($orderby);
//            break;
//    }
//    return $output;
//}
//add_shortcode('at_people', 'aidtransparency_timeline');


/**
 * Creates a table of IATI Implementation Events that is converted into TimelinesJS
 */
function aidtransparency_get_timeline_complex()
{
    //query the loop and pull out just the IATI Implementation Events
    $args = array(
        'order'     => 'ASC',
        'orderby'   =>  'date'
    );
    $implementations = new IATI_Implementation_Collection();
    if($implementations->findPosts($args)) :
        $implementations->getParents( new IATI_Organisation );//Bit more efficient as it saves a lot of DB queries
        ?>
    <table class="timeline-table">
        <thead>
        <tr>
            <th class="country">Country</th>
            <th class="event">Event</th>
            <th class="link">Link</th>
            <th class="details">Details</th>
            <th class="date">Date</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach( $implementations as $implementation) :
            /* @var $implementation IATI_Implementation */
            $organisation = $implementation->getParent( new IATI_Organisation);//We can still dynamically load the parents one at a time if Collection->getParents() hasn't run before.
            ?>
        <tr>
            <td class="country"><?php echo $organisation->post_title; ?></td>
            <td class="event"><?php echo $implementation->post_title; ?></td>
            <td class="link"><a href="<?php echo $implementation->document; ?>"><?php echo $implementation->document; ?></a></td>
            <td class="details"><?php echo $implementation->post_content; ?><br />
                Related Documentation: <a href="<?php echo $implementation->document; ?>">View</a>
            </td>
            <td class="date" data-startdate="<?php echo mysql2date('Y,n,j', $implementation->post_date); ?>" data-enddate="<?php echo mysql2date('Y,n,j', $implementation->post_date); ?>"><?php echo mysql2date('dS F Y', $implementation->post_date); ?></td>
        </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif;
}

/**
 *
 * @param array $options
 * @return \IATI_Person_Collection
 */
function aidtransparency_get_people($options)
{
    $peopleCollection = new IATI_Person_Collection();
    $peopleCollection->findPosts($options);
    return $peopleCollection;
}


function aidtransparency_print_people( $order )
{
    $peopleCollection = aidtransparency_get_people( array('orderby' => $order ) );
    return "All people";
}


function aidtransparency_print_people_management( $order )
{
    $output = "";
    $peopleCollection = aidtransparency_get_people( array('orderby' => $order, 'division' => 'management' ) );
    if($peopleCollection->count()){
        $output .= "<div class='people management'>";
        $count = 1;
        foreach ($peopleCollection as $person) {
            $output .= aidtransparency_get_person_markup($person, $count, 2, 'medium');
            $count++;
        }
        $output .= "</div>";
    }
    return $output;
}


function aidtransparency_print_people_team( $order )
{
    $output = "";
    $peopleCollection = aidtransparency_get_people( array('orderby' => $order, 'division' => 'team'  ) );
    if($peopleCollection->count()){
        $output .= "<div class='people team'>";
        $count = 1;
        foreach ($peopleCollection as $person) {
            $output .= aidtransparency_get_person_markup($person, $count, 2);
            $count++;
        }
        $output .= "</div>";
    }
    return $output;
}

/**
 *
 * @param IATI_Person $person
 * @param int $count
 * @param int $columns
 * @return string
 */
function aidtransparency_get_person_markup(IATI_Person $person, $count, $columns, $size = 'small' )
{
    $output = "";
    $rowClass = sweetapple_get_row_class($count, $columns);
    $photo = "";
    if ( has_post_thumbnail($person->ID) ) {
        $photoSrc = wp_get_attachment_image_src( get_post_thumbnail_id($person->ID), 'post-thumbnail' );
        $photo = "<img src='{$photoSrc[0]}' alt='' class='photo' />";
    }else{
        $defaultSrc = THEME_IMAGES . "/contact-person-placeholder-{$size}.png";
        $photo = "<img src='$defaultSrc' alt=''/>";
    }
    $output .= "<div class='vcard person $rowClass'>";
    $output .= "<div class='photo'>";
    $output .= $photo;
    $output .= "</div>";
    $output .= "<div class='details'>";
    $output .= "<h3 class='fn'>{$person->getName()}</h3>";
    if($person->hasJob()) {
        $output .= "<span class='role'>{$person->job}<span><br/>";
    }
    if($person->hasEmail()) {
        $output .= "<a class='email' href='mailto:{$person->email}'>{$person->email}</a><br/>";
    }
    if($person->hasPhone()) {
        $output .= "<span class='tel'><span class='type'>Phone:</span><span class='value'>{$person->phone}<span></span>";
    }
    $output .= "</div><!--.details -->";
    $output .= "</div><!--.vcard -->";
    return $output;
}

/**
 * Shortcode for embedding people into a Post
 * @param type $atts
 * @return string
 */
function at_people($atts)
{
    extract(
        shortcode_atts( array(
            'type' => 'all',
            'orderby'=> 'menu_order'
        ), $atts )
    );
    //Switch between different rendering functions..
    $output = "";
    switch ($type) {
        case 'management':
            $output =aidtransparency_print_people_management($orderby);
            break;
        case 'team':
            $output = aidtransparency_print_people_team($orderby);
            break;
        default:
            $output = aidtransparency_print_people($orderby);
            break;
    }
    return $output;
}
add_shortcode('at_people', 'at_people');
