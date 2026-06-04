<?php get_header(); ?>

<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="hero__gradient"></div>
	<div class="container">
		<div class="page-hero__inner">
			<nav class="breadcrumb" aria-label="Breadcrumb">
				<a href="<?php echo esc_url( home_url() ); ?>">Home</a>
				<span class="breadcrumb-sep">›</span>
				<span><?php the_title(); ?></span>
			</nav>
			<h1 class="page-hero__title"><?php the_title(); ?></h1>
		</div>
	</div>
</section>

<section class="content-section">
	<div class="container container--narrow">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		<?php endwhile; endif; ?>
	</div>
</section>

<?php get_footer(); ?>
