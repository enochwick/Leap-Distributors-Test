<?php get_header(); ?>

<!-- ── Hero ──────────────────────────────────────────────── -->
<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="container">
		<div class="page-hero__inner page-hero__inner--center">
			<span class="page-hero__eyebrow page-hero__eyebrow--no-line">For Manufacturers</span>
			<h1 class="page-hero__title">Direct coverage with national reach.</h1>
			<p class="page-hero__lead">A distribution partner that offers direct coverage, national reach, and the field visibility you've been asking distributors for.</p>
			<div class="hero__actions" style="opacity:1;margin-top:var(--space-10);">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--lg">Let's talk <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</div>
</section>

<!-- ── Plug In ────────────────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<div class="leap-intro reveal">
			<div>
				<h2 class="leap-intro__h">Plug into a distribution channel that already works.</h2>
			</div>
			<div class="leap-intro__body">
				<p>Standing up a sales force can take years. Leap already has one. A great one. Direct rep teams in the south central US, 200+ distributor partners extending nationally, and a platform that tells you exactly how your product is moving in the OR.</p>
			</div>
		</div>
	</div>
</section>

<!-- ── Feature Band ───────────────────────────────────────── -->
<section class="feature-band reveal">
	<div class="container">
		<figure class="feature-band__media">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/built-for/manufacturers-band.png' ); ?>" alt="Robotic precision machining of titanium orthopedic implants in a medical device facility" loading="lazy">
			<figcaption class="feature-band__caption">We sell your line like it's ours — with field data that tells the truth.</figcaption>
		</figure>
	</div>
</section>

<!-- ── Why Manufacturers Choose Leap ─────────────────────── -->
<section class="why-leap">
	<div class="why-leap__bg">
		<div class="why-leap__bg-glow"></div>
	</div>
	<div class="container">
		<div class="why-leap__inner">
			<div class="why-leap__header" style="justify-content:center;text-align:center;">
								<h2 class="reveal">Reach, visibility, and reps<br>who can carry your line.</h2>
			</div>
			<p class="why-leap__lead reveal" style="max-width:680px;margin-inline:auto;text-align:center;">A distribution partner isn't a logo on a slide. It's the team you want in the room, a contracting function that doesn't drop the ball, a finance team that pays on time, and a platform that tells you the truth about how your product is moving.</p>

			<div class="why-leap__grid" data-stagger>
				<div class="feature-card" data-stagger-child data-glow>
					<div class="feature-card__num">01</div>
					<div class="feature-card__title">Reach without the wait.</div>
					<p class="feature-card__desc">Building direct coverage costs years and millions. Leap is the shortcut. Internal rep teams already covering the south central US, with 200+ distributor partners across the country. Your product launches into a distribution network that's already running.</p>
				</div>
				<div class="feature-card" data-stagger-child data-glow>
					<div class="feature-card__num">02</div>
					<div class="feature-card__title">Field visibility, not field guesses.</div>
					<p class="feature-card__desc">Every case our reps run is logged in Stride, our custom tech platform. That means you see exactly what's moving, where, and by whom. No more waiting on quarterly reports that arrive late and arrive thin.</p>
				</div>
				<div class="feature-card" data-stagger-child data-glow>
					<div class="feature-card__num">03</div>
					<div class="feature-card__title">Reps who carry weight.</div>
					<p class="feature-card__desc">A distributorship is only as good as the reps behind it. Ours are the sharpest in the room, on the case before scrub-in, and treated like the practice partners they are. When your rep has the room's trust, the product moves.</p>
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
			<h2 class="reveal">Let's start a<br>conversation.</h2>
			<p class="reveal">Tell us about your product and where you want it. We'll find a time to talk.</p>
			<div class="cta-banner__actions reveal">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--lg">Let's talk <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
