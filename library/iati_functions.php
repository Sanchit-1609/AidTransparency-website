<?php


// remove junk from head
add_action('wp_head', 'iati_clean_head');

function iati_clean_head()
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




/**
 * Add spme utility buttons to the TinyMCE Editor
 * @param $buttons
 * @return mixed
 */
function iati_mce_buttons_2( $buttons ) {
    array_unshift( $buttons, 'styleselect', 'sup' );
    return $buttons;
}
add_filter( 'mce_buttons_2', 'iati_mce_buttons_2' );


/**
 * Add specific styles the to the editor
 * @param $settings
 * @return mixed
 */
function iati_mce_before_init( $settings ) {

    $style_formats = array(

        array(
            'title' => 'Theme Accent Colour',
            'inline' => 'span',
            'selector' => 'h1,h2,h3,h4,h5,h6',
            'classes' => 'theme-accent'
        ),
        array(
            'title' => 'Link With Icon',
            'selector' => 'a',
            'classes' => 'fileicon'
        ),
        array(
            'title' => 'List of Attachments',
            'block' => 'div',
            'classes' => 'file-list',
            'wrapper' => true
        )
    );

    $settings['style_formats'] = json_encode( $style_formats );

    return $settings;

}
add_filter( 'tiny_mce_before_init', 'iati_mce_before_init' );


function iati_my_editor_style() {
    add_editor_style();
}
add_action( 'admin_init', 'iati_my_editor_style' );



function iati_page_x_of_y()
{

    // bring the wp_query to local scope
    global $wp_query;

    // get the current page number
    $current_page = get_query_var('paged');
    if (!$current_page > 0) {
        $current_page = 1;
    }

    // get the number of posts per page
    $posts_per_page = get_query_var('posts_per_page');

    // get the ceiling number of posts for this current page
    $max_page_posts = $posts_per_page * $current_page;

    // get the count of all posts in the current wp_query
    $total_posts = $wp_query->found_posts;

    if ($max_page_posts > $total_posts) {
        $max_page_posts = $total_posts;
    }

    echo "Displaying " . (($posts_per_page * $current_page) - ($posts_per_page - 1)) . "-" . $max_page_posts . " of " . $total_posts;
}


function iati_excerpt($words = 55, $link_text = '...', $allowed_tags = '', $container = 'p', $smileys = 'no', $more_link = false)
{

    global $post;

    if ($allowed_tags == 'all') {
        $allowed_tags = '<a>,<i>,<em>,<b>,<strong>,<ul>,<ol>,<li>,<span>,<blockquote>,<img>';
    }

    $text = preg_replace('/\[.*\]/', '', strip_tags($post->post_content, $allowed_tags));
    $text = explode(' ', $text);
    $tot = count($text);

    for ($i = 0; $i < $words; $i++) {
        $output .= $text[$i] . ' ';
    }

    if ($smileys == "yes") {
        $output = convert_smilies($output);
    }
    ?><p><?php echo force_balance_tags($output) ?><?php if ($i < $tot) : ?> ...<?php else : ?></p><?php endif; ?>
    <?php
    if ($i < $tot) :
        if ($container == 'p' || $container == 'div') :
            ?></p><?php
        endif;
        if ($container != 'plain') :
            ?><<?php echo $container; ?> class="more"><?php if ($container == 'div') : ?><p><?php
        endif;
        endif;

        if ($more_link) :
            ?><a href="<?php the_permalink(); ?>" title="<?php echo $link_text; ?>"><?php echo $link_text; ?></a><?php
        endif;

        if ($container == 'div') :
            ?></p><?php endif;
        if ($container != 'plain') :
            ?></<?php echo $container; ?>><?php
        endif;

        if ($container == 'plain' || $container == 'span') :
            ?></p><?php
        endif;
    endif;
}


/**
 * Not particuarly pretty, but a way of getting the specific page sub-navigation structure required...
 * @param null $title
 * @param bool $ancestors
 * @return string
 */
function iati_nav_page_submenu( $title = null, $ancestors = false)
{
    global $post;

    $page_ids = null;

    if (sweetapple_is_subpage() && !sweetapple_has_subpages()) {
        $sibling_ids = sweetapple_get_sibling_page_ids();
        $parent_sibling_ids = sweetapple_get_child_page_ids( $post->post_parent);
        $page_ids = ( is_array($sibling_ids ) ) ? array_merge($sibling_ids, $parent_sibling_ids) : $parent_sibling_ids ;
        $page_ids[] = $post->ID;
    }

    if (sweetapple_is_subpage() && sweetapple_has_subpages()) {
        $sibling_ids = sweetapple_get_sibling_page_ids();
        $children_ids = sweetapple_get_child_page_ids($post->ID);
        $page_ids = array_merge($sibling_ids, $children_ids);
        $page_ids[] = $post->ID;
    }

    if( !sweetapple_is_subpage() && sweetapple_has_subpages() ){
        $page_ids = sweetapple_get_child_page_ids();
    }

    //Find the page ancestors and add to the array...
    if( $post->post_parent != 0){
        $ancestor_ids = get_ancestors($post->ID, get_post_type($post) );
        if(count($ancestor_ids) > 0){
            $page_ids = array_merge($page_ids, $ancestor_ids);
        }

        //Get the top level siblings...
        $firstLevelChildren = get_children( array(
            'post_parent' => array_pop( $ancestor_ids),
            'post_type'   => 'page',
            'post_status' => 'published'
        ) );
        $firstLevelChildrenIds = array_keys($firstLevelChildren);
        $page_ids = array_merge($page_ids, $firstLevelChildrenIds);
    }


    //If top level page make sure we show the current page...
    if( $post->post_parent == 0){
        $page_ids[] =  $post->ID;
    }

    $list_title = "";
    if(!empty( $title) ){
        $list_title = "<li class='list_title'>$title</li>" . PHP_EOL;
    }

    $output = "";

    if( $page_ids ) {
        $output .= "<ul class='submenu'>" . PHP_EOL;
        $output .= $list_title;
        $page_ids = implode(',', $page_ids);
        $output .= sweetapple_nav_submenu($page_ids);
        $output .= "</ul>" . PHP_EOL;
    }
    return $output;
}


/**
 * Prints out the links to the IATI family of sites...
 */
function iati_family_sites_navigation()
{
    ?>
<ul class="iati-family">
    <li class="transparency"><a href="http://www.aidtransparency.net/"><?php _e('Aid Transparency', 'iait'); ?></a></li>
    <li class="standard"><a href="http://iatistandard.org/"><?php _e('IATI Standard', 'iait'); ?></a></li>
    <li class="registry"><a href="http://www.iatiregistry.org/"><?php _e('IATI Data', 'iait'); ?></a></li>
    <li class="community"><a href="http://discuss.iatistandard.org/t/welcome-to-iati-discuss"><?php _e('IATI Community', 'iait'); ?></a></li>
</ul>
<?php
}



/**
 * Print  an array of posts for use in multiple loops
 * @param array $args
 */
function iati_print_recent_posts($args = null)
{
    global $post;
    // Store a reference to the current global $post so we can restore the loop...
    $currentPost = clone($post);

    $queryArgs = array(
        'post_type' => 'post',
        'posts_per_page' => 2,
        'ignore_sticky_posts' => true,
        'orderby' => 'date'
    );

    if( $args) {
        $queryArgs = array_merge($queryArgs, $args) ;
    }
    $articles = new WP_Query($queryArgs);
    while ($articles->have_posts()) : $articles->the_post();
        ?>
    <article <?php post_class(); ?>>
        <div class="article-image">
            <?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'featured-thumb' ); else : ?>
            <a class="featured-thumb" href="<?php the_permalink(); ?>" title="">
                <img src="<?php echo THEME_IMAGES; ?>/default-thumb.png" alt="" />
            </a>
            <?php endif; ?>
        </div><!--.featured-image -->

        <div class="article-summary">
            <h2 class="post-title"><a class="post-title-link" href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
            <p class="meta">Published by <?php the_author();?> - <?php days_since(); ?></p>
            <?php echo get_the_excerpt(); ?>
        </div><!--.article-summary -->

    </article><!--.article -->
    <?php
    endwhile;
    $post = clone($currentPost);
}


/**
 * Shows social media links on sidebar...
 */
function iati_print_social_links()
{
    ?>
<aside id="sidebar_social_links" class="widget widget_social_links">
    <div class="text-widget">
        <ul class="social-links">
            <li class="social facebook"><a href="<?php echo sweetapple_get_theme_option('facebook_url_social');?>">Facebook</a></li>
            <li class="social twitter"><a href="<?php echo sweetapple_get_theme_option('twitter_url_social');?>">Twitter</a></li>
            <li class="social linkedin"><a href="<?php echo sweetapple_get_theme_option('linkedin_url_social');?>">LinkedIn</a></li>
            <li class="social rss"><a href="<?php echo bloginfo('rss2_url'); ?>">RSS</a></li>
        </ul>
    </div>
</aside>
<?php
}

/**
 *	Get tweets with specified query
 */
function iati_tweets($query, $limit = 3){
	$json = file_get_contents('http://search.twitter.com/search.json?q='.$query);
	if($json){
		$tweets = json_decode($json);
		if($tweets){
			if($tweets->results){
				array_splice($tweets->results, $limit);
				return $tweets->results;
			}
		}
	}
	return false;
}

function iati_print_dev_warning()
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


class IATI_Widget_Subnavigation extends WP_Widget {


    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'iati_subpages', // Base ID
            'IATI Sub-Navigation', // Name
            array( 'description' => __( 'Display Page Sub-navigation in the sidebar', 'iati' ), ) // Args
        );

    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        $instance['title'] = strip_tags( $new_instance['title'] );
        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>

    <?php
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        extract( $args );

        echo $before_widget;

        $title = apply_filters( 'widget_title', $instance['title'] );
        if ( ! empty( $title ) ){
            echo $before_title . $title . $after_title;
        }

        $order = ( $instance['random'] == 'true' ) ? "rand" : "menu_order" ;

        $options = array(
            'numberposts' => $instance['number'],
            'orderby' => $order,
        );
        $output = $this->get_html( $options );
        //Make the output filterable so we can modify the output in child themes.
        echo apply_filters("sweetapple_testimonial_widget_display", $output);
        echo $after_widget;
    }


    //Returns the widget output
    public function get_html( $args = null ) {
        $output = iati_nav_page_submenu(null,true);
        return $output;
    }

}
