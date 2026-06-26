<?php get_header(); ?>

<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="hero__gradient"></div>
	<div class="container">
		<div class="page-hero__inner page-hero__inner--center">
			<span class="page-hero__eyebrow">Join the Team</span>
			<h1 class="page-hero__title">Build the Future of Healthcare Distribution.</h1>
			<p class="page-hero__lead">We're a fast-moving, mission-driven team that values people who are hungry, humble, and committed to doing good work. Come help us move healthcare forward.</p>
		</div>
	</div>
</section>

<!-- ── Culture ────────────────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<div class="grid-2" style="gap:var(--space-20);align-items:center;">
			<div>
				<span class="section-label section-label--no-line reveal">Life at Leap</span>
				<h2 class="reveal" style="margin-bottom:var(--space-6);">A Place Where Your Work Actually Matters</h2>
				<p class="reveal" style="color:var(--color-text-2);font-weight:300;margin-bottom:var(--space-8);">At Leap, you're not just filling an order, you're part of a chain that changes patients' lives. Every role here contributes to healthcare getting better. That's not something you find everywhere.</p>
				<div style="display:flex;flex-direction:column;gap:var(--space-5);">
					<div class="reveal" style="display:flex;gap:var(--space-4);align-items:flex-start;">
						<div style="width:8px;height:8px;border-radius:50%;background:var(--color-orange);margin-top:6px;flex-shrink:0;"></div>
						<div><strong>Mission-first culture</strong> — We make decisions based on what's right for healthcare, not just what's profitable.</div>
					</div>
					<div class="reveal" style="display:flex;gap:var(--space-4);align-items:flex-start;">
						<div style="width:8px;height:8px;border-radius:50%;background:var(--color-orange);margin-top:6px;flex-shrink:0;"></div>
						<div><strong>Ownership mentality</strong> — You won't get lost in a giant org chart here. Your contributions are visible and valued.</div>
					</div>
					<div class="reveal" style="display:flex;gap:var(--space-4);align-items:flex-start;">
						<div style="width:8px;height:8px;border-radius:50%;background:var(--color-orange);margin-top:6px;flex-shrink:0;"></div>
						<div><strong>Growing fast</strong> — We're expanding our network, our team, and our impact. There's room to grow with us.</div>
					</div>
				</div>
			</div>
			<div class="reveal-right">
				<div style="border-radius:var(--radius-2xl);overflow:hidden;aspect-ratio:1/1;background:var(--color-surface);">
					<img src="<?php echo get_template_directory_uri(); ?>/assets/images/built-for/careers-culture.webp" alt="The Leap Distributors team collaborating in the office" style="width:100%;height:100%;object-fit:cover;" loading="lazy" decoding="async">
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ── Open Roles ─────────────────────────────────────────── -->
<section class="content-section content-section--alt">
	<div class="container">
		<div class="section-header section-header--center">
			<span class="section-label section-label--blue reveal">Open Roles</span>
			<h2 class="reveal">Current Opportunities</h2>
		</div>

		<div style="max-width:800px;margin-inline:auto;">
			<div class="card reveal" data-glow style="margin-bottom:var(--space-4);display:flex;align-items:center;justify-content:space-between;gap:var(--space-6);">
				<div>
					<div style="display:flex;gap:var(--space-2);margin-bottom:var(--space-2);">
						<span class="pill">Sales</span>
						<span class="pill pill--teal">Full-Time</span>
					</div>
					<h4 style="margin-bottom:var(--space-1);">Medical Device Sales Representative</h4>
					<p style="font-size:var(--text-sm);color:var(--color-text-3);">Dallas, TX · Remote options available</p>
				</div>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--outline" style="flex-shrink:0;">Apply</a>
			</div>
			<div class="card reveal" data-glow style="margin-bottom:var(--space-4);display:flex;align-items:center;justify-content:space-between;gap:var(--space-6);">
				<div>
					<div style="display:flex;gap:var(--space-2);margin-bottom:var(--space-2);">
						<span class="pill">Operations</span>
						<span class="pill pill--teal">Full-Time</span>
					</div>
					<h4 style="margin-bottom:var(--space-1);">Supply Chain Coordinator</h4>
					<p style="font-size:var(--text-sm);color:var(--color-text-3);">Dallas, TX</p>
				</div>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--outline" style="flex-shrink:0;">Apply</a>
			</div>
			<div class="card reveal" data-glow style="margin-bottom:var(--space-4);display:flex;align-items:center;justify-content:space-between;gap:var(--space-6);">
				<div>
					<div style="display:flex;gap:var(--space-2);margin-bottom:var(--space-2);">
						<span class="pill">Partnerships</span>
						<span class="pill pill--teal">Full-Time</span>
					</div>
					<h4 style="margin-bottom:var(--space-1);">Business Development Manager</h4>
					<p style="font-size:var(--text-sm);color:var(--color-text-3);">Remote · National Territory</p>
				</div>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--outline" style="flex-shrink:0;">Apply</a>
			</div>
		</div>

		<div class="text-center reveal" style="margin-top:var(--space-10);">
			<p style="color:var(--color-text-2);margin-bottom:var(--space-4);">Don't see the perfect fit? Reach out anyway! We're always open to talking to exceptional people.</p>
			<a href="mailto:careers@leapdistributors.com" class="btn btn--primary">Send Us Your Resume</a>
		</div>
	</div>
</section>

<!-- ── Feature Band ───────────────────────────────────────── -->
<section class="feature-band reveal">
	<div class="container">
		<figure class="feature-band__media">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/built-for/careers-band.webp' ); ?>" alt="The Leap operations team at work in a modern medical-device distribution center" loading="lazy" decoding="async">
			<figcaption class="feature-band__caption">Every role here ends at the patient bedside. That's the work.</figcaption>
		</figure>
	</div>
</section>

<section class="cta-banner">
	<div class="cta-banner__bg-grid"></div>
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="container">
		<div class="cta-banner__inner">
			<h2 class="reveal">Let's Build Something Together</h2>
			<p class="reveal">If you're passionate about healthcare and want your work to matter, we'd love to hear from you.</p>
			<div class="cta-banner__actions reveal">
				<a href="mailto:careers@leapdistributors.com" class="btn btn--primary btn--lg">Email Our Team</a>
				<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="btn btn--hero-ghost">Learn About Leap</a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
