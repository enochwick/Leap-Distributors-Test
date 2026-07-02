<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#001F2B">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>


<!-- ── Overlay Nav ───────────────────────────────────────── -->
<div class="nav-overlay" id="nav-overlay" aria-hidden="true" role="dialog" aria-label="Navigation menu">
	<div class="nav-overlay__inner">
		<nav class="nav-overlay__nav" aria-label="Primary">
			<a href="<?php echo esc_url( home_url( '/platform/' ) ); ?>" class="nav-overlay__link">Platform</a>
			<a href="<?php echo esc_url( home_url( '/surgeons/' ) ); ?>" class="nav-overlay__link">Surgeons</a>
			<a href="<?php echo esc_url( home_url( '/partnerships/hospitals/' ) ); ?>" class="nav-overlay__link">Hospitals</a>
			<a href="<?php echo esc_url( home_url( '/partnerships/manufacturers/' ) ); ?>" class="nav-overlay__link">Manufacturers</a>
			<a href="<?php echo esc_url( home_url( '/distributors/' ) ); ?>" class="nav-overlay__link">Distributors</a>
			<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="nav-overlay__link">About</a>
			<a href="<?php echo esc_url( home_url( '/news/' ) ); ?>" class="nav-overlay__link">News</a>
			<a href="<?php echo esc_url( home_url( '/careers/' ) ); ?>" class="nav-overlay__link">Careers</a>
		</nav>
		<div class="nav-overlay__footer">
			<div class="nav-overlay__contact">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="nav-overlay__cta cta-talk">Let's Talk →</a>
			</div>
			<div class="nav-overlay__meta">
				<a href="tel:+18887765553">+1 888-776-5553</a>
				<a href="mailto:info@leapdistributors.com">info@leapdistributors.com</a>
			</div>
		</div>
	</div>
</div>

<!-- ── Header ────────────────────────────────────────────── -->
<header class="site-header" id="site-header">
	<div class="site-header__inner">

		<a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?> — Home">
			<?php $logo = get_template_directory_uri() . '/assets/images/leap-mark.png'; ?>
			<img src="<?php echo esc_url( $logo ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="logo--color" width="210" height="58">
			<img src="<?php echo esc_url( $logo ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="logo--white" width="210" height="58">
		</a>

		<!-- Desktop pill nav -->
		<nav class="nav-pill" aria-label="Primary">
			<ul class="nav-pill__list">
				<li class="nav-pill__item"><a class="nav-pill__link" href="<?php echo esc_url( home_url( '/platform/' ) ); ?>">Platform</a></li>
				<li class="nav-pill__item"><a class="nav-pill__link" href="<?php echo esc_url( home_url( '/surgeons/' ) ); ?>">Surgeons</a></li>
				<li class="nav-pill__item"><a class="nav-pill__link" href="<?php echo esc_url( home_url( '/partnerships/hospitals/' ) ); ?>">Hospitals</a></li>
				<li class="nav-pill__item"><a class="nav-pill__link" href="<?php echo esc_url( home_url( '/partnerships/manufacturers/' ) ); ?>">Manufacturers</a></li>
				<li class="nav-pill__item"><a class="nav-pill__link" href="<?php echo esc_url( home_url( '/distributors/' ) ); ?>">Distributors</a></li>
				<li class="nav-pill__item"><a class="nav-pill__link" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
				<li class="nav-pill__item"><a class="nav-pill__link" href="<?php echo esc_url( home_url( '/news/' ) ); ?>">News</a></li>
				<li class="nav-pill__item"><a class="nav-pill__link" href="<?php echo esc_url( home_url( '/careers/' ) ); ?>">Careers</a></li>
				<span class="nav-pill__cursor" aria-hidden="true"></span>
			</ul>
		</nav>

		<div class="site-header__right">

			<!-- Search -->
			<div class="nav-search" id="nav-search">
				<form class="nav-search__form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search" aria-label="Site search" autocomplete="off">
					<input class="nav-search__input" type="search" name="s" placeholder="Search…" aria-label="Search" maxlength="100" autocomplete="off">
					<button class="nav-search__close" type="button" aria-label="Close search">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
					</button>
				</form>
				<div class="nav-search__dropdown" id="nav-search-dropdown" aria-live="polite"></div>
				<button class="nav-search__toggle" id="nav-search-toggle" aria-label="Open search" aria-expanded="false">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
				</button>
			</div>

			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="site-header__cta cta-talk">Let's Talk <span aria-hidden="true">→</span></a>
			<button class="hamburger" id="nav-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="nav-overlay">
				<span class="hamburger__line hamburger__line--top"></span>
				<span class="hamburger__line hamburger__line--bottom"></span>
			</button>
		</div>

	</div>
</header>

<main id="main-content">
