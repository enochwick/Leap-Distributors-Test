<?php get_header(); ?>

<!-- ── Hero ──────────────────────────────────────────────── -->
<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="container">
		<div class="page-hero__inner page-hero__inner--center">
			<span class="page-hero__eyebrow page-hero__eyebrow--no-line">For Hospitals</span>
			<h1 class="page-hero__title">One team. Every product line we cover.</h1>
			<p class="page-hero__lead">Leap operates as a single point of accountability across everything we carry, with the case data and billing speed your team deserves.</p>
			<div class="hero__actions" style="opacity:1;margin-top:var(--space-10);justify-content:center;">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--lg" style="opacity:1;">Let's talk <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</div>
</section>

<!-- ── Fewer Vendors ──────────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<div class="leap-intro reveal">
			<div>
				<h2 class="leap-intro__h">Fewer vendors. Cleaner records. One contact.</h2>
			</div>
			<div class="leap-intro__body">
				<p>Hospital supply chains don't need more vendors. They need fewer, running cleaner. Leap operates as one team across every product line we cover, with a single point of accountability behind it. When something needs to move, we move it. When something needs an answer, we have one.</p>
			</div>
		</div>
	</div>
</section>

<!-- ── Feature Band ───────────────────────────────────────── -->
<section class="feature-band reveal">
	<div class="container">
		<figure class="feature-band__media">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/built-for/hospitals-band.webp' ); ?>" alt="A clinician reviewing inventory in a hospital central supply area" loading="lazy" decoding="async">
			<figcaption class="feature-band__caption">One team across every product line — with live case data behind it.</figcaption>
		</figure>
	</div>
</section>

<!-- ── Why Hospitals Choose Leap ─────────────────────────── -->
<section class="why-leap">
	<div class="why-leap__bg">
		<div class="why-leap__bg-glow"></div>
	</div>
	<div class="container">
		<div class="why-leap__inner">
			<div class="why-leap__header" style="justify-content:center;text-align:center;">
								<h2 class="reveal">This is where surgeon preference<br>meets supply chain.</h2>
			</div>
			<p class="why-leap__lead reveal" style="max-width:680px;margin-inline:auto;text-align:center;">Leap doesn't make hospitals choose between surgeon preference and supply chain discipline. We make both work at once. Cases run well, service lines perform, and the revenue follows.</p>

			<div class="why-leap__grid" data-stagger>
				<div class="feature-card" data-stagger-child data-glow>
					<div class="feature-card__num">01</div>
					<div class="feature-card__title">Accountable, end to end.</div>
					<p class="feature-card__desc">Same standards, same platform, same team behind every product line we cover. Whatever Leap covers, Leap owns. When something needs to move, it moves. When something needs an answer, you get one.</p>
				</div>
				<div class="feature-card" data-stagger-child data-glow>
					<div class="feature-card__num">02</div>
					<div class="feature-card__title">Live case data. Faster billing.</div>
					<p class="feature-card__desc">Our reps log every product in the OR as it's used, on our own platform. Scrub sheets generate themselves and route by manufacturer. Billing on our side moves fast and lands clean, so your team stops chasing paperwork.</p>
				</div>
				<div class="feature-card" data-stagger-child data-glow>
					<div class="feature-card__num">03</div>
					<div class="feature-card__title">One Vendor. Surgeon preference protected.</div>
					<p class="feature-card__desc">Leap is one accountable partner across multiple product lines. Your finance team gets cleaner consolidation, your surgeons keep the products they trust, and both run through the same Leap relationship.</p>
				</div>
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
	</div>
</div>

<!-- ── CTA ────────────────────────────────────────────────── -->
<section class="cta-banner">
	<div class="cta-banner__bg-grid"></div>
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="container">
		<div class="cta-banner__inner">
			<span class="section-label section-label--white section-label--no-line reveal" style="justify-content:center;">See what the right partner can do.</span>
				<h2 class="reveal">Let's start a<br>conversation.</h2>
			<p class="reveal">Tell us what your team is dealing with. We'll find a time to talk.</p>
			<div class="cta-banner__actions reveal">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--lg">Let's talk <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
