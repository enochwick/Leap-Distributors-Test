<?php get_header(); ?>

<?php
// ── Hero ACF fields with fallbacks ────────────────────────
$hero_eyebrow  = get_field( 'hero_eyebrow' )  ?: 'Medical Device Distribution';
$hero_subtext  = get_field( 'hero_subtext' )  ?: 'Sharper reps. Smarter platform. Cleaner data on every side of the OR door. One distribution partner across surgeons, hospitals, and manufacturers, all running in Stride.';
$hero_cta1_text = get_field( 'hero_cta1_text' ) ?: 'Talk to our team';
$hero_cta1_url  = get_field( 'hero_cta1_url' )  ?: home_url( '/contact/' );
$hero_cta2_text = get_field( 'hero_cta2_text' ) ?: 'Explore the platform';
$hero_cta2_url  = get_field( 'hero_cta2_url' )  ?: home_url( '/platform/' );

// Headline: one line per row, last line gets accent color
$hero_headline_raw = get_field( 'hero_headline' );
if ( $hero_headline_raw ) {
	$lines = array_filter( array_map( 'trim', explode( "\n", $hero_headline_raw ) ) );
	$lines = array_values( $lines );
	$last  = count( $lines ) - 1;
	$headline_html = '';
	foreach ( $lines as $i => $line ) {
		if ( $i === $last ) {
			$headline_html .= '<em>' . esc_html( $line ) . '</em>';
		} else {
			$headline_html .= esc_html( $line ) . '<br>';
		}
	}
} else {
	$headline_html = 'The New Standard<br>in Medical Device<br><em>Distribution.</em>';
}
?>

<!-- ── Hero (scroll-driven video) ────────────────────────── -->
<div class="hero-scroll-container">
<section class="hero hero--video">
	<canvas id="hero-canvas" aria-hidden="true"></canvas>
	<div class="hero__video-overlay"></div>

	<div class="hero__body">
		<div class="hero__badge hero__eyebrow">
			<span><?php echo esc_html( $hero_eyebrow ); ?></span>
			<span aria-hidden="true">→</span>
		</div>

		<h1 class="hero__headline">
			<?php echo $headline_html; ?>
		</h1>

		<div class="hero__foot">
			<p class="hero__subtext">
				<?php echo esc_html( $hero_subtext ); ?>
			</p>
			<div class="hero__actions">
				<a href="<?php echo esc_url( $hero_cta1_url ); ?>" class="btn btn--hero-primary"><?php echo esc_html( $hero_cta1_text ); ?> <span aria-hidden="true">→</span></a>
				<?php if ( $hero_cta2_text ) : ?>
				<a href="<?php echo esc_url( $hero_cta2_url ); ?>" class="btn btn--hero-ghost"><?php echo esc_html( $hero_cta2_text ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="hero__scroll" aria-hidden="true">
		<span>Scroll</span>
		<div class="hero__scroll-line"></div>
	</div>
</section>
</div>

<!-- ── Proof Bar ──────────────────────────────────────────── -->
<div class="stats-strip">
	<div class="stats-strip__inner">
		<div class="stats-strip__item">
			<span class="stats-strip__val" data-count="10000" data-suffix="+">10,000+</span>
			<span class="stats-strip__label">Surgeries Annually</span>
		</div>
		<span class="stats-strip__divider" aria-hidden="true">—</span>
		<div class="stats-strip__item">
			<span class="stats-strip__val" data-count="750" data-suffix="+">750+</span>
			<span class="stats-strip__label">Surgeons</span>
		</div>
		<span class="stats-strip__divider" aria-hidden="true">—</span>
		<div class="stats-strip__item">
			<span class="stats-strip__val" data-count="350" data-suffix="+">350+</span>
			<span class="stats-strip__label">Facilities, GPOs &amp; IDNs</span>
		</div>
	</div>
</div>

<!-- ── What Leap Is ───────────────────────────────────────── -->
<section class="content-section bg-radial-glow bg-radial-glow--cyan">
	<div class="container">
		<div class="leap-intro reveal">
			<div>
				<h2 class="leap-intro__h">We earn the room one case at a time. Every case after, we earn it again.</h2>
			</div>
			<div class="leap-intro__body">
				<p>Surgeons choose Leap because we're partners, not pushers. Hospitals stay because we operate as one team across every product line we cover. We run Leap on Stride, our custom tech platform that logs every case in the OR as it happens. The paperwork generates itself. The billing moves faster. The data sharpens with every case.</p>
				<a href="<?php echo esc_url( home_url( '/platform/' ) ); ?>" class="arrow-link" style="margin-top: var(--space-6); display:inline-flex;">Explore the platform <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</div>
</section>

<!-- ── Three Capabilities ─────────────────────────────────── -->
<section class="capabilities-section">
	<div class="container">
		<div class="section-header reveal">
			<span class="section-label section-label--blue">Three Capabilities. One Platform.</span>
		</div>

		<div class="capability-grid">

			<div class="capability-card reveal" data-glow>
				<div data-glow-inner></div>
				<span class="capability-card__num">01</span>
				<div class="capability-card__content">
					<h3>Built to move.</h3>
					<p>Every product logged the moment it's used. Pricing structures pre-loaded. Scrub sheets generate themselves and route by manufacturer. Cleaner records, faster billing, no paper chase.</p>
				</div>
			</div>

			<div class="capability-card reveal" data-glow>
				<div data-glow-inner></div>
				<span class="capability-card__num">02</span>
				<div class="capability-card__content">
					<h3>Patients before products.</h3>
					<p>We think holistically about the surgeon's practice, the patient on the table, and the case in front of us. Our reps are the sharpest in the room, and they advocate for surgeon choice every step of the way. Patient care comes first. Not with our team.</p>
				</div>
			</div>

			<div class="capability-card reveal" data-glow>
				<div data-glow-inner></div>
				<span class="capability-card__num">03</span>
				<div class="capability-card__content">
					<h3>Engineered for intelligence.</h3>
					<p>Every case generates data, and we put it to work. Real-time visibility into product trends, case volume, surgeon trends, and rep performance. Today, it powers our work and informs our partners.</p>
				</div>
			</div>

		</div>
	</div>
</section>

<!-- ── Who Leap Is For — Vertical Tabs ─────────────────────── -->
<section class="vt-section">
	<div class="container">
		<div class="vt-grid">

			<!-- Left: header + vertical tabs -->
			<div class="vt-left">
				<div class="vt-header">
					<span class="section-label section-label--blue">Built For</span>
					<h2>Built for the people on both sides of the OR door.</h2>
				</div>

				<div class="vt-tabs" id="vt-tabs">

					<button class="vt-tab is-active" data-vt-index="0">
						<div class="vt-tab__track"><div class="vt-tab__progress"></div></div>
						<span class="vt-tab__num">/01</span>
						<div class="vt-tab__body">
							<span class="vt-tab__title">Surgeons</span>
							<div class="vt-tab__desc-wrap">
								<p class="vt-tab__desc">Reps who know your preferences, your procedures, and your room before they walk in. Broader product access without losing the people you trust.</p>
								<a href="<?php echo esc_url( home_url( '/surgeons/' ) ); ?>" class="arrow-link" style="margin-top:var(--space-4);display:inline-flex;">Learn more <span aria-hidden="true">→</span></a>
							</div>
						</div>
					</button>

					<button class="vt-tab" data-vt-index="1">
						<div class="vt-tab__track"><div class="vt-tab__progress"></div></div>
						<span class="vt-tab__num">/02</span>
						<div class="vt-tab__body">
							<span class="vt-tab__title">Hospitals</span>
							<div class="vt-tab__desc-wrap">
								<p class="vt-tab__desc">Stop chasing fifteen reps across fifteen manufacturers. One team, every product line, with live case data and billing speed your supply chain needs.</p>
								<a href="<?php echo esc_url( home_url( '/partnerships/hospitals/' ) ); ?>" class="arrow-link" style="margin-top:var(--space-4);display:inline-flex;">Learn more <span aria-hidden="true">→</span></a>
							</div>
						</div>
					</button>

					<button class="vt-tab" data-vt-index="2">
						<div class="vt-tab__track"><div class="vt-tab__progress"></div></div>
						<span class="vt-tab__num">/03</span>
						<div class="vt-tab__body">
							<span class="vt-tab__title">Manufacturers</span>
							<div class="vt-tab__desc-wrap">
								<p class="vt-tab__desc">Direct rep coverage in our south central hub, a national distributor partner reach, and field data that tells you exactly how your product is moving.</p>
								<a href="<?php echo esc_url( home_url( '/partnerships/manufacturers/' ) ); ?>" class="arrow-link" style="margin-top:var(--space-4);display:inline-flex;">Learn more <span aria-hidden="true">→</span></a>
							</div>
						</div>
					</button>

				</div>
			</div>

			<!-- Right: animated image gallery -->
			<div class="vt-right">
				<div class="vt-gallery" id="vt-gallery">
					<div class="vt-slide is-active" data-vt-slide="0">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/built-for/surgeons.png' ); ?>" alt="A surgical team at work in a modern operating room" loading="lazy">
					</div>
					<div class="vt-slide" data-vt-slide="1">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/built-for/hospitals.png' ); ?>" alt="A clinician in a modern hospital corridor" loading="lazy">
					</div>
					<div class="vt-slide" data-vt-slide="2">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/built-for/manufacturers.png' ); ?>" alt="Precision-machined surgical implants and instruments" loading="lazy">
					</div>

					<!-- Prev / Next -->
					<div class="vt-nav">
						<button class="vt-nav__btn" id="vt-prev" aria-label="Previous">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
						</button>
						<button class="vt-nav__btn" id="vt-next" aria-label="Next">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
						</button>
					</div>
				</div>
			</div>

		</div>
	</div>
</section>

<!-- ── News ───────────────────────────────────────────────── -->
<section class="content-section content-section--alt">
	<div class="container">
		<div class="section-header reveal" style="display:flex;align-items:flex-end;justify-content:space-between;max-width:100%;flex-wrap:wrap;gap:var(--space-4);">
			<h2 style="margin:0;">What we're up to.</h2>
			<a href="<?php echo esc_url( home_url( '/news/' ) ); ?>" class="arrow-link reveal">More from Leap <span aria-hidden="true">→</span></a>
		</div>
		<div class="news-grid" data-stagger>

			<a class="news-card" href="https://www.orthospinenews.com/" target="_blank" rel="noopener" data-stagger-child data-glow>
				<div data-glow-inner></div>
				<div class="news-card__image" style="background:var(--color-teal-dark);display:flex;align-items:center;justify-content:center;">
					<span style="font-size:var(--text-xs);font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:rgba(255,255,255,0.5);">OrthoSpineNews</span>
				</div>
				<div class="news-card__body">
					<div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-3);">
						<span class="news-card__cat">Press</span>
						<span style="font-size:var(--text-xs);color:var(--color-text-4);">April 2025</span>
					</div>
					<h3 class="news-card__title">Medtech Incubator Partners with Leap Distributors</h3>
					<div style="display:flex;align-items:center;justify-content:space-between;margin-top:var(--space-5);">
						<span style="font-size:var(--text-xs);color:var(--color-text-3);">OrthoSpineNews</span>
						<span style="font-size:var(--text-lg);color:var(--color-text-3);">↗</span>
					</div>
				</div>
			</a>

			<a class="news-card" href="https://dallasinnovates.com/" target="_blank" rel="noopener" data-stagger-child data-glow>
				<div data-glow-inner></div>
				<div class="news-card__image" style="background:var(--color-blue);display:flex;align-items:center;justify-content:center;">
					<span style="font-size:var(--text-xs);font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:rgba(255,255,255,0.7);">Dallas Innovates</span>
				</div>
				<div class="news-card__body">
					<div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-3);">
						<span class="news-card__cat">Press</span>
						<span style="font-size:var(--text-xs);color:var(--color-text-4);">March 2025</span>
					</div>
					<h3 class="news-card__title">Dallas' Leap Distributors Acquires Leap Surgical and DUB Enterprises</h3>
					<div style="display:flex;align-items:center;justify-content:space-between;margin-top:var(--space-5);">
						<span style="font-size:var(--text-xs);color:var(--color-text-3);">Dallas Innovates</span>
						<span style="font-size:var(--text-lg);color:var(--color-text-3);">↗</span>
					</div>
				</div>
			</a>

			<a class="news-card" href="<?php echo esc_url( home_url( '/news/' ) ); ?>" data-stagger-child data-glow>
				<div data-glow-inner></div>
				<div class="news-card__image" style="background:var(--color-orange);display:flex;align-items:center;justify-content:center;">
					<span style="font-size:var(--text-xs);font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:rgba(255,255,255,0.8);">Leap Insights</span>
				</div>
				<div class="news-card__body">
					<div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-3);">
						<span class="news-card__cat" style="color:var(--color-orange);background:rgba(230,83,0,0.08);">Insight</span>
						<span style="font-size:var(--text-xs);color:var(--color-text-4);">February 2025</span>
					</div>
					<h3 class="news-card__title">Why Product-Agnostic Distribution Wins for Surgeons and Hospitals</h3>
					<div style="display:flex;align-items:center;justify-content:space-between;margin-top:var(--space-5);">
						<span style="font-size:var(--text-xs);color:var(--color-text-3);">Leap Distributors</span>
						<span style="font-size:var(--text-lg);color:var(--color-text-3);">→</span>
					</div>
				</div>
			</a>

		</div>
	</div>
</section>

<!-- ── CTA Banner ─────────────────────────────────────────── -->
<section class="cta-banner">
	<div class="container">
		<div class="cta-banner__inner">
			<span class="section-label section-label--white reveal" style="justify-content:center;">Keep up with Leap</span>
			<h2 class="reveal">Better.<br>Together.</h2>
			<p class="reveal">Monthly. The good stuff. No noise.</p>
			<?php if ( isset( $_GET['newsletter'] ) && $_GET['newsletter'] === 'success' ) : ?>
				<div class="form-feedback form-feedback--success reveal" style="margin-bottom:var(--space-6);">
					<strong>You're in.</strong> We'll be in touch with the good stuff.
				</div>
			<?php endif; ?>

			<form class="newsletter-form reveal" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="leap_newsletter_form">
				<?php wp_nonce_field( 'leap_newsletter_form', 'leap_newsletter_nonce' ); ?>
				<div class="newsletter-form__row">
					<input type="email" name="email" placeholder="Email address" required aria-label="Email address">
					<select name="audience" aria-label="I'm a">
						<option value="" disabled selected>I'm a…</option>
						<option value="surgeon">Surgeon</option>
						<option value="hospital">Hospital or health system</option>
						<option value="manufacturer">Manufacturer</option>
						<option value="distributor">Independent rep or distributor</option>
					</select>
				</div>
				<button type="submit" class="btn btn--primary btn--lg">Subscribe <span aria-hidden="true">→</span></button>
			</form>
		</div>
	</div>
</section>

<?php get_footer(); ?>
