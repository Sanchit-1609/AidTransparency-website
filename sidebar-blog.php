<?php
/**
 * The Sidebar containing the main widget area.
 *
 * @package WordPress
 * @subpackage Aid Transparency
 * @since Aid Transparency 3.1
 */
?>
    <?php aidtransparency_print_sidebar_contact(); ?>

    <?php iati_print_social_links(); ?>

    <?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) : ?>

<!--    <aside id="archives" class="widget">-->
<!--        <h3 class="widget-title">--><?php //_e( 'Archives', 'twentyeleven' ); ?><!--</h3>-->
<!--        <ul>-->
<!--            --><?php //wp_get_archives( array( 'type' => 'monthly' ) ); ?>
<!--        </ul>-->
<!--    </aside>-->
    <?php endif; // end sidebar widget area ?>
