<?php get_header(); ?>

<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="hero__gradient"></div>
	<div class="container">
		<div class="page-hero__inner">
			<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> <span class="breadcrumb-sep">›</span> <span>Orders</span></nav>
			<span class="page-hero__eyebrow">Order Management</span>
			<h1 class="page-hero__title">Manage Your Orders</h1>
			<p class="page-hero__lead">Access your order history, track shipments, and manage your account — all in one place.</p>
		</div>
	</div>
</section>

<section class="content-section">
	<div class="container container--narrow">
		<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:var(--space-6);margin-bottom:var(--space-12);" data-stagger>
			<div class="card reveal" data-stagger-child data-glow style="text-align:center;padding:var(--space-10);">
				<div class="feature-card__icon" style="background:rgba(42,125,225,0.08);color:var(--color-blue);margin:0 auto var(--space-5);">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
				</div>
				<h4 style="margin-bottom:var(--space-3);">Place an Order</h4>
				<p style="color:var(--color-text-2);font-weight:300;margin-bottom:var(--space-6);font-size:var(--text-sm);">Submit a new product order through our team or ordering portal.</p>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--sm">Start Order</a>
			</div>
			<div class="card reveal" data-stagger-child data-glow style="text-align:center;padding:var(--space-10);">
				<div class="feature-card__icon" style="background:rgba(42,125,225,0.08);color:var(--color-blue);margin:0 auto var(--space-5);">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
				</div>
				<h4 style="margin-bottom:var(--space-3);">Track a Shipment</h4>
				<p style="color:var(--color-text-2);font-weight:300;margin-bottom:var(--space-6);font-size:var(--text-sm);">Check the status and location of your current deliveries.</p>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--outline btn--sm">Track Order</a>
			</div>
		</div>

		<div class="card reveal" data-glow style="padding:var(--space-10);text-align:center;">
			<h3 style="margin-bottom:var(--space-4);">Need Help with an Order?</h3>
			<p style="color:var(--color-text-2);font-weight:300;margin-bottom:var(--space-8);">Our client support team is available to assist with orders, returns, billing, and product questions.</p>
			<div style="display:flex;justify-content:center;flex-wrap:wrap;gap:var(--space-4);">
				<a href="tel:+18887765553" class="btn btn--primary">Call +1 888-776-5553</a>
				<a href="mailto:info@leapdistributors.com" class="btn btn--hero-ghost">Email Support</a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
