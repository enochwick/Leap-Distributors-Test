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
?>

<section class="page-hero page-hero--post">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="hero__gradient"></div>
	<div class="container">
		<div class="page-hero__inner">
			<p class="page-hero__eyebrow page-hero__eyebrow--post"><?php the_category( ' · ' ); ?></p>
			<h1 class="page-hero__title page-hero__title--post" style="font-size:clamp(2rem,4vw,3.5rem);"><?php the_title(); ?></h1>
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
		<?php if ( has_post_thumbnail() && ! ( $is_newsletter && $pdf_url ) ) : ?>
			<figure class="post-hero-media">
				<?php the_post_thumbnail( 'large' ); ?>
			</figure>
		<?php endif; ?>

		<div class="entry-content">
			<?php
			if ( $is_newsletter && $pdf_url ) {
				// Show the intro copy but drop the "Download" button — the flipbook replaces it.
				$content = apply_filters( 'the_content', get_the_content() );
				echo preg_replace( '#<p>\s*<a[^>]*class="[^"]*btn[^"]*"[^>]*>.*?</a>\s*</p>#is', '', $content );
			} else {
				the_content();
			}
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
					<span class="pdf-flip__divider" aria-hidden="true"></span>
					<button type="button" class="pdf-flip__btn" data-flip-zoom-out aria-label="Zoom out">&minus;</button>
					<button type="button" class="pdf-flip__btn" data-flip-zoom-in aria-label="Zoom in">+</button>
				</div>
			</div>
		<?php endif; ?>

		<?php
		$prev_post = get_previous_post();
		$next_post = get_next_post();
		if ( $prev_post || $next_post ) : ?>
			<nav class="news-pagination" aria-label="Post navigation" style="border-top:1px solid var(--color-border);margin-top:var(--space-16);padding-top:var(--space-8);">
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
