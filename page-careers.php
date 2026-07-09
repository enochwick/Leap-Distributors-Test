<?php get_header(); ?>

<section class="page-hero">
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="hero__gradient"></div>
	<div class="container">
		<div class="page-hero__inner page-hero__inner--center">
			<span class="page-hero__eyebrow">Join the Team</span>
			<h1 class="page-hero__title">Build the Future of Healthcare Distribution.</h1>
			<p class="page-hero__lead">We're a fast-moving, mission-driven team that values people who are hungry, humble, and committed to doing good work. Come help us move healthcare forward.</p>
		</div>
	</div>
</section>

<!-- ── Culture ────────────────────────────────────────────── -->
<section class="content-section">
	<div class="container">
		<div class="grid-2" style="gap:var(--space-20);align-items:center;">
			<div>
				<span class="section-label section-label--no-line reveal">Life at Leap</span>
				<h2 class="reveal" style="margin-bottom:var(--space-6);">A Place Where Your Work Actually Matters</h2>
				<p class="reveal" style="color:var(--color-text-2);font-weight:300;margin-bottom:var(--space-8);">At Leap, you're not just filling an order, you're part of a chain that changes patients' lives. Every role here contributes to healthcare getting better. That's not something you find everywhere.</p>
				<div style="display:flex;flex-direction:column;gap:var(--space-5);">
					<div class="reveal" style="display:flex;gap:var(--space-4);align-items:flex-start;">
						<div style="width:8px;height:8px;border-radius:50%;background:var(--color-orange);margin-top:6px;flex-shrink:0;"></div>
						<div><strong>Mission-first culture</strong> — We make decisions based on what's right for healthcare, not just what's profitable.</div>
					</div>
					<div class="reveal" style="display:flex;gap:var(--space-4);align-items:flex-start;">
						<div style="width:8px;height:8px;border-radius:50%;background:var(--color-orange);margin-top:6px;flex-shrink:0;"></div>
						<div><strong>Ownership mentality</strong> — You won't get lost in a giant org chart here. Your contributions are visible and valued.</div>
					</div>
					<div class="reveal" style="display:flex;gap:var(--space-4);align-items:flex-start;">
						<div style="width:8px;height:8px;border-radius:50%;background:var(--color-orange);margin-top:6px;flex-shrink:0;"></div>
						<div><strong>Growing fast</strong> — We're expanding our network, our team, and our impact. There's room to grow with us.</div>
					</div>
				</div>
			</div>
			<div class="reveal-right">
				<div style="border-radius:var(--radius-2xl);overflow:hidden;aspect-ratio:1/1;background:var(--color-surface);">
					<img src="<?php echo get_template_directory_uri(); ?>/assets/images/built-for/careers-culture.webp" alt="The Leap Distributors team collaborating in the office" style="width:100%;height:100%;object-fit:cover;" loading="lazy" decoding="async">
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ── Open Roles ─────────────────────────────────────────── -->
<section class="content-section content-section--alt">
	<div class="container">
		<div class="section-header section-header--center">
			<span class="section-label section-label--blue reveal">Open Roles</span>
			<h2 class="reveal">Current Opportunities</h2>
		</div>

		<?php if ( isset( $_GET['application'] ) && $_GET['application'] === 'success' ) : ?>
			<div class="form-feedback form-feedback--success reveal" style="max-width:820px;margin:0 auto var(--space-8);">
				<strong>Application received.</strong> Thanks — our team will review it and be in touch.
			</div>
		<?php elseif ( isset( $_GET['application'] ) && $_GET['application'] === 'error' ) : ?>
			<div class="form-feedback form-feedback--error reveal" style="max-width:820px;margin:0 auto var(--space-8);">
				Something went wrong. Please try again or email your resume to <a href="mailto:careers@leapdistributors.com">careers@leapdistributors.com</a>.
			</div>
		<?php endif; ?>

		<div style="max-width:820px;margin-inline:auto;">
			<article class="job-card card reveal" data-glow>
				<div class="job-card__head">
					<div>
						<div style="display:flex;gap:var(--space-2);margin-bottom:var(--space-2);">
							<span class="pill">Clinical Field Sales</span>
							<span class="pill pill--teal">Full-Time</span>
						</div>
						<h4 style="margin-bottom:var(--space-1);">Surgical Consultant</h4>
						<p style="font-size:var(--text-sm);color:var(--color-text-3);">Dallas–Fort Worth Metroplex · Field-based</p>
					</div>
					<button type="button" class="btn btn--outline" style="flex-shrink:0;" data-open-apply data-position="Surgical Consultant" data-title="Surgical Consultant" data-subtitle="Dallas–Fort Worth Metroplex · Full-Time">Apply</button>
				</div>

				<details class="job-toggle">
					<summary class="job-toggle__summary">
						<span class="job-toggle__label job-toggle__label--more">View more</span>
						<span class="job-toggle__label job-toggle__label--less">View less</span>
						<svg class="job-toggle__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
					</summary>

					<div class="job-detail">
					<p>Bring your talents to an industry leader in medical technology and healthcare solutions. You can be proud to sell medical technologies in an ever changing, fast paced environment that restores quality of life for patients. Our expansive portfolio delivers measurable clinical and economic value — and opens doors. You will be empowered to shape your own career. We support your growth with the training, mentorship, and guidance you need to own your future success.</p>

					<h5>Who We Are</h5>
					<p>Leap Distributors is a leading supplier of innovative products and services to the medical community, helping our clients stay up to date on the latest procedural solutions. We act as a one-stop-shop — with the right tools, right patients, latest tech, and less hassle. More than just hardware and biologics, we keep surgeons at the top of their game with new innovative ways to approach surgery and the growth of their practice.</p>

					<h5>Who You Are</h5>
					<p>You embody the entrepreneurial spirit, thrive in a high-energy environment, and consider yourself a problem-solver. You don't mind working independently and taking ownership of your responsibilities. Networking comes naturally to you, and you have the innate ability to recognize opportunities as they come your way. Learning is important to you, and you are interested in new innovative products and how they can positively impact the surgical environment. You are organized, precise, and rarely drop the ball when it comes to juggling multiple tasks.</p>

					<h5>What You'll Do</h5>
					<p>At Leap Distributors, the Surgical Consultant supports the Spine, Orthopedics and Biologics business in the areas of surgical coverage, follow-up support, troubleshooting, and customer service. This person will be engaged in basic market development activities depending upon the needs of the assigned territory and district. This is a field-based role. Responsibilities may include the following:</p>
					<ul>
						<li>Advance the Company's sales of surgical products by providing clinical and logistical expertise in hospitals and operating rooms — such as independent coverage of surgical cases, management of billing/purchase orders, logistics, and asset management.</li>
						<li>Represent and cover surgical cases professionally, efficiently, and effectively.</li>
						<li>Articulate and train on the appropriate use of surgical instruments and implants to surgeons, nurses, and sales representatives.</li>
						<li>Process instrument sets and paperwork efficiently and promptly.</li>
						<li>Maintain education on appropriate implant offerings, instrumentations, surgical techniques, and procedures.</li>
						<li>Resolve customer complaints and advise leadership promptly of any situation outside scope of authority.</li>
						<li>Recommend the addition of new product, modification, or deletion of current product to accounts.</li>
						<li>Prospect, identify and convert prospective accounts. Develop account-specific sales plans and strategies to grow and convert business.</li>
						<li>Increase product sales for assigned territory or accounts. Utilize product knowledge to provide solutions which drive sales and ensure proper utilization of product offerings.</li>
						<li>Build relationships and identify buyer points within the account. Build customer relationships with key surgeon and hospital personnel.</li>
						<li>Independently provide appropriate case coverage and manage surgery schedule via appropriate surgeon contact. Order, transport and prepare surgical trays as needed.</li>
						<li>Educate potential end users and customers through workshops, in-services and labs.</li>
						<li>Maintain all equipment in proper condition and utilize them when necessary to support sales efforts.</li>
						<li>Identify and handle any competitive threats as necessary.</li>
						<li>Strategically encourage sales via a product mix that promotes selling objectives.</li>
						<li>Share key customer, procedural and marketplace insights with other sales, clinical, marketing, and strategic account teams to improve on solutions, service levels and support sales growth.</li>
					</ul>

					<h5>Qualifications</h5>
					<p>We seek out and hire a diverse workforce at every level: we need fresh ideas and inclusive insights to continue to be an innovative industry leader. That's why we make it a point to seek out, attract and develop employees who are patient-centric, passionate, and who represent the same wide variety of life experiences as our patients.</p>
					<p class="job-detail__label">Education &amp; Experience</p>
					<ul>
						<li>Bachelor's degree, or</li>
						<li>Associate degree or Medical Certification (CST, PT, etc.), or</li>
						<li>Minimum of 3 years of professional and/or related experience</li>
						<li>Clinical/Medical experience</li>
					</ul>
					<p class="job-detail__label">Other</p>
					<ul>
						<li>The ability to work in an operating room environment.</li>
						<li>A valid driver's license issued in the United States.</li>
						<li>The ability to travel, which may include weekend and/or overnight travel.</li>
						<li>Residence in or ability to relocate to the posted territory.</li>
						<li>Strong interpersonal communication, influencing, critical thinking and problem-solving skills required.</li>
						<li>Experienced in data analysis and excellent problem-solving skills.</li>
						<li>Results orientation / prioritization.</li>
						<li>Ability to work independently and autonomously.</li>
						<li>Partnership and collaboration — ability to work in a complex reporting structure.</li>
						<li>High level of accuracy and attention to detail.</li>
						<li>Demonstrated ability to understand, interpret, communicate, and work in complex environments.</li>
						<li>Functional knowledge of human anatomy and physiology, basic knowledge of surgery.</li>
						<li>Strong technical product knowledge of surgical instruments, procedures, protocols, and solutions preferred.</li>
						<li>Work weekends, evenings, and holidays as required on an emergency basis.</li>
						<li>Must be able to lift and carry multiple surgical trays (minimum 24 lbs).</li>
					</ul>

					<h5>What We Offer</h5>
					<p>Leap Distributors is an equal opportunity employer, and all qualified applicants will receive consideration for employment without regard to race, color, religion, sex, sexual orientation, gender identity, genetic information, national origin, protected veteran status, disability status, or any other characteristic protected by law. We will ensure that individuals with disabilities are provided reasonable accommodation to participate in the job application or interview process, to perform essential job functions, and to receive other benefits and privileges of employment. Please contact us to request accommodation.</p>
					<p>Leap Surgical, LLC offers the following benefits:</p>
					<ul>
						<li>Full medical, dental and vision benefits</li>
						<li>Competitive pay</li>
						<li>Flexible schedule</li>
						<li>Opportunity for advancement</li>
					</ul>

						<button type="button" class="btn btn--primary" style="margin-top:var(--space-4);" data-open-apply data-position="Surgical Consultant" data-title="Surgical Consultant" data-subtitle="Dallas–Fort Worth Metroplex · Full-Time">Apply for this role</button>
					</div>
				</details>
			</article>
		</div>

		<div class="text-center reveal" style="margin-top:var(--space-10);">
			<p style="color:var(--color-text-2);margin-bottom:var(--space-4);">Don't see the perfect fit? Reach out anyway! We're always open to talking to exceptional people.</p>
			<button type="button" class="btn btn--primary" data-open-apply data-position="General Application" data-title="Send Us Your Resume" data-subtitle="Don't see the perfect fit? Introduce yourself and we'll keep you in mind.">Send Us Your Resume</button>
		</div>
	</div>
</section>

<!-- ── Feature Band ───────────────────────────────────────── -->
<section class="feature-band reveal">
	<div class="container">
		<figure class="feature-band__media">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/LeapGroup.png' ); ?>" alt="The Leap Distributors sales team together at a company event" loading="lazy" decoding="async">
		</figure>
	</div>
</section>

<section class="cta-banner">
	<div class="cta-banner__bg-grid"></div>
	<canvas class="mesh-canvas" aria-hidden="true"></canvas>
	<div class="container">
		<div class="cta-banner__inner">
			<h2 class="reveal">Let's Build Something Together</h2>
			<p class="reveal">If you're passionate about healthcare and want your work to matter, we'd love to hear from you.</p>
			<div class="cta-banner__actions reveal">
				<a href="mailto:careers@leapdistributors.com" class="btn btn--primary btn--lg">Email Our Team</a>
				<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="btn btn--hero-ghost">Learn About Leap</a>
			</div>
		</div>
	</div>
</section>

<!-- ── Apply Modal ────────────────────────────────────────── -->
<div class="apply-modal" id="apply-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Apply for Surgical Consultant">
	<div class="apply-modal__dialog" role="document">
		<button type="button" class="apply-modal__close" data-close-apply aria-label="Close">&times;</button>

		<span class="section-label section-label--blue">Apply</span>
		<h3 class="apply-modal__title" id="apply-modal-title">Surgical Consultant</h3>
		<p class="apply-modal__sub" id="apply-modal-sub">Dallas–Fort Worth Metroplex · Full-Time</p>

		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data" class="apply-modal__form">
			<input type="hidden" name="action" value="leap_application_form">
			<input type="hidden" name="position" id="app-position" value="Surgical Consultant">
			<?php wp_nonce_field( 'leap_application_form', 'leap_application_nonce' ); ?>

			<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
				<div class="form-group">
					<label class="form-label" for="app-first">First Name</label>
					<input class="form-input" type="text" id="app-first" name="first_name" placeholder="John" required>
				</div>
				<div class="form-group">
					<label class="form-label" for="app-last">Last Name</label>
					<input class="form-input" type="text" id="app-last" name="last_name" placeholder="Smith" required>
				</div>
			</div>
			<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
				<div class="form-group">
					<label class="form-label" for="app-email">Email Address</label>
					<input class="form-input" type="email" id="app-email" name="email" placeholder="john@email.com" required>
				</div>
				<div class="form-group">
					<label class="form-label" for="app-phone">Phone</label>
					<input class="form-input" type="tel" id="app-phone" name="phone" placeholder="(555) 555-5555">
				</div>
			</div>
			<div class="form-group">
				<label class="form-label" for="app-linkedin">LinkedIn or Portfolio <span style="color:var(--color-text-3);font-weight:400;">(optional)</span></label>
				<input class="form-input" type="url" id="app-linkedin" name="linkedin" placeholder="https://linkedin.com/in/…">
			</div>
			<div class="form-group">
				<label class="form-label" for="app-resume">Resume <span style="color:var(--color-text-3);font-weight:400;">(PDF, DOC, or DOCX)</span></label>
				<input class="form-input--file" type="file" id="app-resume" name="resume" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
			</div>
			<div class="form-group">
				<label class="form-label" for="app-message">Why Leap? <span style="color:var(--color-text-3);font-weight:400;">(optional)</span></label>
				<textarea class="form-input" id="app-message" name="message" placeholder="Tell us a bit about yourself and why you're a fit…"></textarea>
			</div>
			<button type="submit" class="btn btn--primary btn--lg" style="width:100%;justify-content:center;">Submit Application <span aria-hidden="true">→</span></button>
			<p class="apply-modal__note">Prefer email? Send your resume to <a href="mailto:careers@leapdistributors.com">careers@leapdistributors.com</a>.</p>
		</form>
	</div>
</div>

<?php get_footer(); ?>
