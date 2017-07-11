<?php
/*
  Template Name: Implementation Timeline
 */
?>
<?php get_header(); ?>

<div id="primary">
    <div id="content" class="clearfix">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php  post_class(); ?>>

            <div id="timelinejs">
                <?php aidtransparency_get_timeline(); ?>
            </div>

        <?php the_content(); ?>

        </article>

        <a href="#top" id="back-to-top"><?php _e('Back to top');?></a>
        <?php edit_post_link(__('Edit', 'iati'), '<span class="edit-link">', '</span>'); ?>
    <?php endwhile; ?>

<?php endif; ?>

    </div><!-- end content -->
</div><!--#primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
