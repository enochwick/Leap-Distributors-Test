<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="hero__gradient"></div>
	<div class="container">
		<div class="page-hero__inner">
			<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> <span class="breadcrumb-sep">›</span> <a href="<?php echo esc_url( home_url( '/news/' ) ); ?>">News</a> <span class="breadcrumb-sep">›</span> <span><?php the_title(); ?></span></nav>
			<?php the_category( ' · ' ); ?>
			<h1 class="page-hero__title" style="font-size:clamp(2rem,4vw,3.5rem);"><?php the_title(); ?></h1>
			<p class="page-hero__lead" style="font-size:var(--text-base);">
				<?php echo get_the_date(); ?>
				<?php if ( get_the_author() ) : ?>
					&nbsp;·&nbsp; <?php the_author(); ?>
				<?php endif; ?>
			</p>
		</div>
	</div>
</section>

<section class="content-section">
	<div class="container container--narrow">
		<?php if ( has_post_thumbnail() ) : ?>
			<div style="border-radius:var(--radius-2xl);overflow:hidden;margin-bottom:var(--space-12);">
				<?php the_post_thumbnail( 'large', [ 'style' => 'width:100%;height:auto;' ] ); ?>
			</div>
		<?php endif; ?>

		<div class="entry-content">
			<?php the_content(); ?>
		</div>

		<div style="border-top:1px solid var(--color-border);margin-top:var(--space-16);padding-top:var(--space-8);">
			<?php the_post_navigation( [
				'prev_text' => '← %title',
				'next_text' => '%title →',
			] ); ?>
		</div>
	</div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
