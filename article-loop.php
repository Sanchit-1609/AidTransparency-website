<?php
/**
 * Default file for lopping and displaying articles
 */
 ?>
<div class="pagination top">
    <?php  //wp_pagenavi(); ?>
</div>

<div class="articles">
    <?php if (have_posts()) : ?>

    <?php while (have_posts()) : the_post(); ?>

        <article <?php post_class(); ?>>
            <div class="article-image">
                <?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'featured-thumb' ); else : ?>
                <a class="featured-thumb" href="<?php the_permalink(); ?>" title="">
                    <img src="<?php echo THEME_IMAGES; ?>/default-thumb.png" alt="" />
                </a>
                <?php endif; ?>
            </div><!--.featured-image -->

            <div class="article-summary">
                <h2 class="post-title"><a class="post-title-link" href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                <p class="meta">Published by <?php the_author();?> - <?php days_since(); ?> - <?php comments_popup_link(__('No Comments'), __('1 Comment'), __('% Comments')); ?></p>
                <?php echo get_the_excerpt(); ?>
            </div><!--.article-summary -->

        </article><!--.article -->

        <?php endwhile; ?>
    <?php endif; ?>
</div><!--.articles -->
<div class="pagination bottom">
    <?php wp_pagenavi(); ?>
</div>
