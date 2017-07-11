<?php
/**
 * The Sidebar containing the main widget area.
 *
 * @package WordPress
 * @subpackage Aid Transparency
 * @since Aid Transparency 3.1
 */
?>
<div id="secondary" class="widget-area" role="complementary">
<?php
    //Let's us use different sidebars according to context
    if( is_front_page() ){
        get_sidebar("home");
    }elseif( is_page() ){
        get_sidebar("page");
    }elseif( is_search() || is_404() ){
        get_sidebar("search");
    }else{
        get_sidebar("blog");
    }
?>
</div><!-- #secondary .widget-area -->
