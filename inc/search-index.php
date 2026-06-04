<?php
/**
 * Site search index — full page content for client-side search.
 */

function leap_get_search_index() {
	return [
		[
			'title'       => 'Home',
			'url'         => home_url( '/' ),
			'description' => 'Leap Distributors — the new standard in medical device distribution. Sharper reps, smarter platform, cleaner data across surgeons, hospitals, and manufacturers.',
			'keywords'    => 'home leap distributors new standard medical device distribution sharper reps smarter platform cleaner data surgeons hospitals manufacturers stride OR coverage south central United States',
			'type'        => 'Page',
		],
		[
			'title'       => 'Stride Platform',
			'url'         => home_url( '/platform/' ),
			'description' => 'Stride is Leap\'s own platform. Reps log every case in the OR as it happens, paperwork generates itself, and data sharpens with every case.',
			'keywords'    => 'Stride Leap platform technology custom tech runs every case OR case logging scrub sheets billing data analytics real-time rep dashboard commission quota live performance built to move patients before products engineered for intelligence ACL reconstruction case log insights spine posterior AI-assisted billing partner dashboards distributor network paperwork generates email Excel built by us used in every case three capabilities one platform',
			'type'        => 'Platform',
		],
		[
			'title'       => 'Surgeons',
			'url'         => home_url( '/surgeons/' ),
			'description' => 'Leap reps know your preferences, your procedures, and your room. Broader product access, patient-first advocacy, and trusted coverage for every case.',
			'keywords'    => 'surgeons your call our coverage OR coverage rep learn practice preferences patients earn the room partner thinks like partner patients before products trusted small things Trey leap rep spine orthopedics biologics soft tissue 10000 surgeries annually 750 surgeons supported 350 facilities GPO IDN 6am reply last-minute schedule surgeon choose leap',
			'type'        => 'Who We Serve',
		],
		[
			'title'       => 'Hospitals & Health Systems',
			'url'         => home_url( '/partnerships/hospitals/' ),
			'description' => 'One team covering every product line. Live case data, faster billing, single point of accountability for hospitals and health systems.',
			'keywords'    => 'hospitals health systems one team every product line fewer vendors cleaner records one contact supply chain accountable end to end live case data faster billing vendor consolidation surgeon preference GPO IDN single point accountability scrub sheets paperwork finance billing speed service line revenue volume',
			'type'        => 'Who We Serve',
		],
		[
			'title'       => 'Manufacturers',
			'url'         => home_url( '/partnerships/manufacturers/' ),
			'description' => 'Direct rep coverage in the south central US with national reach. Real field data on product movement straight from the OR.',
			'keywords'    => 'manufacturers direct coverage national reach south central distribution partner sales force 200 distributor partners Stride platform field visibility product movement OR real time quarterly reports launch distribution network contracting finance pays on time reps carry your line shortcut plug in',
			'type'        => 'Who We Serve',
		],
		[
			'title'       => 'Distributors & Independent Reps',
			'url'         => home_url( '/distributors/' ),
			'description' => 'Leap is built for reps who want better infrastructure, real back-office support, and a brand that puts more weight behind them.',
			'keywords'    => 'distributors independent reps move join network better infrastructure back office support brand leverage tech Stride auto-generated scrub sheets live performance dashboards real-time commission visibility contracting finance ops credentialing manufacturer relationships book GPO IDN vendor consolidation growing fast Better Together independents squeezed platform brand standards',
			'type'        => 'Who We Serve',
		],
		[
			'title'       => 'About Leap',
			'url'         => home_url( '/about/' ),
			'description' => 'Leap was built by people who spent years in the OR. Based in Dallas and Houston, we came from Leap Surgical and DUB Enterprises.',
			'keywords'    => 'about company team Dallas Houston Leap Surgical DUB Enterprises mission values founders partners Allen Mason CEO co-founder Jonathan Knickerbocker VP Spine Wes Lambard VP Sales Peyton Woodyard VP Orthopedics 75 years medical device distribution professional personable confident humble direct no-nonsense solution-focused authentic partnerships mission-driven better patient outcomes two hubs one mission 3151 Halifax Street Suite 160 Dallas TX 75219 200 distributor partners professional personable confident humble',
			'type'        => 'Company',
		],
		[
			'title'       => 'Careers',
			'url'         => home_url( '/careers/' ),
			'description' => 'Join the Leap team. Mission-driven, fast-moving, and building the future of healthcare distribution.',
			'keywords'    => 'careers jobs hiring join team mission driven healthcare distribution life at leap work matters patient bedside mission first ownership mentality growing fast Medical Device Sales Representative Dallas Supply Chain Coordinator Business Development Manager Remote National Territory send resume exceptional people hungry humble',
			'type'        => 'Company',
		],
		[
			'title'       => 'Orders',
			'url'         => home_url( '/orders/' ),
			'description' => 'Place and track your orders with Leap. Access order history, track shipments, and manage your account.',
			'keywords'    => 'orders order management place order track shipment order history account billing returns product questions client support 888-776-5553 portal delivery',
			'type'        => 'Operations',
		],
		[
			'title'       => 'Contact',
			'url'         => home_url( '/contact/' ),
			'description' => 'Get in touch with Leap Distributors. Dallas HQ, email info@leapdistributors.com, call +1 888-776-5553.',
			'keywords'    => 'contact get in touch lets talk partnership support learn more Dallas Texas 3151 Halifax Street Suite 160 75219 phone 888-776-5553 email info@leapdistributors.com surgeon distributor hospital manufacturer message form',
			'type'        => 'Company',
		],
		[
			'title'       => 'News & Insights',
			'url'         => home_url( '/news/' ),
			'description' => 'Industry insights, company news, and partnership announcements from the Leap Distributors team.',
			'keywords'    => 'news insights blog announcements updates press industry company partnership',
			'type'        => 'News',
		],
	];
}
