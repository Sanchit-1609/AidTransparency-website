<?php
/*
Template Name: Front Page
*/
?>
<?php get_header(); ?>

<div id="primary">

    <div id="home-featured">
        <h2 class="section-header"><?php _e('News &amp; Events'); ?></h2>
            <div class="articles">
                <?php iati_print_recent_posts(); ?>
            </div><!--.articles -->
    </div><!--#home-featured -->

    <div id="home-vimeo">
        <h2 class="section-header"><?php _e('Videos'); ?></h2>
        <iframe width="620" height="345" src="https://www.youtube.com/embed/cfjxWLB9kpE?rel=0" frameborder="0" allowfullscreen></iframe>
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
