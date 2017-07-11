<?php
/*
Template Name: Front Page
*/
?>
<?php get_header(); ?>

<div id="home-strapline">
    <?php //echo types_render_field("home_page_strapline", array('raw' => false));?>
</div>

<div id="primary">

    <div id="home-featured">
        <h2 class="section-header"><?php _e('News &amp; Events'); ?></h2>
            <div class="articles">
                <?php iati_print_recent_posts(); ?>
            </div><!--.articles -->
    </div><!--#home-featured -->

    <div id="home-vimeo">
        <h2 class="section-header"><?php _e('Videos'); ?></h2>
        <?php aidtransparency_print_vimeo_videos($post);?>
        <?php _e('View other videos on our'); ?> <a
            href="<?php echo sweetapple_get_theme_option('vimeo_url_social'); ?>"
            target="_blank">Vimeo</a> <?php _e('channel'); ?>.
    </div><!--#content-wrapper -->
</div><!--#home-vimeo -->

<?php get_sidebar(); ?>

<div id="home-intro-content">
    <!--
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <?php the_content(); ?>
    </article>
    <?php endwhile; endif; ?>
    -->
</div><!-- end content from page -->


<?php get_footer(); ?>
