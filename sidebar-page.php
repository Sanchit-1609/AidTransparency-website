<?php
/**
 * The Sidebar containing the main widget area.
 *
 * @package WordPress
 * @subpackage Aid Transparency
 * @since Aid Transparency 3.1
 */
?>
<aside class="widget iati_subpages">
    <?php echo iati_nav_page_submenu(null,true);?>
</aside>

<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>
<!--<aside id="archives" class="widget">-->
<!--    <h3 class="widget-title">--><?php //_e( 'Archives', 'twentyeleven' ); ?><!--</h3>-->
<!--    <ul>-->
<!--        --><?php //wp_get_archives( array( 'type' => 'monthly' ) ); ?>
<!--    </ul>-->
<!--</aside>-->
<?php endif; // end sidebar widget area ?>
