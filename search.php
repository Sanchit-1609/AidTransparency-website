<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>
<div id="primary">
    <div id="content" class="clearfix">
        <h1>Search Results for '<?php echo get_search_query(); ?>'</h1>
        <?php get_template_part('loop', 'search'); ?>
    </div>
    <!-- end content -->
</div><!--#primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
