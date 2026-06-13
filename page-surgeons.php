<?php get_header(); ?>

<!-- ── Hero ──────────────────────────────────────────────── -->
<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="container">
		<div class="page-hero__inner">
			<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> <span class="breadcrumb-sep">›</span> <span>Surgeons</span></nav>
			<span class="page-hero__eyebrow">For Surgeons</span>
			<h1 class="page-hero__title">Your call. Our coverage.</h1>
			<p class="page-hero__lead">A Leap rep doesn't just cover your cases. They learn your practice, your preferences, and your patients. The OR is just where it starts.</p>
			<div class="hero__actions" style="opacity:1;margin-top:var(--space-10);">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--lg">Let's talk <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</div>
</section>

<!-- ── We Earn the Room ───────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<div class="leap-intro reveal">
			<div>
				<h2 class="leap-intro__h">We earn the room. Then we earn everything after it.</h2>
			</div>
			<div class="leap-intro__body">
				<p>Most distributor relationships stop at the case. Ours start there. A Leap rep learns the practice the way a partner does: the patients, the procedures, the way the clinic runs, the small things that turn a good week into a great one. The work in the OR has to be flawless. The work around it is what makes the relationship last.</p>
			</div>
		</div>
	</div>
</section>

<!-- ── Three Things You Can Count On ─────────────────────── -->
<section class="why-leap">
	<div class="why-leap__bg">
		<div class="why-leap__bg-glow"></div>
	</div>
	<div class="container">
		<div class="why-leap__inner">
			<div class="why-leap__header">
				<span class="section-label section-label--white reveal">Why Surgeons Choose Leap</span>
				<h2 class="reveal">Three things you can count on.</h2>
			</div>

			<div class="why-leap__grid" data-stagger>
				<div class="feature-card" data-stagger-child data-glow>
					<div class="feature-card__num">01</div>
					<div class="feature-card__title">Reps who think like partners.</div>
					<p class="feature-card__desc">A Leap rep walks in ready. Ready means knowing the procedure, the room, the surgeon, and the practice well enough to anticipate what's needed before it's asked for. The best service in any field starts with attention. Ours does too.</p>
				</div>
				<div class="feature-card" data-stagger-child data-glow>
					<div class="feature-card__num">02</div>
					<div class="feature-card__title">Patients before products.</div>
					<p class="feature-card__desc">We advocate for your choice every step of the way. If a product isn't right for the patient, we say so. If a manufacturer pushes something we wouldn't put in our own family member, it doesn't move forward. That's how we do business.</p>
				</div>
				<div class="feature-card" data-stagger-child data-glow>
					<div class="feature-card__num">03</div>
					<div class="feature-card__title">Trusted with the small things, too.</div>
					<p class="feature-card__desc">A 6 a.m. question gets a 6:02 a.m. reply. A last-minute schedule change gets handled. The logistics that aren't anyone's job — we handle those too. We treat the small things as seriously as the cases.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ── Meet Trey ──────────────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<div class="leap-intro reveal">
			<div>
				<h2 class="leap-intro__h">This is what a Leap rep looks like.</h2>
			</div>
			<div class="leap-intro__body">
				<p>Trey is one of our reps. He'll tell you what working with Leap actually looks like.</p>
			</div>
		</div>

		<div class="surgeon-trey reveal">
			<div class="surgeon-trey__video">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/built-for/rep-or-wide.png' ); ?>" alt="A Leap rep in scrubs supporting a case in the operating room" loading="lazy">
				<div class="surgeon-trey__video-overlay">
					<div class="surgeon-trey__play" aria-label="Play video">
						<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="5 3 19 12 5 21 5 3"/></svg>
					</div>
				</div>
			</div>
			<blockquote class="surgeon-trey__quote">
				<p>"The OR is the easy part. What earns the trust is everything around it."</p>
				<cite>Trey, Leap rep</cite>
			</blockquote>
		</div>
	</div>
</section>

<!-- ── Specialties ────────────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<div class="leap-intro reveal">
			<div>
				<h2 class="leap-intro__h">Where we work.</h2>
			</div>
			<div class="leap-intro__body">
				<p>Our core coverage is spine, orthopedics, biologics, and soft tissue. Our reps and product lines extend well beyond that. Not sure if we cover what you do? Ask us. The answer's usually yes.</p>
			</div>
		</div>

		<div class="specialties reveal">
			<span class="specialty">Spine</span>
			<span class="specialty">Orthopedics</span>
			<span class="specialty">Biologics</span>
			<span class="specialty">Soft Tissue</span>
			<span class="specialty specialty--more">+ more</span>
		</div>
	</div>
</section>

<!-- ── Stats ──────────────────────────────────────────────── -->
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
	<div class="container">
		<div class="cta-banner__inner">
			<h2 class="reveal">Let's start a<br>conversation.</h2>
			<p class="reveal">Tell us what you're working on. We'll find a time to talk.</p>
			<div class="cta-banner__actions reveal">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--lg">Let's talk <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
