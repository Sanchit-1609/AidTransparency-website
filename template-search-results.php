<?php
/*
Template Name: Search Results Teamplate
*/
?>

<?php get_header(); ?>
    <div id="primary">
        <div id="content" class="clearfix">
                <div id="cse-search-results"></div>
                <script type="text/javascript">
					  var googleSearchIframeName = "cse-search-results";
					  var googleSearchFormName = "cse-search-box";
					  var googleSearchFrameWidth = 300;
					  var googleSearchDomain = "www.google.com";
					  var googleSearchPath = "/cse";
                </script>
                <script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>
        </div><!-- end content -->
    </div><!--#primary -->
<?php get_footer(); ?>
