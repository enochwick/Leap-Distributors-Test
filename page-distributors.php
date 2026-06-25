<?php get_header(); ?>

<!-- ── Hero ──────────────────────────────────────────────── -->
<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="container">
		<div class="page-hero__inner page-hero__inner--center">
			<span class="page-hero__eyebrow">For Distributors &amp; Independent Reps</span>
			<h1 class="page-hero__title">You know what good distribution looks like.<br>So do we.</h1>
			<p class="page-hero__lead">Leap is built for reps who want better infrastructure, real back-office support, and a brand that puts more weight behind them. If that's the kind of move you're considering, let's talk.</p>
			<div class="hero__actions" style="opacity:1;margin-top:var(--space-10);justify-content:center;">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--lg">Let's talk <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</div>
</section>

<!-- ── Four Reasons ───────────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<div class="leap-intro reveal">
			<div>
				<h2 class="leap-intro__h">Four reasons reps make the move.</h2>
			</div>
		</div>

		<div class="reason-grid" data-stagger>
			<article class="reason-card" data-stagger-child data-glow>
				<span class="reason-card__num">01</span>
				<h3 class="reason-card__h">Tech that makes you look good.</h3>
				<p>Most distributors are still running on paper, email, and Excel. Leap built Stride, our own platform that logs every case in the OR as it happens. Auto-generated scrub sheets. Live performance dashboards. Real-time commission visibility. You walk into a case with the kind of tools your business deserves.</p>
			</article>
			<article class="reason-card" data-stagger-child data-glow>
				<span class="reason-card__num">02</span>
				<h3 class="reason-card__h">A real back office behind you.</h3>
				<p>Contracting, finance, ops, credentialing. All run by people whose job is making you faster, not slower. You spend more time in front of surgeons. We handle what's behind you.</p>
			</article>
			<article class="reason-card" data-stagger-child data-glow>
				<span class="reason-card__num">03</span>
				<h3 class="reason-card__h">A brand that walks in with you.</h3>
				<p>Leap is a serious operator with serious relationships. When you walk into a case under our name, the surgeon, the hospital, and the manufacturer already know what to expect. That's leverage you can't build on your own.</p>
			</article>
			<article class="reason-card" data-stagger-child data-glow>
				<span class="reason-card__num">04</span>
				<h3 class="reason-card__h">Real growth, not just a logo swap.</h3>
				<p>Manufacturer relationships your book might not have access to. Internal sales teams you can plug into. 200+ distributor partners already in the network. The point isn't to flip your existing business. The point is to grow it.</p>
			</article>
		</div>
	</div>
</section>

<!-- ── Feature Band ───────────────────────────────────────── -->
<section class="feature-band reveal">
	<div class="container">
		<figure class="feature-band__media">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/built-for/distributors-band.webp' ); ?>" alt="Two medical device representatives meeting in a hospital lobby" loading="lazy" decoding="async">
			<figcaption class="feature-band__caption">A brand that walks in with you — and a back office behind you.</figcaption>
		</figure>
	</div>
</section>

<!-- ── Better. Together. ──────────────────────────────────── -->
<section class="better-together-section">
	<div class="container">
		<div class="better-together-section__inner reveal">
			<div class="better-together-section__words">
				<span>Better.</span>
				<span>Together.</span>
			</div>
			<div class="better-together-section__body">
				<p>Distribution is getting harder for independents. Hospitals are consolidating into GPOs and IDNs. Vendors are being cut. The reps and small distributors doing this work right are getting squeezed by forces they can't navigate alone.</p>
				<p>We're building the answer together. Independent operators under one platform, one brand, one set of standards. Better for the surgeons, who get to keep the reps and products they trust. Stronger for the hospitals, who get a real partner instead of vendor sprawl. More leverage for the manufacturers, who get real reach without building it from scratch.</p>
				<p><strong>Better, together. That's not a tagline. That's the strategy.</strong></p>
			</div>
		</div>
	</div>
</section>

<!-- ── Numbers ────────────────────────────────────────────── -->
<div class="stats-strip">
	<div class="stats-strip__inner">
		<div class="stats-strip__item">
			<span class="stats-strip__val" data-count="10000" data-suffix="+">10,000+</span>
			<span class="stats-strip__label">Surgeries Annually</span>
		</div>
		<span class="stats-strip__divider" aria-hidden="true">—</span>
		<div class="stats-strip__item">
			<span class="stats-strip__val" data-count="750" data-suffix="+">750+</span>
			<span class="stats-strip__label">Surgeons Supported</span>
		</div>
		<span class="stats-strip__divider" aria-hidden="true">—</span>
		<div class="stats-strip__item">
			<span class="stats-strip__val" data-count="350" data-suffix="+">350+</span>
			<span class="stats-strip__label">Facilities, GPOs &amp; IDNs</span>
		</div>
		<span class="stats-strip__divider" aria-hidden="true">—</span>
		<div class="stats-strip__item">
			<span class="stats-strip__val" data-count="200" data-suffix="+">200+</span>
			<span class="stats-strip__label">Distributor Partners</span>
		</div>
	</div>
</div>

<!-- ── CTA ────────────────────────────────────────────────── -->
<section class="cta-banner">
	<div class="cta-banner__bg-grid"></div>
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="container">
		<div class="cta-banner__inner">
			<h2 class="reveal">Let's see if<br>there's a fit.</h2>
			<p class="reveal">No pitch deck, no recruiting flow. Just a conversation about your book, your goals, and whether Leap is the right next move.</p>
			<div class="cta-banner__actions reveal">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--lg">Let's talk <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
