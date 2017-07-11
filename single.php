<?php get_header(); ?>

<div id="primary">
    <div id="content">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <h1 class="post-title"><?php the_title(); ?></h1>

                <p class="meta">
                    <?php _e('Published by'); ?>: <?php the_author(); ?> on the <?php the_time('l, F jS, Y'); ?> -
                    <?php comments_popup_link(__('No Comments'), __('1 Comment'), __('% Comments')); ?>
                </p>

                <p class="meta sharing">
                    <?php //echo st_makeEntries(); ?>
                </p>

<!--                 <?php if (has_post_thumbnail()) : // check if the post has a Post Thumbnail assigned to it.
                the_post_thumbnail('single-thumb');
                endif; ?> -->
                <?php the_content(); ?>
            </article>

            <?php comments_template(); ?>

            <a href="#top" id="back-to-top"><?php _e('Back to top') ;?></a>
            <?php edit_post_link( __( 'Edit', 'iati' ), '<span class="edit-link">', '</span>' ); ?>
            <?php endwhile; ?>

        <?php else : ?>


        <h3 class="post-title">Not found!</h3>
        <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
        <?php include (TEMPLATEPATH . "/searchform.php"); ?>


        <?php endif; ?>

    </div>
    <!-- end content -->
</div><!-- end wrapper -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
