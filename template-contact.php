<?php
/*
  Template Name: Contact
 */
get_header();
?>
    <div id="primary">
        <div id="content">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class();?>>
                    <?php the_content(); ?>
                </article>
            <?php endwhile; endif; ?>
            <a href="#top" id="back-to-top"><?php _e('Back to top');?></a>
            <?php edit_post_link(__('Edit', 'iati'), '<span class="edit-link">', '</span>'); ?>
        </div><!--#content -->
    </div><!--#content-wrapper -->
    <div id="secondary">
        <aside class="widget contact-secondary">
            <?php //echo types_render_field("post_secondary", array('raw' => false));?>
        </aside>
    </div>

<?php get_footer(); ?>
