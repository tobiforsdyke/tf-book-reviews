<?php
/**
 * Default template for movie reviews
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="content site-main" role="main">

		<?php
		// Start the loop.
		while ( have_posts() ) : the_post();

			$the_field_prefix = TF_Book_Reviews::FIELD_PREFIX;

			$poster = get_the_post_thumbnail( get_the_ID(), 'full');
			$poster_url = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');

			$rating = (int)post_custom( $the_field_prefix . 'review_rating' );
			$rating_display = mr_get_rating_stars($rating);

			$director = wp_strip_all_tags( post_custom( $the_field_prefix . 'book_author' ) );
			$imdb_link = esc_url( post_custom( $the_field_prefix . 'book_link' ) );
			$year = (int)post_custom( $the_field_prefix . 'book_year' );

			$movie_terms = get_the_terms( get_the_ID(), 'book_types' );
			$movie_type = '';
			if ( $movie_terms && ! is_wp_error( $movie_terms ) ) {
				$movie_types = array();

				foreach ( $movie_terms as $term ) {
					$movie_types[] = $term->name;
				}

				$movie_type = implode( ", ", $movie_types );
			}
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class('hentry'); ?>>

				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<div class="left">

						<?php if ( isset($poster)) : ?>
							<div class="poster">
								<?php if ($imdb_link) : ?>
									<?php print '<a href="'. $imdb_link .'" target="_blank">' . $poster . '</a>'; ?>
								<?php else : ?>
									<?php print $poster; ?>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( !empty($rating_display)) : ?>
							<div class="rating rating-<?php print $rating; ?>">
								<?php print $rating_display; ?>
							</div>
						<?php endif; ?>

						<div class="movie-meta">

							<?php if ( !empty($director)) : ?>
								<div class="director">
									<label>Author:</label> <?php print $director; ?>
								</div>
							<?php endif; ?>

							<?php if ( !empty($movie_type)) : ?>
								<div class="types">
									<label>Genre:</label> <?php print $movie_type; ?>
								</div>
							<?php endif; ?>

							<?php if ( !empty($year)) : ?>
								<div class="release-year">
									<label>Published:</label> <?php print $year; ?>
								</div>
							<?php endif; ?>

						</div>

						<?php if ( !empty($imdb_link)) : ?>
							<div class="link">
								<a href="<?php print $imdb_link; ?>" target="_blank">Website »</a>
							</div>
						<?php endif; ?>

					</div> <!-- // left -->

					<div class="right">
						<div class="review-body">
							<?php the_content(); ?>
						</div>
					</div> <!-- // right -->
				</div>

				<?php edit_post_link( __( 'Edit' ), '<footer class="entry-footer"><span class="edit-link">', '</span></footer><!-- .entry-footer -->' ); ?>
			</article>
			<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

			// Previous/next post navigation.
			the_post_navigation( array(
				'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next' ) . '</span> ' .
				               '<span class="screen-reader-text">' . __( 'Next review:' ) . '</span> ' .
				               '<span class="post-title">%title</span>',
				'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous' ) . '</span> ' .
				               '<span class="screen-reader-text">' . __( 'Previous review:' ) . '</span> ' .
				               '<span class="post-title">%title</span>',
			) );

			// End the loop.
		endwhile;
		?>

	</main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_footer(); ?>

<?php
/*--------------------------
	Helper functions
--------------------------*/

function mr_get_rating_stars($rating = NULL) {
	$rating = (int) $rating;
	if ( $rating > 0 ) {
		$rating_stars   = array();
		$rating_display = '';

		// add filled stars first
		for ( $i = 0; $i < floor( $rating / 2 ); $i ++ ) {
			$rating_stars[] = '<span class="dashicons dashicons-star-filled"></span>';
		}

		// if the rating is odd, add a half-filled star
		if ( $rating % 2 === 1 ) {
			$rating_stars[] = '<span class="dashicons dashicons-star-half"></span>';
		}

		// pad the rest with empties
		$rating_stars = array_pad( $rating_stars, 5, '<span class="dashicons dashicons-star-empty"></span>' );

		return implode( "\n", $rating_stars );
	}

	return FALSE;
}
?>
