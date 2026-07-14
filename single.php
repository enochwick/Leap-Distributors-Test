<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<?php
// Newsletter posts embed their PDF as a page-flip viewer instead of a link.
$is_newsletter = has_category( 'newsletters' );
$pdf_url       = '';
if ( $is_newsletter ) {
	$pdfs = get_attached_media( 'application/pdf' );
	if ( ! empty( $pdfs ) ) {
		$first   = reset( $pdfs );
		$pdf_url = wp_get_attachment_url( $first->ID );
	}
}

// Top image for each post — matched to the card image on /news/ so the
// individual page and its listing card show the same visual. Keyed by slug.
$card_images = array(
	'product-agnostic-distribution-wins-for-surgeons-and-hospitals' => array( 'LeapDistributors3-e1769101290191.webp' ),
	'the-leap-dec-edition'                              => array( 'Leap-Distributors-BlogImg1-2.webp' ),
	'creating-your-sales-system-oct-2025'              => array( 'Leap-Distributors-BlogImg1-2.webp' ),
	'surgeon-choice-august-2025'                       => array( 'Leap-Distributors-BlogImg1-2.webp' ),
	'surgeon-preference-isnt-the-problem-its-the-point'=> array( 'LeapDistributors00-e1757003978173.webp' ),
	'hospitals-should-rethink-distributor-partnerships'=> array( 'LeapDistributorsBlog1-e1755620018111.webp' ),
	'the-magic-of-aggregation-jul-2025'                => array( 'Leap-Distributors-BlogImg1-2.webp' ),
	'why-independent-doesnt-mean-alone-anymore'        => array( 'Leap-Distributors-BlogImg-1.webp' ),
	'building-an-infrastructure-meant-to-share'        => array( 'Leap-Distributors-BlogImg-e1752595306217.webp' ),
	'scaling-smarter'                                  => array( 'LD_Blog-feature-image_16_1.webp', 'contain' ),
);
$post_slug   = get_post_field( 'post_name', get_post() );
$card_image  = '';
if ( isset( $card_images[ $post_slug ] ) ) {
	$card_image = get_template_directory_uri() . '/assets/images/blog-news/' . $card_images[ $post_slug ][0];
}
?>

<section class="page-hero page-hero--post">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="hero__gradient"></div>
	<div class="container">
		<div class="page-hero__inner">
			<p class="page-hero__eyebrow page-hero__eyebrow--post"><?php the_category( ' · ' ); ?></p>
			<h1 class="page-hero__title page-hero__title--post"><?php the_title(); ?></h1>
			<p class="page-hero__lead page-hero__lead--post" style="font-size:var(--text-base);">
				<?php echo get_the_date(); ?>
				<?php if ( get_the_author() ) : ?>
					&nbsp;·&nbsp; <?php the_author(); ?>
				<?php endif; ?>
			</p>
		</div>
	</div>
</section>

<section class="content-section">
	<div class="container">
		<?php if ( ! $is_newsletter && $card_image ) : ?>
			<figure class="post-hero-media">
				<img src="<?php echo esc_url( $card_image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="eager">
			</figure>
		<?php elseif ( ! $is_newsletter && has_post_thumbnail() ) : ?>
			<figure class="post-hero-media">
				<?php the_post_thumbnail( 'large' ); ?>
			</figure>
		<?php endif; ?>

		<div class="entry-content">
			<?php
			$content = apply_filters( 'the_content', get_the_content() );

			if ( $is_newsletter && $pdf_url ) {
				// Show the intro copy but drop the "Download" button — the flipbook replaces it.
				$content = preg_replace( '#<p>\s*<a[^>]*class="[^"]*btn[^"]*"[^>]*>.*?</a>\s*</p>#is', '', $content );
			}

			// Insight posts end with a "Let's talk" CTA (heading + list + contact link).
			// Wrap that trailing block so it reads as a visually separate section.
			if ( preg_match( '#<h[2-4][^>]*>(?:(?!<h[2-4]).)*$#is', $content, $m, PREG_OFFSET_CAPTURE ) ) {
				$tail = $m[0][0];
				if ( preg_match( '#href="[^"]*/contact#i', $tail ) ) {
					$content = substr( $content, 0, $m[0][1] ) . '<div class="post-cta">' . $tail . '</div>';
				}
			}

			echo $content;
			?>
		</div>

		<?php if ( $is_newsletter && $pdf_url ) : ?>
			<div class="pdf-flip-wrap">
				<div class="pdf-flip-viewport">
					<div class="pdf-flip" data-pdf="<?php echo esc_url( $pdf_url ); ?>" aria-label="Newsletter, flip through the pages">
						<div class="pdf-flip__loading">Loading newsletter…</div>
					</div>
				</div>
				<div class="pdf-flip__controls" hidden>
					<button type="button" class="pdf-flip__btn" data-flip-prev aria-label="Previous page">&larr;</button>
					<span class="pdf-flip__page" data-flip-page>1</span>
					<button type="button" class="pdf-flip__btn" data-flip-next aria-label="Next page">&rarr;</button>
																			</div>
			</div>
		<?php endif; ?>

		<?php
		$prev_post = get_previous_post();
		$next_post = get_next_post();
		if ( $prev_post || $next_post ) : ?>
			<nav class="news-pagination news-pagination--post" aria-label="Post navigation" style="border-top:1px solid var(--color-border);margin-top:var(--space-16);padding-top:var(--space-8);">
				<?php if ( $prev_post ) : ?>
					<a class="news-pagination__arrow" href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>" aria-label="Previous: <?php echo esc_attr( get_the_title( $prev_post ) ); ?>"><span aria-hidden="true">←</span></a>
				<?php endif; ?>
				<?php if ( $next_post ) : ?>
					<a class="news-pagination__arrow" href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" aria-label="Next: <?php echo esc_attr( get_the_title( $next_post ) ); ?>"><span aria-hidden="true">→</span></a>
				<?php endif; ?>
			</nav>
		<?php endif; ?>
	</div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
