<?php get_header(); ?>

<!-- ── Hero ──────────────────────────────────────────────── -->
<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="container">
		<div class="page-hero__inner">
			<span class="page-hero__eyebrow">News &amp; Insights</span>
			<h1 class="page-hero__title">What we're up to.</h1>
			<p class="page-hero__lead">Industry updates, company news, and partnership announcements from the Leap Distributors team.</p>
		</div>
	</div>
</section>

<!-- ── Posts ──────────────────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="news-grid" data-stagger>
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="news-card" data-stagger-child>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="news-card__image">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium_large' ); ?>
								</a>
							</div>
						<?php else : ?>
							<div class="news-card__image" style="background:var(--color-teal-dark);display:flex;align-items:center;justify-content:center;">
								<span style="font-size:var(--text-xs);font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:rgba(255,255,255,0.4);">Leap Distributors</span>
							</div>
						<?php endif; ?>
						<div class="news-card__body">
							<div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-3);">
								<?php
								$cats = get_the_category();
								if ( $cats ) :
								?>
									<span class="news-card__cat"><?php echo esc_html( $cats[0]->name ); ?></span>
								<?php endif; ?>
								<span style="font-size:var(--text-xs);color:var(--color-text-4);"><?php echo get_the_date( 'F Y' ); ?></span>
							</div>
							<h2 class="news-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<p class="news-card__excerpt"><?php the_excerpt(); ?></p>
							<a href="<?php the_permalink(); ?>" class="arrow-link" style="margin-top:var(--space-2);display:inline-flex;">Read more <span aria-hidden="true">→</span></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<div style="margin-top:var(--space-16);text-align:center;">
				<?php the_posts_navigation( [ 'prev_text' => '← Older', 'next_text' => 'Newer →' ] ); ?>
			</div>

		<?php else : ?>
			<div class="text-center" style="padding:var(--space-20) 0;">
				<h3>No news yet.</h3>
				<p style="color:var(--color-text-3);margin-top:var(--space-3);">Check back soon for the latest updates from Leap Distributors.</p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
