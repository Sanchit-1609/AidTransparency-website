<?php
/*
* Template Name: Annual Report
*/
?>
<?php get_header(); the_post(); ?>

	<div id="page-content">

	<div class="annual-report">

	   <div class="wrapper">

	     <h1 class="report-title">Annual Report 2013</h1>

		<span class='st_facebook_hcount' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='facebook'></span><span class='st_twitter_hcount' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='twitter'></span><span class='st_plusone_hcount' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='plusone'></span><span class='st_email_hcount' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='email'></span>

		<?php the_content(); ?>

	     <div class="buttons buttons-top">
            <a href="http://issuu.com/iati/docs/iati-annual-report-2013" target="_blank" class="btn-report-download"><b>View Report Online</b></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         </div>
		<div class="buttons">
			<a href="http://www.aidtransparency.net/reports/IATI-annual-report-2013.pdf" target="_blank" class="btn-report-download2">Download</b> Report (PDF)</a>&nbsp;&nbsp;
	       <a href="http://www.aidtransparency.net/wp-content/uploads/2013/04/signatory-summary-table.csv" class="btn-report-download2">Download Report Data (.csv)</a>
	     </div>

	     <div class="tweets">

	       <header>
	         <h2>Join the conversation</h2>
	         <p>Share your thoughts and opinions with us using #iatireport</p>
	       </header>

	       <div class="tweet-list">

			<?php
				$tweets = iati_tweets('%23IATI%20OR%20%23IATIReport', 3);

				if($tweets):
					foreach($tweets as $tweet):
			?>

				<div class="tweet">

		           <div class="twitter-title"><a target="_blank" href="http://twitter.com/<?php echo $tweet->from_user; ?>/status/<?php echo $tweet->id; ?>"><span class="twitter-name"><?php echo $tweet->from_user_name; ?></span> <span class="twitter-handle">@<?php echo $tweet->from_user; ?></span></a></div>

		           <div class="content">
		             <p><?php echo $tweet->text; ?></p>
		           </div>

		         </div><!-- tweet -->

			<?php
					endforeach;
				endif;
			?>

	       </div><!-- tweet-list -->

	       <a href="https://twitter.com/IATI_aid" class="btn-report-follow" target="_blank">Follow us on Twitter</a>

	     </div><!-- tweets -->

	   </div><!-- wrapper -->

	 </div><!-- annual-report -->
   </div><!--#page-content -->
<?php get_footer(); ?>
