<?php get_header(); ?>

<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="hero__gradient"></div>
	<div class="container">
		<div class="page-hero__inner">
			<span class="page-hero__eyebrow">Search Results</span>
			<?php if ( get_search_query() ) : ?>
				<h1 class="page-hero__title">Results for &ldquo;<?php echo esc_html( get_search_query() ); ?>&rdquo;</h1>
			<?php else : ?>
				<h1 class="page-hero__title">Search</h1>
			<?php endif; ?>
			<form class="search-hero__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input class="search-hero__input" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Search the site…" aria-label="Search" maxlength="100">
				<button class="search-hero__btn" type="submit" aria-label="Search">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
				</button>
			</form>
		</div>
	</div>
</section>

<section class="content-section">
	<div class="container">

		<?php if ( have_posts() ) : ?>

			<p class="search-results__count reveal">
				<?php
				global $wp_query;
				printf(
					'%s result%s found',
					number_format_i18n( $wp_query->found_posts ),
					$wp_query->found_posts !== 1 ? 's' : ''
				);
				?>
			</p>

			<div class="search-results">
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="search-result reveal">
						<div class="search-result__type"><?php echo esc_html( ucfirst( get_post_type() ) ); ?></div>
						<h2 class="search-result__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>
						<?php if ( has_excerpt() || get_the_excerpt() ) : ?>
							<p class="search-result__excerpt"><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
						<?php endif; ?>
						<a class="search-result__link arrow-link" href="<?php the_permalink(); ?>">
							Read more <span aria-hidden="true">→</span>
						</a>
					</article>
				<?php endwhile; ?>
			</div>

			<div style="margin-top:var(--space-16);text-align:center;">
				<?php the_posts_navigation( [ 'prev_text' => '← Previous', 'next_text' => 'Next →' ] ); ?>
			</div>

		<?php else : ?>

			<div style="padding:var(--space-20) 0;text-align:center;">
				<p style="font-size:var(--text-lg);color:var(--color-text-3);">No results found for &ldquo;<?php echo esc_html( get_search_query() ); ?>&rdquo;.</p>
				<p style="color:var(--color-text-4);margin-top:var(--space-3);">Try a different search term, or <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" style="color:var(--color-blue);">contact us directly</a>.</p>
			</div>

		<?php endif; ?>

	</div>
</section>

<?php get_footer(); ?>
