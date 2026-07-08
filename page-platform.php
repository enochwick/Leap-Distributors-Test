<?php get_header(); ?>

<!-- ── Hero — Animated Gallery ────────────────────────────── -->
<section class="phg-section">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>

	<!-- Header text -->
	<div class="phg-header">
		<div class="phg-header__inner">
			<span class="section-label section-label--blue section-label--no-line phg-fade">Stride — The Leap Platform</span>
			<h1 class="phg-title phg-fade">The custom tech<br>that runs every case.</h1>
			<p class="phg-lead phg-fade">Stride is Leap's own platform. Reps log every case in the OR as it happens, the paperwork generates itself, and every side of the relationship gets sharper data with every case.</p>
			<div class="phg-fade" style="margin-top:var(--space-8);">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--lg">Request a walkthrough <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</div>

	<!-- 3D Scroll Image -->
	<div class="phg-scroll" id="phg-scroll">
		<div class="phg-sticky" id="phg-sticky">
			<div class="phg-single" id="phg-gallery">
				<div class="laptop-mockup">
					<div class="laptop-mockup__screen">
						<span class="laptop-mockup__camera" aria-hidden="true"></span>
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/platform screenshots/crm_cases_grid_fictional_anonymized.png' ); ?>" alt="Stride dashboard" loading="eager">
					</div>
					<div class="laptop-mockup__base"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ── What Stride Is ─────────────────────────────────────────── -->
<section class="content-section" style="padding-top:var(--space-12)">
	<div class="container">
		<div class="leap-intro reveal">
			<div>
				<h2 class="leap-intro__h">Built by us.<br>Used in every case.</h2>
			</div>
			<div class="leap-intro__body">
				<p>The tool we needed didn't exist, so we built it. Stride is custom-made for how independent distribution actually works. Reps use it in the OR. Our team bills faster and cleaner. The data sharpens with every case.</p>
			</div>
		</div>
	</div>
</section>

<!-- ── Three Capabilities ────────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<div class="platform-stack" id="platform-stack">
		<!-- Capability 1 -->
		<div class="platform-cap platform-stack__card" data-stack-index="0">
			<div class="platform-cap__copy">
				<span class="platform-cap__num">01</span>
				<h3 class="platform-cap__h">Built to move.</h3>
				<p>Reps log products in the OR as they're used. Pricing pulls from the appropriate contract. Scrub sheets generate themselves and route by manufacturer. Restock requests fire automatically. The case closes cleaner than it started.</p>
			</div>
			<div class="platform-cap__visual platform-cap__visual--shot">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/platform screenshots/Unknon-7.png' ); ?>" alt="Stride case log dashboard" loading="lazy" decoding="async">
				</div>
			</div>

			<!-- Capability 2 -->
		<div class="platform-cap platform-cap--reverse platform-stack__card" data-stack-index="1">
			<div class="platform-cap__copy">
				<span class="platform-cap__num">02</span>
				<h3 class="platform-cap__h">Patients before products.</h3>
				<p>The platform doesn't replace the rep. It frees them to do the real work — knowing the procedure, the surgeon, and the patient on the table. Stride handles the paperwork so our reps don't have to look down. They stay in the case, advocating for surgeon choice and the right call for the patient.</p>
			</div>
			<div class="platform-cap__visual platform-cap__visual--device">
				<div class="tablet-mockup">
					<span class="tablet-mockup__cam" aria-hidden="true"></span>
					<img class="tablet-mockup__screen" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/platform screenshots/stride-phone.webp' ); ?>" alt="Stride running on a tablet" loading="lazy" decoding="async">
				</div>
			</div>
		</div>

		<!-- Capability 3 -->
		<div class="platform-cap platform-stack__card" data-stack-index="2">
			<div class="platform-cap__copy">
				<span class="platform-cap__num">03</span>
				<h3 class="platform-cap__h">Engineered for intelligence.</h3>
				<p>Every case generates data, and we put it to work. Today, that means real-time visibility into product trends, case volume, surgeon trends, and rep performance, powering our work and informing our partners. Productized partner dashboards and AI-assisted billing checks are in active development.</p>
			</div>
			<div class="platform-cap__visual platform-cap__visual--shot">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/platform screenshots/Unknown-5.png' ); ?>" alt="Stride insights dashboard" loading="lazy" decoding="async">
				</div>
			</div>
		</div><!-- /.platform-stack -->
	</div>
</section>

<!-- ── Rep Dashboard ──────────────────────────────────────────── -->
<section class="content-section repdash-section">
	<div class="container repdash-wrap">
		<img class="repdash-bg" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/platform screenshots/Unknown78.png' ); ?>" alt="" aria-hidden="true" loading="lazy" decoding="async">
		<div class="leap-intro reveal">
			<div>
				<h2 class="leap-intro__h">A platform reps actually want to use.</h2>
			</div>
			<div class="leap-intro__body">
				<p>Live performance dashboards. Real-time commission projections. Quota visibility by the day. Stride is built for the field, not just the back office. It's one of the reasons our reps stay and our partners join.</p>
			</div>
		</div>
	</div>
</section>


<!-- ── What's Next ────────────────────────────────────────────── -->
<section class="content-section roadmap-section">
	<div class="container">
		<div class="section-header section-header--center reveal">
			<h2>Stride keeps moving forward.</h2>
			<p style="margin-top:var(--space-4);color:var(--color-text-2);font-weight:300;">Three things shipping over the next twelve months:</p>
		</div>

		<div class="mtl" id="roadmap-timeline">
			<span class="mtl__rail" aria-hidden="true"></span>
			<span class="mtl__rail-fill" id="roadmap-progress" aria-hidden="true"></span>

			<div class="mtl__item mtl__item--current">
				<div class="mtl__marker" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
				</div>
				<div class="mtl__card" data-glow>
					<div data-glow-inner></div>
					<div class="mtl__card-top">
						<div class="mtl__head">
							<h3 class="mtl__title">Partner dashboards</h3>
						</div>
						<span class="mtl__badge mtl__badge--current">In progress</span>
					</div>
					<p class="mtl__desc">Partner-facing dashboards for hospitals and manufacturers.</p>
				</div>
			</div>

			<div class="mtl__item mtl__item--current">
				<div class="mtl__marker" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
				</div>
				<div class="mtl__card" data-glow>
					<div data-glow-inner></div>
					<div class="mtl__card-top">
						<div class="mtl__head">
							<h3 class="mtl__title">AI cross-checks</h3>
						</div>
						<span class="mtl__badge mtl__badge--current">In progress</span>
					</div>
					<p class="mtl__desc">AI-assisted cross-checks for scrub sheets and billing.</p>
				</div>
			</div>

			<div class="mtl__item mtl__item--current">
				<div class="mtl__marker" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
				</div>
				<div class="mtl__card" data-glow>
					<div data-glow-inner></div>
					<div class="mtl__card-top">
						<div class="mtl__head">
							<h3 class="mtl__title">Distributor access</h3>
						</div>
						<span class="mtl__badge mtl__badge--current">In progress</span>
					</div>
					<p class="mtl__desc">Full Stride access for our distributor partner network.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ── Closing CTA ────────────────────────────────────────────── -->
<section class="cta-banner">
	<div class="cta-banner__bg-grid"></div>
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="container">
		<div class="cta-banner__inner">
			<h2 class="reveal">See Us Stride.</h2>
			<p class="reveal cta-oneline">The fastest way to understand what Leap does differently is to see the platform behind it.</p>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--lg reveal">Request a walkthrough <span aria-hidden="true">→</span></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
