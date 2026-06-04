<?php get_header(); ?>

<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="hero__gradient"></div>
	<div class="container">
		<div class="page-hero__inner">
			<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> <span class="breadcrumb-sep">›</span> <span>News</span></nav>
			<span class="page-hero__eyebrow">News &amp; Updates</span>
			<h1 class="page-hero__title">What's Happening at Leap</h1>
			<p class="page-hero__lead">Industry insights, company news, and partnership announcements from the Leap Distributors team.</p>
		</div>
	</div>
</section>

<section class="content-section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="news-grid" data-stagger>
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="news-card" data-stagger-child data-glow>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="news-card__image">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium_large' ); ?>
								</a>
							</div>
						<?php endif; ?>
						<div class="news-card__body">
							<div class="news-card__cat"><?php the_category( ' · ' ); ?></div>
							<h2 class="news-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<p class="news-card__excerpt"><?php the_excerpt(); ?></p>
							<div class="news-card__meta"><?php echo get_the_date(); ?></div>
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
