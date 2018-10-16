<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="no-js ie6" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<!--<meta name="viewport" content="width=device-width,initial-scale=auto">-->

	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
	Remove this if you use the .htaccess -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php
	    /*
	     * Print the <title> tag based on what is being viewed.
	     */
	    global $page, $paged;

	    wp_title( '|', true, 'right' );

	    // Add the blog name.
	    bloginfo( 'name' );

	    // Add the blog description for the home/front page.
	    $site_description = get_bloginfo( 'description', 'display' );
	    if ( $site_description && ( is_home() || is_front_page() ) )
	        echo " | $site_description";

	    // Add a page number if necessary:
	    if ( $paged >= 2 || $page >= 2 )
	        echo ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );

	    ?>
	</title>

	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) ) {
	    wp_enqueue_script( 'comment-reply' );
	}
	wp_head();
	?>

	<style>#header { margin-bottom: 4em; }</style>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-6109435-3"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-6109435-3');
	</script>

</head>

<body <?php body_class(); ?>>
    <a id="top"></a>
    <?php iati_print_dev_warning(); ?>
    <div id="page">
        <div id="header">

			<style>

				.header-message {
					background-color: #f7fbb9;
					padding: 8px;
				}

				.header-message p {
					margin: 0;
				}

			</style>

			<div class="header-message">
				<p>This website will be switched off on Monday 29 October. All of the content on this site can now be found on IATI’s brand new website: <a href="http://iatistandard.org/">iatistandard.org</a>. Please email <a href="mailto:support@iatistandard.org">support@iatistandard.org</a> with any questions.</p>
			</div>

            <div class="header-wrapper">
                <a id="logo" href="<?php bloginfo('url'); ?>"><?php echo esc_attr( get_bloginfo( 'name' ) ); ?></a>

                <form role="search" method="get" id="searchform" action="/">
                    <input type="text" value="" name="s" id="s" placeholder="Search Aid Transparency">
                    <input type="submit" id="searchsubmit" value="Search" class="button">
                </form>
            </div><!-- end wrapper -->

            <nav id="main-nav" class="nav" role="navigation">

                <h3 class="assistive-text"><?php _e( 'Main menu', 'iati' ); ?></h3>
                <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff. */ ?>
                <div class="skip-link"><a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to primary content', 'iati' ); ?>"><?php _e( 'Skip to primary content', 'iati' ); ?></a></div>
                <div class="skip-link"><a class="assistive-text" href="#secondary" title="<?php esc_attr_e( 'Skip to secondary content', 'iati' ); ?>"><?php _e( 'Skip to secondary content', 'iati' ); ?></a></div>
                <?php iati_family_sites_navigation(); ?>
                <?php wp_nav_menu(array('menu_class' => 'menu-main', 'container'=>'ul', 'theme_location' => 'primary-navigation', 'menu' => 'primary-navigation' )); ?>
            </nav>

        </div><!-- end header -->

<div id="page-content">

    <?php if(function_exists('bcn_display') && !is_front_page()) :?>
    <div id="nav-breadcrumbs">
    <?php bcn_display(); ?>
    </div><!--.breadcrumbs -->
    <?php endif; ?>
