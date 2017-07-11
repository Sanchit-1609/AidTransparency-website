<?php /* If there are no posts to display, such as an empty archive page */ ?>
<?php if ( ! have_posts() ) : ?>
	<div class="entry">
		<div class="entry-content">
			<p><?php _e( 'Sorry, no articles were found, please try a different search term'); ?></p>
		</div><!-- .entry-content -->
	</div><!-- #post-0 -->
<?php else: ?>
<div class="pagination top">
    <?php wp_pagenavi(); ?>
</div>
<div class="articles">
<?php while (have_posts()) : the_post(); ?>
	<article class="search-result">
		<div class="content">
			<h2 class="post-title"><?php the_title(); ?></h2>
			<?php iati_excerpt(30); ?>
			<p><a href="<?php the_permalink(); ?>"><?php the_permalink(); ?></a></p>
		</div>
	</article><!--.article -->

<?php endwhile;?>
</div><!--.articles -->
<div class="pagination bottom">
    <?php wp_pagenavi(); ?>
</div>
<div class="search-other-sites">
    <?php aidtransparency_print_family_search(); ?>
</div>
<?php endif; ?>
