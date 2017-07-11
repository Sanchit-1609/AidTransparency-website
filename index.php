<?php
/*
Template Name: News Page
*/
?>
<?php get_header(); ?>

    <div id="primary" class="category">

        <header class="page-header">
            <h1 class="page-title"><?php _e("News &amp; Events", "iati"); ?></h1>
        </header>

        <?php get_template_part('article', 'loop'); ?>

    </div><!--#primary -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
