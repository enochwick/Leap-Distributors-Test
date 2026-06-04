<?php
add_action( 'acf/init', 'leap_register_acf_fields' );

function leap_register_acf_fields() {

	// ── Site Settings (Options Page — ACF Pro only) ──────────────────────────

	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_local_field_group( [
			'key'      => 'group_site_settings',
			'title'    => 'Site Settings',
			'fields'   => [
				[
					'key'          => 'field_site_phone',
					'label'        => 'Phone Number (href value)',
					'name'         => 'site_phone',
					'type'         => 'text',
					'instructions' => 'No spaces or dashes, e.g. +18887765553',
				],
				[
					'key'          => 'field_site_phone_display',
					'label'        => 'Phone Display Text',
					'name'         => 'site_phone_display',
					'type'         => 'text',
					'instructions' => 'e.g. +1 888-776-5553',
				],
				[
					'key'   => 'field_site_email',
					'label' => 'Email Address',
					'name'  => 'site_email',
					'type'  => 'email',
				],
				[
					'key'   => 'field_site_location',
					'label' => 'Location',
					'name'  => 'site_location',
					'type'  => 'text',
				],
			],
			'location' => [
				[ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'site-settings' ] ],
			],
		] );
	}

	// ── Homepage ──────────────────────────────────────────────────────────────

	acf_add_local_field_group( [
		'key'    => 'group_homepage',
		'title'  => 'Homepage Content',
		'fields' => [
			[
				'key'   => 'field_hero_eyebrow',
				'label' => 'Hero Eyebrow',
				'name'  => 'hero_eyebrow',
				'type'  => 'text',
			],
			[
				'key'          => 'field_hero_headline',
				'label'        => 'Hero Headline',
				'name'         => 'hero_headline',
				'type'         => 'textarea',
				'rows'         => 3,
				'instructions' => 'One line per row. Last line gets the accent color.',
			],
			[
				'key'   => 'field_hero_subtext',
				'label' => 'Hero Subtext',
				'name'  => 'hero_subtext',
				'type'  => 'textarea',
				'rows'  => 3,
			],
			[
				'key'   => 'field_hero_cta1_text',
				'label' => 'Hero CTA 1 — Label',
				'name'  => 'hero_cta1_text',
				'type'  => 'text',
			],
			[
				'key'   => 'field_hero_cta1_url',
				'label' => 'Hero CTA 1 — URL',
				'name'  => 'hero_cta1_url',
				'type'  => 'text',
			],
			[
				'key'   => 'field_hero_cta2_text',
				'label' => 'Hero CTA 2 — Label',
				'name'  => 'hero_cta2_text',
				'type'  => 'text',
			],
			[
				'key'   => 'field_hero_cta2_url',
				'label' => 'Hero CTA 2 — URL',
				'name'  => 'hero_cta2_url',
				'type'  => 'text',
			],
			[
				'key'   => 'field_quote_text',
				'label' => 'Quote Text',
				'name'  => 'quote_text',
				'type'  => 'textarea',
				'rows'  => 4,
			],
			[
				'key'   => 'field_quote_author',
				'label' => 'Quote Attribution',
				'name'  => 'quote_author',
				'type'  => 'text',
			],
			[
				'key'   => 'field_cta_heading',
				'label' => 'CTA Banner — Heading',
				'name'  => 'cta_heading',
				'type'  => 'text',
			],
			[
				'key'   => 'field_cta_subtext',
				'label' => 'CTA Banner — Subtext',
				'name'  => 'cta_subtext',
				'type'  => 'textarea',
				'rows'  => 2,
			],
		],
		'location' => [
			[ [ 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ] ],
		],
	] );

	// ── Page Hero (all inner pages) ───────────────────────────────────────────

	acf_add_local_field_group( [
		'key'    => 'group_page_hero',
		'title'  => 'Page Hero',
		'fields' => [
			[
				'key'   => 'field_ph_eyebrow',
				'label' => 'Eyebrow',
				'name'  => 'page_hero_eyebrow',
				'type'  => 'text',
			],
			[
				'key'   => 'field_ph_title',
				'label' => 'Page Title',
				'name'  => 'page_hero_title',
				'type'  => 'text',
			],
			[
				'key'   => 'field_ph_lead',
				'label' => 'Lead Paragraph',
				'name'  => 'page_hero_lead',
				'type'  => 'textarea',
				'rows'  => 3,
			],
			[
				'key'   => 'field_ph_cta1_text',
				'label' => 'CTA 1 — Label',
				'name'  => 'page_hero_cta1_text',
				'type'  => 'text',
			],
			[
				'key'   => 'field_ph_cta1_url',
				'label' => 'CTA 1 — URL',
				'name'  => 'page_hero_cta1_url',
				'type'  => 'text',
			],
			[
				'key'   => 'field_ph_cta2_text',
				'label' => 'CTA 2 — Label',
				'name'  => 'page_hero_cta2_text',
				'type'  => 'text',
			],
			[
				'key'   => 'field_ph_cta2_url',
				'label' => 'CTA 2 — URL',
				'name'  => 'page_hero_cta2_url',
				'type'  => 'text',
			],
		],
		'location' => [
			[
				[ 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ],
				[ 'param' => 'page_type', 'operator' => '!=', 'value' => 'front_page' ],
			],
		],
	] );

	// ── About Page — Content ──────────────────────────────────────────────────

	acf_add_local_field_group( [
		'key'    => 'group_about',
		'title'  => 'About — Mission & Vision',
		'fields' => [
			[
				'key'   => 'field_about_who',
				'label' => 'Who We Are paragraph',
				'name'  => 'about_who',
				'type'  => 'textarea',
				'rows'  => 4,
			],
			[
				'key'   => 'field_about_mission',
				'label' => 'Mission Statement',
				'name'  => 'about_mission',
				'type'  => 'textarea',
				'rows'  => 3,
			],
			[
				'key'   => 'field_about_vision',
				'label' => 'Vision Statement',
				'name'  => 'about_vision',
				'type'  => 'textarea',
				'rows'  => 3,
			],
		],
		'location' => [
			[
				[ 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ],
				[ 'param' => 'page_type', 'operator' => '!=', 'value' => 'front_page' ],
			],
		],
	] );

	// ── Careers — Job Listings (ACF Pro repeater) ─────────────────────────────

	if ( class_exists( 'acf_field_repeater' ) ) {
		acf_add_local_field_group( [
			'key'    => 'group_careers',
			'title'  => 'Careers — Job Listings',
			'fields' => [
				[
					'key'          => 'field_job_listings',
					'label'        => 'Job Listings',
					'name'         => 'job_listings',
					'type'         => 'repeater',
					'min'          => 0,
					'layout'       => 'block',
					'button_label' => 'Add Job',
					'sub_fields'   => [
						[
							'key'   => 'field_job_title',
							'label' => 'Job Title',
							'name'  => 'job_title',
							'type'  => 'text',
						],
						[
							'key'   => 'field_job_department',
							'label' => 'Department',
							'name'  => 'job_department',
							'type'  => 'text',
						],
						[
							'key'   => 'field_job_location',
							'label' => 'Location',
							'name'  => 'job_location',
							'type'  => 'text',
						],
						[
							'key'     => 'field_job_type',
							'label'   => 'Employment Type',
							'name'    => 'job_type',
							'type'    => 'select',
							'choices' => [
								'Full-Time'  => 'Full-Time',
								'Part-Time'  => 'Part-Time',
								'Contract'   => 'Contract',
								'Internship' => 'Internship',
							],
						],
						[
							'key'   => 'field_job_description',
							'label' => 'Description',
							'name'  => 'job_description',
							'type'  => 'textarea',
							'rows'  => 4,
						],
						[
							'key'   => 'field_job_apply_url',
							'label' => 'Apply URL',
							'name'  => 'job_apply_url',
							'type'  => 'url',
						],
					],
				],
			],
			'location' => [
				[
					[ 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ],
					[ 'param' => 'page_type', 'operator' => '!=', 'value' => 'front_page' ],
				],
			],
		] );
	}
}
