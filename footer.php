    </div><!--#page-content -->
</div><!--#page -->
<div id="pre-footer">

    <?php if (is_front_page()) {
        get_template_part('home', 'icons');
    }?>

    <div id="footer-links">
        <ul>
            <li>
                <a href="<?php echo get_permalink(172); ?>">Site map</a>
            </li>
            <li>
                <a href="<?php echo get_permalink(6402); ?>">Privacy policy</a>
            </li>
            <li>
                <a href="<?php echo get_permalink(6415); ?>">Cookie policy</a>
            </li>
        </ul>
    </div>

    <div id="footer-additional">
        <div id="footer-cookie">
            <?php dynamic_sidebar('footer-1'); ?>
        </div>
        <div id="footer-social">
            <?php iati_print_social_links(); ?>
        </div>
        <!--#footer-social -->
    </div>
</div><!--#footer-wrapper -->
<footer id="footer">
    <div id="footer-credits">
       <!--  <span class="copyright">&copy; <?php echo date("Y"); echo " "; echo bloginfo('name'); ?></span> -->
    </div>
    <!--.footer-inner -->
</footer><!--#footer -->
<?php wp_footer(); ?>

<!-- DO NOT REMOVE: theideabureau-page-loaded -->

</body>
</html>
