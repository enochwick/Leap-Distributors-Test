<?php get_header(); ?>

<section class="error-404" style="background:var(--color-off-white);">
	<div class="container text-center">
		<div class="error-404__code">404</div>
		<h1 style="margin-bottom:var(--space-4);">Page Not Found</h1>
		<p style="color:var(--color-text-3);font-weight:300;margin-bottom:var(--space-10);max-width:480px;margin-inline:auto;">The page you're looking for doesn't exist or may have moved. Let's get you back on track.</p>
		<div class="btn-row" style="display:flex;justify-content:center;gap:var(--space-4);flex-wrap:wrap;">
			<a href="<?php echo esc_url( home_url() ); ?>" class="btn btn--primary btn--lg">Back to Home</a>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--hero-ghost">Contact Us</a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
