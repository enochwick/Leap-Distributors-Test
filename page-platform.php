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
				<button type="button" class="btn btn--primary btn--lg" data-open-walkthrough>Request a walkthrough <span aria-hidden="true">→</span></button>
			</div>
			<?php if ( isset( $_GET['walkthrough'] ) && $_GET['walkthrough'] === 'success' ) : ?>
				<div class="form-feedback form-feedback--success phg-fade" style="margin-top:var(--space-6);max-width:520px;">
					<strong>Request received.</strong> Our team will reach out to schedule your walkthrough.
				</div>
			<?php elseif ( isset( $_GET['walkthrough'] ) && $_GET['walkthrough'] === 'error' ) : ?>
				<div class="form-feedback form-feedback--error phg-fade" style="margin-top:var(--space-6);max-width:520px;">
					Something went wrong. Please try again or email <a href="mailto:info@leapdistributors.com">info@leapdistributors.com</a>.
				</div>
			<?php endif; ?>
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
		<div class="platform-cap platform-stack__card" data-stack-index="0" data-glow>
			<div data-glow-inner></div>
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
		<div class="platform-cap platform-cap--reverse platform-stack__card" data-stack-index="1" data-glow>
			<div data-glow-inner></div>
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
		<div class="platform-cap platform-stack__card" data-stack-index="2" data-glow>
			<div data-glow-inner></div>
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
			<button type="button" class="btn btn--primary btn--lg reveal" data-open-walkthrough>Request a walkthrough <span aria-hidden="true">→</span></button>
		</div>
	</div>
</section>

<!-- ── Walkthrough Request Modal ──────────────────────────── -->
<div class="apply-modal" id="walkthrough-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Request a walkthrough">
	<div class="apply-modal__dialog" role="document">
		<button type="button" class="apply-modal__close" data-close-walkthrough aria-label="Close">&times;</button>

		<span class="section-label section-label--blue">Request a walkthrough</span>
		<h3 class="apply-modal__title">See Stride in action</h3>
		<p class="apply-modal__sub">Tell us a bit about you and we’ll set up a live walkthrough of the platform.</p>

		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="apply-modal__form">
			<input type="hidden" name="action" value="leap_walkthrough_form">
			<?php wp_nonce_field( 'leap_walkthrough_form', 'leap_walkthrough_nonce' ); ?>

			<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
				<div class="form-group">
					<label class="form-label" for="wt-first">First Name</label>
					<input class="form-input" type="text" id="wt-first" name="first_name" placeholder="John" required>
				</div>
				<div class="form-group">
					<label class="form-label" for="wt-last">Last Name</label>
					<input class="form-input" type="text" id="wt-last" name="last_name" placeholder="Smith" required>
				</div>
			</div>
			<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
				<div class="form-group">
					<label class="form-label" for="wt-email">Work Email</label>
					<input class="form-input" type="email" id="wt-email" name="email" placeholder="john@company.com" required>
				</div>
				<div class="form-group">
					<label class="form-label" for="wt-phone">Phone <span style="color:var(--color-text-3);font-weight:400;">(optional)</span></label>
					<input class="form-input" type="tel" id="wt-phone" name="phone" placeholder="(555) 555-5555">
				</div>
			</div>
			<div class="form-group">
				<label class="form-label" for="wt-company">Company / Organization</label>
				<input class="form-input" type="text" id="wt-company" name="company" placeholder="Your hospital, practice, or company">
			</div>
			<div class="form-group">
				<label class="form-label" for="wt-role">I am a…</label>
				<select class="form-input" id="wt-role" name="role">
					<option value="">Select your role</option>
					<option>Surgeon</option>
					<option>Hospital / Healthcare Facility</option>
					<option>Distributor / Independent Rep</option>
					<option>Manufacturer</option>
					<option>Other</option>
				</select>
			</div>
			<div class="form-group">
				<label class="form-label" for="wt-message">What would you like to see? <span style="color:var(--color-text-3);font-weight:400;">(optional)</span></label>
				<textarea class="form-input" id="wt-message" name="message" placeholder="Tell us what matters most to your team…"></textarea>
			</div>
			<button type="submit" class="btn btn--primary btn--lg" style="width:100%;justify-content:center;">Request walkthrough <span aria-hidden="true">→</span></button>
		</form>
	</div>
</div>

<?php get_footer(); ?>
