<?php
/*
Template Name: News Page
*/
?>
<?php get_header(); ?>
<div id="content-wrapper" class="category">

    <header class="page-header">
        <h1 class="page-title"><?php
            printf( __( 'Category Archives: %s', 'twentyeleven' ), '<span>' . single_cat_title( '', false ) . '</span>' );
            ?></h1>
    </header>

    <?php get_template_part('article', 'loop'); ?>

</div><!--#primary -->

 <?php get_sidebar(); ?>

<?php get_footer(); ?>
