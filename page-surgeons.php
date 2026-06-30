<?php get_header(); ?>

<!-- ── Hero ──────────────────────────────────────────────── -->
<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="container">
		<div class="page-hero__inner page-hero__inner--center">
			<span class="page-hero__eyebrow page-hero__eyebrow--no-line">For Surgeons</span>
			<h1 class="page-hero__title">The products you trust. The partner you deserve.</h1>
			<p class="page-hero__lead">Full choice, real support, no trade-off. We learn your practice, your preferences, and your standards. The OR is just where it starts.</p>
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
				<h2 class="leap-intro__h">We earn the room.<br>Then we earn everything after it.</h2>
			</div>
			<div class="leap-intro__body">
				<p>Most reps think the relationship stops at the case. Ours starts there. A Leap rep learns the practice like a partner does: patient mix, procedure mix, the way the clinic runs, the small things that turn your week from good to great. Flawless in the OR is the minimum. What we do around it is what sets us apart.</p>
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
				<h2 class="reveal">You have standards. So do we.</h2>
			</div>

			<div class="why-leap__grid" data-stagger>
				<div class="feature-card" data-stagger-child data-glow>
					<div class="feature-card__num">01</div>
					<div class="feature-card__title">Reps who think like partners.</div>
					<p class="feature-card__desc">Our reps walk in ready. For us, that means knowing the procedure, the room, your preferences, and your practice well enough to anticipate what's needed before it's asked for. The best service in any field starts with attention. Ours does too.</p>
				</div>
				<div class="feature-card" data-stagger-child data-glow>
					<div class="feature-card__num">02</div>
					<div class="feature-card__title">Patients before products.</div>
					<p class="feature-card__desc">We advocate for your choice every step of the way. And if something isn't right for the patient, we say so. If we wouldn't put it in our own family member, it doesn't make the cut. That's how we do business.</p>
				</div>
				<div class="feature-card" data-stagger-child data-glow>
					<div class="feature-card__num">03</div>
					<div class="feature-card__title">Trusted with the small things, too.</div>
					<p class="feature-card__desc">A 6 a.m. question gets a 6:02 a.m. reply. A last-minute schedule change gets handled before it becomes your problem. The logistics that aren't anyone's job, we make them ours. The small things break a good day. We take them seriously.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ── Trey video ─────────────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<div class="surgeon-trey surgeon-trey--solo reveal">
			<div class="surgeon-trey__video">
				<video class="surgeon-trey__player" controls playsinline preload="metadata"
					poster="<?php echo esc_url( get_template_directory_uri() . '/assets/videos/trey-owning-the-case-poster.webp' ); ?>">
					<source src="<?php echo esc_url( get_template_directory_uri() . '/assets/videos/trey-owning-the-case.mp4' ); ?>" type="video/mp4">
				</video>
			</div>
		</div>
	</div>
</section>

<!-- ── Specialties ────────────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<div class="leap-intro reveal">
			<div>
				<h2 class="leap-intro__h">Your Specialty? Covered.</h2>
			</div>
			<div class="leap-intro__body">
				<p>Spine, orthopedics, biologics, and soft tissue are just the start. Our reps and product lines reach across the surgical board. Don't see yours? Ask. The answer's usually yes.</p>
			</div>
		</div>

		<?php
		$leap_specialties = array(
			'Spine', 'Orthopedics', 'Biologics', 'Soft Tissue', 'Neurosurgery',
			'Plastics & Reconstructive', 'General Surgery', 'Colorectal', 'Gastroenterology',
			'Otolaryngology', 'Wound Care', 'Podiatry', 'Sports Medicine', 'Trauma',
			'Cardiothoracic', 'Vascular', 'Urology', 'Gynecology', 'Bariatric',
			'Ophthalmology', 'Pain Management', 'Hand & Upper Extremity', 'Foot & Ankle',
			'Maxillofacial', 'Plastics', 'Surgical Oncology',
		);
		?>
		<div class="specialty-marquee reveal" aria-label="Specialties we cover">
			<ul class="specialty-marquee__track">
				<?php for ( $copy = 0; $copy < 2; $copy++ ) : ?>
					<?php foreach ( $leap_specialties as $specialty ) : ?>
						<li<?php echo $copy === 1 ? ' aria-hidden="true"' : ''; ?>><?php echo esc_html( $specialty ); ?></li>
					<?php endforeach; ?>
				<?php endfor; ?>
			</ul>
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
	<div class="cta-banner__bg-grid"></div>
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="container">
		<div class="cta-banner__inner">
			<span class="section-label section-label--white section-label--no-line reveal" style="justify-content:center;">See what the right partner can do.</span>
			<h2 class="reveal">Let's start a<br>conversation.</h2>
			<p class="reveal">Tell us what you're working on. We'll find a time to talk.</p>
			<div class="cta-banner__actions reveal">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--lg">Let's talk <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
