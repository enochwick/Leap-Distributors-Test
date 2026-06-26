</main>

<footer class="site-footer">
	<div class="site-footer__top">
		<div class="container">
			<div class="site-footer__grid">

				<div class="site-footer__brand">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-footer__logo">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/images/leap-mark.png" alt="Leap Distributors" width="210" height="58">
					</a>
					<p class="site-footer__tagline">Leap is the new standard in medical device distribution. One distribution partner across surgeons, hospitals, and manufacturers, all running in Stride.</p>
					<div class="site-footer__social">
						<a href="https://www.linkedin.com/company/leapdistributors" class="social-link" aria-label="LinkedIn" target="_blank" rel="noopener">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.55v-5.57c0-1.33-.03-3.04-1.85-3.04-1.85 0-2.13 1.45-2.13 2.94v5.67H9.36V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.38-1.85 3.61 0 4.28 2.38 4.28 5.47v6.27zM5.34 7.43a2.06 2.06 0 1 1 0-4.13 2.06 2.06 0 0 1 0 4.13zM7.12 20.45H3.55V9h3.57v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z"/></svg>
						</a>
					</div>
				</div>

				<div class="site-footer__col">
					<h4 class="site-footer__heading">Who We Serve</h4>
					<ul class="site-footer__links">
						<li><a href="<?php echo esc_url( home_url( '/surgeons/' ) ); ?>">Surgeons</a></li>
						<li><a href="<?php echo esc_url( home_url( '/partnerships/hospitals/' ) ); ?>">Hospitals</a></li>
						<li><a href="<?php echo esc_url( home_url( '/partnerships/manufacturers/' ) ); ?>">Manufacturers</a></li>
						<li><a href="<?php echo esc_url( home_url( '/distributors/' ) ); ?>">Distributors &amp; Reps</a></li>
					</ul>
				</div>

				<div class="site-footer__col">
					<h4 class="site-footer__heading">Company</h4>
					<ul class="site-footer__links">
						<li><a href="<?php echo esc_url( home_url( '/platform/' ) ); ?>">Platform</a></li>
						<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
						<li><a href="<?php echo esc_url( home_url( '/news/' ) ); ?>">News &amp; Insights</a></li>
						<li><a href="<?php echo esc_url( home_url( '/careers/' ) ); ?>">Careers</a></li>
					</ul>
				</div>

				<div class="site-footer__col">
					<h4 class="site-footer__heading">Contact</h4>
					<address class="site-footer__contact">
						<p>
							<a href="mailto:info@leapdistributors.com">info@leapdistributors.com</a>
						</p>
						<p style="margin:0;">
							<a href="tel:+18887765553">+1 888-776-5553</a>
						</p>
						<p style="margin-top:var(--space-3);">
							<span style="display:block;font-size:var(--text-xs);color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:var(--space-1);">Dallas HQ</span>
							3151 Halifax Street, Suite 160<br>
							Dallas, TX 75219
						</p>
					</address>
					<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="arrow-link arrow-link--white" style="margin-top:var(--space-6);display:inline-flex;">Let's talk <span aria-hidden="true">→</span></a>
				</div>

			</div>
		</div>
	</div>

	<div class="site-footer__bottom">
		<div class="container">
			<p class="site-footer__copy">
				&copy; <?php echo date( 'Y' ); ?> Leap Distributors&reg;. All rights reserved.
			</p>
			<span class="site-footer__copy" style="font-style:italic;opacity:0.85;">Better. Together.</span>
		</div>
	</div>
</footer>

<!-- ── Floating Search (mobile/tablet) ─────────────────── -->
<div id="leap-fab-search" class="fabs">
	<div class="fabs__results" id="fabs-results" aria-live="polite"></div>
	<form class="fabs__form" id="fabs-panel" role="search" autocomplete="off" aria-hidden="true">
		<input class="fabs__input" id="fabs-input" type="search" name="s" placeholder="Search Leap…" aria-label="Search" maxlength="100">
	</form>
	<button class="fabs__toggle" id="fabs-toggle" aria-label="Open search" aria-expanded="false">
		<svg class="fabs__icon-search" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
		<svg class="fabs__icon-close" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
	</button>
</div>

<!-- ── AI Chat Widget ─────────────────────────────────── -->
<div id="leap-chat" class="lc" aria-live="polite" aria-label="Chat with Trey">

	<!-- Trey peeking next to the chat button -->
	<div class="lc__trey" aria-hidden="true">
		<span class="lc__trey-bubble">Hi, I'm Trey! 👋</span>
		<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/trey.webp' ); ?>" alt="Trey, your Leap assistant" width="84" height="118" loading="lazy" decoding="async">
	</div>

	<button class="lc__toggle" id="lc-toggle" aria-label="Open chat with Trey" aria-expanded="false">
		<svg class="lc__icon-chat" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
		<svg class="lc__icon-close" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
	</button>

	<div class="lc__panel" id="lc-panel" aria-hidden="true">
		<div class="lc__header">
			<div class="lc__header-info">
				<div class="lc__avatar" aria-hidden="true">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/trey.webp' ); ?>" alt="Trey">
				</div>
				<div>
					<div class="lc__name">Trey</div>
					<div class="lc__status"><span class="lc__status-dot"></span>Leap Assistant</div>
				</div>
			</div>
			<button class="lc__handover-btn" id="lc-handover" type="button">Talk to a person</button>
		</div>

		<div class="lc__messages" id="lc-messages">
			<div class="lc__msg lc__msg--ai">
				<p>Hi, I'm Trey, your Leap assistant. Ask me anything about our distribution services, the Stride platform, or how we work with surgeons, hospitals, and manufacturers.</p>
			</div>
		</div>

		<form class="lc__form" id="lc-form" autocomplete="off">
			<textarea class="lc__input" id="lc-input" rows="1" placeholder="Ask something…" aria-label="Message" maxlength="500" required></textarea>
			<button class="lc__send" type="submit" aria-label="Send">
				<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
			</button>
		</form>
	</div>

</div>

<?php wp_footer(); ?>
</body>
</html>
