<?php get_header(); ?>

<div id="primary">
    <div id="content" class="clearfix">
        <?php if (have_posts()) : ?>

        <?php while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <?php the_content(); ?>

            </article>
            <?php endwhile; ?>

        <a href="#top" id="back-to-top"><?php _e('Back to top');?></a>
        <?php edit_post_link( __( 'Edit', 'iati' ), '<span class="edit-link">', '</span>' ); ?>

        <?php endif; ?>

    </div>
    <!-- end content -->
</div><!--#primary -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
