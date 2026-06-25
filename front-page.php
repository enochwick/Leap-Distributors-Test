<?php get_header(); ?>

<?php
// ── Hero ACF fields with fallbacks ────────────────────────
$hero_subtext  = get_field( 'hero_subtext' )  ?: 'Sharper reps. Smarter platform. Cleaner data on every side of the OR door.';
$hero_tagline  = get_field( 'hero_tagline' )  ?: 'One distribution partner across surgeons, hospitals, and manufacturers, all running in Stride.';
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
			<?php if ( $hero_tagline ) : ?>
			<p class="hero__tagline">
				<?php echo esc_html( $hero_tagline ); ?>
			</p>
			<?php endif; ?>
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
				<h2 class="leap-intro__h">We earn the room one case at a time.<br>Every case after, we earn it again.</h2>
			</div>
			<div class="leap-intro__body">
				<p>Surgeons choose Leap for the freedom to choose, from one patient-first partner. Hospitals stay because we operate as a single team across every product line we offer. Everything runs in Stride, our custom platform that logs every case as it happens. The paperwork generates itself. The billing moves faster. The data sharpens with every case.</p>
				<a href="<?php echo esc_url( home_url( '/platform/' ) ); ?>" class="arrow-link" style="margin-top: var(--space-6); display:inline-flex;">Explore the platform <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</div>
</section>

<!-- ── Three Capabilities ─────────────────────────────────── -->
<section class="capabilities-section">
	<div class="container">
		<div class="section-header reveal">
			<h2>Three Capabilities. One Platform.</h2>
		</div>

		<div class="capability-grid">

			<div class="capability-card reveal" data-glow>
				<div data-glow-inner></div>
				<span class="capability-card__num">01</span>
				<div class="capability-card__content">
					<h3>Live case logging.</h3>
					<p>Every product logged in the OR as it's used. Pricing pre-loaded. Scrub sheets that write themselves and route by manufacturer. The case closes cleaner than it started.</p>
				</div>
			</div>

			<div class="capability-card reveal" data-glow>
				<div data-glow-inner></div>
				<span class="capability-card__num">02</span>
				<div class="capability-card__content">
					<h3>Clinical-first coverage.</h3>
					<p>Reps who know the procedure, the surgeon, and the patient on the table. They advocate for surgeon choice and the right call for the patient, every case.</p>
				</div>
			</div>

			<div class="capability-card reveal" data-glow>
				<div data-glow-inner></div>
				<span class="capability-card__num">03</span>
				<div class="capability-card__content">
					<h3>Real-time visibility.</h3>
					<p>Live insights into product trends, case volume, and rep performance. Today, the data powers our work and informs our partners. Partner dashboards and AI-assisted billing checks are in development.</p>
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
					<h2>Built for the people on both sides of the OR&nbsp;door.</h2>
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
		<div class="section-header reveal">
			<h2 style="margin:0;">What we're up to.</h2>
		</div>
		<div class="news-grid" data-stagger>

			<a class="news-card" href="https://www.orthospinenews.com/" target="_blank" rel="noopener" data-stagger-child data-glow>
				<div data-glow-inner></div>
				<div class="news-card__image" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/blog-news/LEAP-X-MEDTECH-07.png' ); ?>');background-size:cover;background-position:center;"></div>
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
				<div class="news-card__image" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/blog-news/pexels-karolina-grabowska-7875996-1-scaled.jpg' ); ?>');background-size:cover;background-position:center;"></div>
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
				<div class="news-card__image" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/blog-news/LeapDistributors3-e1769101290191.jpeg' ); ?>');background-size:cover;background-position:center;"></div>
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
		<div class="reveal" style="margin-top:var(--space-8);">
			<a href="<?php echo esc_url( home_url( '/news/' ) ); ?>" class="arrow-link">More from Leap <span aria-hidden="true">→</span></a>
		</div>
	</div>
</section>

<!-- ── CTA Banner ─────────────────────────────────────────── -->
<section class="cta-banner">
	<div class="container">
		<div class="cta-banner__inner">
			<span class="section-label section-label--white section-label--no-line reveal" style="justify-content:center;">Keep up with Leap</span>
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
						<option value="other">Other</option>
					</select>
				</div>
				<button type="submit" class="btn btn--primary btn--lg">Subscribe <span aria-hidden="true">→</span></button>
			</form>
		</div>
	</div>
</section>

<?php get_footer(); ?>
