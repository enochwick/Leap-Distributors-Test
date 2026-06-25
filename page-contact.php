<?php get_header(); ?>

<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="hero__gradient"></div>
	<div class="container">
		<div class="page-hero__inner page-hero__inner--center">
			<span class="page-hero__eyebrow">Get in Touch</span>
			<h1 class="page-hero__title">Let's Talk.</h1>
			<p class="page-hero__lead">Whether you're exploring a partnership, need support, or just want to learn more, we'd love to hear from you.</p>
		</div>
	</div>
</section>

<section class="content-section">
	<div class="container">
		<div class="grid-2" style="gap:var(--space-16);align-items:flex-start;">

			<div>
				<span class="section-label section-label--no-line reveal">Send a Message</span>

				<?php if ( isset( $_GET['contact'] ) && $_GET['contact'] === 'success' ) : ?>
					<div class="form-feedback form-feedback--success reveal" style="margin-top:var(--space-6);">
						<strong>Message sent.</strong> We'll be in touch shortly.
					</div>
				<?php elseif ( isset( $_GET['contact'] ) && $_GET['contact'] === 'error' ) : ?>
					<div class="form-feedback form-feedback--error reveal" style="margin-top:var(--space-6);">
						Something went wrong. Please try again or email us directly at <a href="mailto:info@leapdistributors.com">info@leapdistributors.com</a>.
					</div>
				<?php endif; ?>

				<form class="reveal" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" style="margin-top:var(--space-6);">
					<input type="hidden" name="action" value="leap_contact_form">
					<?php wp_nonce_field( 'leap_contact_form', 'leap_contact_nonce' ); ?>

					<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);margin-bottom:var(--space-4);">
						<div class="form-group" style="margin-bottom:0;">
							<label class="form-label" for="first-name">First Name</label>
							<input class="form-input" type="text" id="first-name" name="first_name" placeholder="John" required>
						</div>
						<div class="form-group" style="margin-bottom:0;">
							<label class="form-label" for="last-name">Last Name</label>
							<input class="form-input" type="text" id="last-name" name="last_name" placeholder="Smith" required>
						</div>
					</div>
					<div class="form-group">
						<label class="form-label" for="email">Email Address</label>
						<input class="form-input" type="email" id="email" name="email" placeholder="john@company.com" required>
					</div>
					<div class="form-group">
						<label class="form-label" for="role">I am a…</label>
						<select class="form-input" id="role" name="role">
							<option value="">Select your role</option>
							<option>Surgeon</option>
							<option>Distributor / Independent Rep</option>
							<option>Hospital / Healthcare Facility</option>
							<option>Manufacturer</option>
							<option>Other</option>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label" for="message">Message</label>
						<textarea class="form-input" id="message" name="message" placeholder="Tell us how we can help…" required></textarea>
					</div>
					<button type="submit" class="btn btn--primary btn--lg" style="width:100%;justify-content:center;">Send Message <span aria-hidden="true">→</span></button>
				</form>
			</div>

			<div>
				<span class="section-label section-label--no-line reveal">Contact Info</span>
				<div style="margin-top:var(--space-6);display:flex;flex-direction:column;gap:var(--space-6);">
					<div class="card reveal" data-glow>
						<div style="display:flex;gap:var(--space-4);align-items:center;">
							<div class="feature-card__icon" style="background:rgba(230,83,0,0.08);color:var(--color-orange);flex-shrink:0;">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.24h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9c1.91 3.28 4.65 5.61 8.63 8.63l1.16-1.16a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
							</div>
							<div>
								<div style="font-size:var(--text-xs);color:var(--color-text-3);text-transform:uppercase;letter-spacing:0.08em;font-weight:500;margin-bottom:var(--space-1);">Phone</div>
								<a href="tel:+18887765553" style="font-weight:500;font-size:var(--text-lg);color:var(--color-text);">+1 888-776-5553</a>
							</div>
						</div>
					</div>
					<div class="card reveal" data-glow>
						<div style="display:flex;gap:var(--space-4);align-items:center;">
							<div class="feature-card__icon" style="background:rgba(230,83,0,0.08);color:var(--color-orange);flex-shrink:0;">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
							</div>
							<div>
								<div style="font-size:var(--text-xs);color:var(--color-text-3);text-transform:uppercase;letter-spacing:0.08em;font-weight:500;margin-bottom:var(--space-1);">Email</div>
								<a href="mailto:info@leapdistributors.com" style="font-weight:500;color:var(--color-text);">info@leapdistributors.com</a>
							</div>
						</div>
					</div>
					<div class="card reveal" data-glow>
						<div style="display:flex;gap:var(--space-4);align-items:center;">
							<div class="feature-card__icon" style="background:rgba(230,83,0,0.08);color:var(--color-orange);flex-shrink:0;">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
							</div>
							<div>
								<div style="font-size:var(--text-xs);color:var(--color-text-3);text-transform:uppercase;letter-spacing:0.08em;font-weight:500;margin-bottom:var(--space-1);">Location</div>
								<span style="font-weight:500;color:var(--color-text);">Dallas, Texas</span>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</section>

<?php get_footer(); ?>
