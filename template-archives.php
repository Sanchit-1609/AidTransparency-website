<?php
    /*
    Template Name: Archives
    */
    ?>
<?php get_header(); ?>

    <div id="primary">
        <div id="content">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                        <?php the_content(); ?>

                        <?php $custom = get_post_custom($post->ID); ?>

                        <div class="resources-wrapper">
                            <?php
                                $custom = get_post_custom($post->ID);
                                for ($i = 1; $i <= 3; $i++) :
                            ?>
                                <h2><?php echo $custom["archives_box{$i}_title"][0]; ?></h2>
                                <div class="resource-content-wrapper resource-<?php echo $i; ?> collapseable">
                                    <?php echo $custom["archives_box{$i}_content"][0]; ?>
                                </div>
                            <?php endfor; ?>
                        </div>

                    </article><!--#post-<?php the_ID(); ?>-->

            <?php endwhile; endif; ?>

            <a href="#top" id="back-to-top"><?php _e('Back to top');?></a>
            <?php edit_post_link(__('Edit', 'iati'), '<span class="edit-link">', '</span>'); ?>


        </div><!--#content -->
    </div><!--#primary -->

    <?php get_sidebar(); ?>
    <?php get_footer(); ?>
