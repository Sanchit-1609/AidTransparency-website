<?php get_header(); ?>

	<div id="content" class="clearfix">

		<div id="content-left">

			<div class="box-left clearfix">

				<h3 class="post-title">Archives by Month:</h3>

				<ul>
					<?php wp_get_archives('type=monthly'); ?>
				</ul>

			</div>

			<div class="box-left clearfix">

				<h3 class="post-title">Archives by Subject:</h3>

				<ul>
					<?php wp_list_categories('title_li='); ?>
				</ul>

			</div>

	  </div><!-- end content-left -->

	  <?php get_sidebar(); ?>

</div><!-- end content -->

<?php get_footer(); ?>
