<?php get_header(); ?>

<!-- ── Hero — Animated Gallery ────────────────────────────── -->
<section class="phg-section">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>

	<!-- Header text -->
	<div class="phg-header">
		<div class="phg-header__inner">
			<span class="section-label section-label--blue phg-fade">Stride — The Leap Platform</span>
			<h1 class="phg-title phg-fade">The custom tech<br>that runs every case.</h1>
			<p class="phg-lead phg-fade">Stride is Leap's own platform. Reps log every case in the OR as it happens, the paperwork generates itself, and every side of the relationship gets sharper data with every case.</p>
			<div class="phg-fade" style="margin-top:var(--space-8);">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--lg">Talk to our team <span aria-hidden="true">→</span></a>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--ghost" style="margin-left:var(--space-3);">Request a walkthrough</a>
			</div>
		</div>
	</div>

	<!-- 3D Scroll Image -->
	<div class="phg-scroll" id="phg-scroll">
		<div class="phg-sticky" id="phg-sticky">
			<div class="phg-single" id="phg-gallery">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/platform screenshots/business_central_dashboard_anonymized_clean_v2.png' ); ?>" alt="Stride dashboard" loading="eager">
			</div>
		</div>
	</div>
</section>

<!-- ── What Stride Is ─────────────────────────────────────────── -->
<section class="content-section" style="padding-top:var(--space-12)">
	<div class="container">
		<div class="leap-intro reveal">
			<div>
				<h2 class="leap-intro__h">Built by us. Used in every case.</h2>
			</div>
			<div class="leap-intro__body">
				<p>Most distribution still runs on paper, email, and Excel. We chose differently. Stride is custom-built for how medical device distribution actually works. Reps use it in the OR. Our team uses it to bill faster and cleaner. The data sharpens the business with every case logged.</p>
			</div>
		</div>
	</div>
</section>

<!-- ── Three Capabilities ────────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<div class="section-header reveal">
			<span class="section-label section-label--blue">Three Capabilities. One Platform.</span>
		</div>

		<!-- Capability 1 -->
		<div class="platform-cap reveal">
			<div class="platform-cap__copy">
				<span class="platform-cap__num">01</span>
				<h3 class="platform-cap__h">Built to move.</h3>
				<p>Reps log products in the OR as they're used. Pricing pulls from the appropriate contract. Scrub sheets generate themselves and route by manufacturer. Restock requests fire automatically. The case closes cleaner than it started.</p>
			</div>
			<div class="platform-cap__visual">
				<div class="ui-frame">
					<div class="ui-frame__bar">
						<span class="ui-frame__dot"></span><span class="ui-frame__dot"></span><span class="ui-frame__dot"></span>
						<span class="ui-frame__title">Stride · Case Log</span>
					</div>
					<div class="ui-frame__body">
						<div class="stride-case">
							<div class="stride-case__head">
								<span class="stride-case__title">ACL Reconstruction</span>
								<span class="stride-case__live">Live</span>
							</div>
							<dl class="stride-case__meta">
								<div><dt>Room</dt><dd>OR 3</dd></div>
								<div><dt>Started</dt><dd>09:14</dd></div>
								<div><dt>Rep</dt><dd>K.L.</dd></div>
							</dl>
							<div class="stride-case__rows">
								<div class="stride-row stride-row--done"><span class="stride-row__check">✓</span><span class="stride-row__name">Anchor, 5.5mm bio</span><span class="stride-row__mfr">Mfr C</span><span class="stride-row__time">09:31</span></div>
								<div class="stride-row stride-row--done"><span class="stride-row__check">✓</span><span class="stride-row__name">Interference screw 9×25</span><span class="stride-row__mfr">Mfr C</span><span class="stride-row__time">09:48</span></div>
								<div class="stride-row stride-row--done"><span class="stride-row__check">✓</span><span class="stride-row__name">Suture pack, ortho</span><span class="stride-row__mfr">Mfr D</span><span class="stride-row__time">10:02</span></div>
								<div class="stride-row"><span class="stride-row__check stride-row__check--pending"></span><span class="stride-row__name">Drain, closed</span><span class="stride-row__mfr">Mfr D</span><span class="stride-row__time">—</span></div>
							</div>
							<div class="stride-case__footer">
								<span class="stride-case__status">Auto-routed to Mfr C, D</span>
								<span class="stride-case__count">3 / 4 logged</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Capability 2 -->
		<div class="platform-cap platform-cap--reverse reveal">
			<div class="platform-cap__copy">
				<span class="platform-cap__num">02</span>
				<h3 class="platform-cap__h">Patients before products.</h3>
				<p>The platform doesn't replace the rep. It frees them to do the real work — knowing the procedure, the surgeon, and the patient on the table. Stride handles the paperwork so our reps don't have to look down. They stay in the case, advocating for surgeon choice and the right call for the patient.</p>
			</div>
			<div class="platform-cap__visual platform-cap__visual--photo">
				<img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=900&q=80" alt="Leap rep in OR" loading="lazy">
				<div class="platform-cap__photo-overlay">
					<span class="platform-cap__photo-label">Leap rep · OR coverage</span>
				</div>
			</div>
		</div>

		<!-- Capability 3 -->
		<div class="platform-cap reveal">
			<div class="platform-cap__copy">
				<span class="platform-cap__num">03</span>
				<h3 class="platform-cap__h">Engineered for intelligence.</h3>
				<p>Every case generates data, and we put it to work. Today, that means real-time visibility into product trends, case volume, surgeon trends, and rep performance, powering our work and informing our partners. Productized partner dashboards and AI-assisted billing checks are in active development.</p>
			</div>
			<div class="platform-cap__visual">
				<div class="ui-frame">
					<div class="ui-frame__bar">
						<span class="ui-frame__dot"></span><span class="ui-frame__dot"></span><span class="ui-frame__dot"></span>
						<span class="ui-frame__title">Stride · Insights</span>
					</div>
					<div class="ui-frame__body">
						<div class="stride-bi">
							<div class="stride-bi__card"><div class="stride-bi__label">Case Volume</div><div class="stride-bi__val">1,284</div><div class="stride-bi__delta stride-bi__delta--up">+12.4% MoM</div></div>
							<div class="stride-bi__card"><div class="stride-bi__label">Active Surgeons</div><div class="stride-bi__val">312</div><div class="stride-bi__delta stride-bi__delta--up">+8</div></div>
							<div class="stride-bi__card"><div class="stride-bi__label">Avg Bill Time</div><div class="stride-bi__val">2.4d</div><div class="stride-bi__delta stride-bi__delta--up">↓18%</div></div>
							<div class="stride-bi__card"><div class="stride-bi__label">Top Line</div><div class="stride-bi__val" style="font-size:var(--text-base)">Spine · Posterior</div><div class="stride-bi__delta stride-bi__delta--up">+22%</div></div>
							<div class="stride-bi__card stride-bi__card--full">
								<div class="stride-bi__label">Case Volume · 12 weeks</div>
								<div class="stride-bi__bars">
									<span style="height:45%"></span><span style="height:52%"></span><span style="height:48%"></span>
									<span style="height:61%"></span><span style="height:55%"></span><span style="height:70%"></span>
									<span style="height:65%"></span><span style="height:78%"></span><span style="height:72%"></span>
									<span style="height:83%"></span><span style="height:88%"></span><span style="height:95%"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
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
				<p>Live performance dashboards. Real-time commission projections. Quota visibility by the day. Stride is built for the field, not just the back office — which is one reason our reps stay and our partners join.</p>
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
		<div class="leap-intro reveal">
			<div>
				<h2 class="leap-intro__h">Stride keeps moving forward.</h2>
			</div>
			<div class="leap-intro__body">
				<p>Three things shipping over the next twelve months:</p>
			</div>
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
			<span class="section-label section-label--white reveal" style="justify-content:center;">See the platform in action</span>
			<h2 class="reveal">See Us Stride.</h2>
			<p class="reveal">The fastest way to understand what Leap does differently is to see the platform behind it.</p>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary btn--lg reveal">Request a walkthrough <span aria-hidden="true">→</span></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
