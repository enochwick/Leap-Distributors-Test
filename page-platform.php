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
			<div class="platform-cap__visual platform-cap__visual--photo">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/built-for/rep-or.webp' ); ?>" alt="A Leap rep passing an instrument during OR coverage" loading="lazy" decoding="async">
				<div class="platform-cap__photo-overlay">
					<span class="platform-cap__photo-label">Leap rep · OR coverage</span>
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
<section class="content-section">
	<div class="container">
		<div class="leap-intro reveal">
			<div>
				<h2 class="leap-intro__h">A platform reps actually want to use.</h2>
			</div>
			<div class="leap-intro__body">
				<p>Live performance dashboards. Real-time commission projections. Quota visibility by the day. Stride is built for the field, not just the back office. It's one of the reasons our reps stay and our partners join.</p>
			</div>
		</div>

		<div class="platform-repdash reveal">
			<div class="ui-frame">
				<div class="ui-frame__bar">
					<span class="ui-frame__dot"></span><span class="ui-frame__dot"></span><span class="ui-frame__dot"></span>
					<span class="ui-frame__title">Stride · Rep Dashboard</span>
				</div>
				<div class="ui-frame__body">
					<div class="stride-rep">
						<div class="stride-rep__tiles">
							<div class="stride-rep__tile">
								<div class="stride-rep__label">Commission MTD</div>
								<div class="stride-rep__val">$48,210</div>
								<div class="stride-rep__delta">+$6,420 vs. last month</div>
							</div>
							<div class="stride-rep__tile">
								<div class="stride-rep__label">Cases this week</div>
								<div class="stride-rep__val">18</div>
								<div class="stride-rep__delta">+4 vs. avg</div>
							</div>
						</div>
						<div class="stride-rep__quota">
							<div class="stride-rep__quota-head">
								<span>Quarterly quota</span>
								<span class="stride-rep__quota-pct">72%</span>
							</div>
							<div class="stride-rep__bar"><span class="stride-rep__bar-fill" style="width:72%"></span></div>
							<div class="stride-rep__legend">
								<span>$180k of $250k</span>
								<span>21 days left</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<!-- ── What's Next ────────────────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<div class="section-header reveal">
			<h2>Stride keeps moving forward.</h2>
			<p style="margin-top:var(--space-4);color:var(--color-text-2);font-weight:300;">Three things shipping over the next twelve months:</p>
		</div>

		<ol class="platform-list reveal">
			<li class="platform-list__item">
				<span class="platform-list__num">01</span>
				<span class="platform-list__text">Partner-facing dashboards for hospitals and manufacturers.</span>
			</li>
			<li class="platform-list__item">
				<span class="platform-list__num">02</span>
				<span class="platform-list__text">AI-assisted cross-checks for scrub sheets and billing.</span>
			</li>
			<li class="platform-list__item">
				<span class="platform-list__num">03</span>
				<span class="platform-list__text">Full Stride access for our distributor partner network.</span>
			</li>
		</ol>
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
