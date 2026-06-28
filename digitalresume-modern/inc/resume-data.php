<?php
/**
 * Résumé data model — the single source of truth.
 *
 * All three outputs (website sections, generated PDF, generated Word doc) read
 * from the `dhm_resume_data` option through dhm_resume_get(), so they can never
 * drift. The structured wp-admin form (inc/resume-admin.php) is the only writer.
 *
 * Schema, per audience key (finance|defense|healthcare|general):
 *   name        string
 *   title_line  string   e.g. "Senior Software Engineer · Financial Software · Full-Stack / Frontend"
 *   location    string
 *   contact     string[] one line per contact item (email, linkedin, website)
 *   clearance   string   optional single line (defense uses it; '' hides it)
 *   summary     string   paragraph
 *   experience  array[]  { role, company, dates, location, bullets[] }
 *   projects    array[]  { role, company, dates, location, bullets[] }
 *   skills      array[]  { category, items }
 *   education   string   single line
 *
 * @package DigitalResumeModern
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Bump when the seed content changes and you want it pushed to the live option.
 * dhm_resume_maybe_reseed() refreshes dhm_resume_data when the stored version is
 * older. v2 = ATS pass (keywords, expanded roles, polished verbs). v3 = phone.
 * v4 = populate the general variant (cross-industry, mirrors LinkedIn).
 */
if ( ! defined( 'DHM_RESUME_SEED_VERSION' ) ) {
	define( 'DHM_RESUME_SEED_VERSION', 4 );
}

/** Audiences this site supports (kept in sync with digitalresume_audience()). */
function dhm_resume_audiences() {
	return array( 'finance', 'defense', 'healthcare', 'general' );
}

/** An empty résumé record (used for "general" until it's filled in). */
function dhm_resume_blank() {
	return array(
		'name'       => '',
		'title_line' => '',
		'location'   => '',
		'contact'    => array(),
		'clearance'  => '',
		'summary'    => '',
		'keywords'   => '',
		'experience' => array(),
		'projects'   => array(),
		'skills'     => array(),
		'education'  => '',
	);
}

/**
 * Normalize one résumé record to the full schema (fills missing keys, coerces
 * types). Keeps reads defensive regardless of what's stored.
 */
function dhm_resume_normalize( $r ) {
	$b = dhm_resume_blank();
	if ( ! is_array( $r ) ) {
		return $b;
	}
	$out               = $b;
	$out['name']       = isset( $r['name'] ) ? (string) $r['name'] : '';
	$out['title_line'] = isset( $r['title_line'] ) ? (string) $r['title_line'] : '';
	$out['location']   = isset( $r['location'] ) ? (string) $r['location'] : '';
	$out['clearance']  = isset( $r['clearance'] ) ? (string) $r['clearance'] : '';
	$out['summary']    = isset( $r['summary'] ) ? (string) $r['summary'] : '';
	$out['keywords']   = isset( $r['keywords'] ) ? (string) $r['keywords'] : '';
	$out['education']  = isset( $r['education'] ) ? (string) $r['education'] : '';

	$out['contact'] = array();
	if ( ! empty( $r['contact'] ) && is_array( $r['contact'] ) ) {
		foreach ( $r['contact'] as $c ) {
			$c = trim( (string) $c );
			if ( '' !== $c ) {
				$out['contact'][] = $c;
			}
		}
	}

	foreach ( array( 'experience', 'projects' ) as $sect ) {
		$out[ $sect ] = array();
		if ( ! empty( $r[ $sect ] ) && is_array( $r[ $sect ] ) ) {
			foreach ( $r[ $sect ] as $job ) {
				if ( ! is_array( $job ) ) {
					continue;
				}
				$bullets = array();
				if ( ! empty( $job['bullets'] ) && is_array( $job['bullets'] ) ) {
					foreach ( $job['bullets'] as $bl ) {
						$bl = trim( (string) $bl );
						if ( '' !== $bl ) {
							$bullets[] = $bl;
						}
					}
				}
				$out[ $sect ][] = array(
					'role'     => isset( $job['role'] ) ? (string) $job['role'] : '',
					'company'  => isset( $job['company'] ) ? (string) $job['company'] : '',
					'dates'    => isset( $job['dates'] ) ? (string) $job['dates'] : '',
					'location' => isset( $job['location'] ) ? (string) $job['location'] : '',
					'bullets'  => $bullets,
				);
			}
		}
	}

	$out['skills'] = array();
	if ( ! empty( $r['skills'] ) && is_array( $r['skills'] ) ) {
		foreach ( $r['skills'] as $grp ) {
			if ( ! is_array( $grp ) ) {
				continue;
			}
			$out['skills'][] = array(
				'category' => isset( $grp['category'] ) ? (string) $grp['category'] : '',
				'items'    => isset( $grp['items'] ) ? (string) $grp['items'] : '',
			);
		}
	}

	return $out;
}

/** Whether a résumé record has enough content to render/offer downloads. */
function dhm_resume_has_content( $r ) {
	$r = dhm_resume_normalize( $r );
	return ( '' !== $r['summary'] ) || ! empty( $r['experience'] ) || ! empty( $r['skills'] );
}

/** Full data set (all audiences), normalized. Seeds on first read. */
function dhm_resume_all() {
	$data = get_option( 'dhm_resume_data', null );
	if ( null === $data || ! is_array( $data ) ) {
		$data = dhm_resume_seed();
		add_option( 'dhm_resume_data', $data, '', false );
	}
	$out = array();
	foreach ( dhm_resume_audiences() as $aud ) {
		$out[ $aud ] = dhm_resume_normalize( isset( $data[ $aud ] ) ? $data[ $aud ] : array() );
	}
	return $out;
}

/** One audience's résumé, normalized. */
function dhm_resume_get( $aud ) {
	$aud = in_array( $aud, dhm_resume_audiences(), true ) ? $aud : 'finance';
	$all = dhm_resume_all();
	return isset( $all[ $aud ] ) ? $all[ $aud ] : dhm_resume_blank();
}

/**
 * Persist one audience's record (already-sanitized array from the admin form).
 */
function dhm_resume_save( $aud, $record ) {
	if ( ! in_array( $aud, dhm_resume_audiences(), true ) ) {
		return false;
	}
	$data         = get_option( 'dhm_resume_data', array() );
	$data         = is_array( $data ) ? $data : array();
	$data[ $aud ] = dhm_resume_normalize( $record );
	return update_option( 'dhm_resume_data', $data );
}

/**
 * Refresh stored content from the seed when the seed version advances.
 *
 * Runs once per version bump. It overwrites dhm_resume_data with the current
 * seed — intended for pushing curated seed updates (like this ATS pass) to the
 * live site. Only audiences present in the seed are replaced.
 */
function dhm_resume_maybe_reseed() {
	$stored = (int) get_option( 'dhm_resume_seed_version', 0 );
	if ( $stored >= DHM_RESUME_SEED_VERSION ) {
		return;
	}
	$data = get_option( 'dhm_resume_data', array() );
	$data = is_array( $data ) ? $data : array();
	foreach ( dhm_resume_seed() as $aud => $record ) {
		$data[ $aud ] = $record;
	}
	update_option( 'dhm_resume_data', $data );
	update_option( 'dhm_resume_seed_version', DHM_RESUME_SEED_VERSION );
}

/* -------------------------------------------------------------------------
 * Seed — the current finance / defense / healthcare résumés, transcribed from
 * the hand-designed .docx files so the form ships populated with real content.
 * "general" starts blank.
 * ---------------------------------------------------------------------- */
function dhm_resume_seed() {
	$contact = array(
		'904-703-4413',
		'hickad@gmail.com',
		'linkedin.com/in/davidhicka',
		'davidhicka.com',
	);
	$skills = array(
		array( 'category' => 'Languages & Frameworks', 'items' => 'JavaScript (ES2024), TypeScript, React 19, Next.js 15, Node.js, C#, ASP.NET, PHP, T-SQL, PowerShell' ),
		array( 'category' => 'Frontend', 'items' => 'Tailwind CSS, shadcn/ui, CSS Modules, Highcharts, Material-UI, WCAG Accessibility' ),
		array( 'category' => 'Backend & Cloud', 'items' => 'Firebase, Google Cloud Run, Vercel, Azure DevOps, Next.js API Routes' ),
		array( 'category' => 'AI & LLM', 'items' => 'OpenAI API (gpt-image-2, Whisper), Claude, GitHub Copilot, Cursor, Codex, Ollama (DeepSeek, Qwen-Coder), Midjourney' ),
		array( 'category' => 'Databases', 'items' => 'SQL Server, Firestore, T-SQL, SSRS' ),
		array( 'category' => 'Tools', 'items' => 'ffmpeg, Playwright, Stripe API, Printful API, Sharp, Git, Adobe Creative Cloud' ),
	);
	$education = 'B.A.Sc., Information Technology Management · Florida State College at Jacksonville';

	$seed = array();

	/* ---- FINANCE ---- */
	$seed['finance'] = array(
		'name'       => 'David Hicka',
		'title_line' => 'Senior Software Engineer · Financial Software · Full-Stack / Frontend',
		'location'   => 'Ponte Vedra, FL',
		'contact'    => $contact,
		'clearance'  => '',
		'summary'    => 'Senior software engineer with 7+ years building financial management software in a SOC 2 Type II and PCI DSS compliant environment. Deal Pack Web — the platform I engineer — handles loan origination, subprime finance, payment processing, collections, and dealership accounting for a national customer base. Strong background in T-SQL, financial reporting (SSRS), data visualization, and secure full-stack web development. Active practitioner of AI-assisted development using Claude, GitHub Copilot, Cursor, and local LLMs.',
		'keywords'   => 'Financial Software Engineering, SOC 2 Type II, PCI DSS, Loan Origination, Loan Servicing, Payment Processing, Collections, Regulatory Compliance, Secure Full-Stack Development, T-SQL, SSRS, C#, ASP.NET, JavaScript, TypeScript, React, Next.js, Data Visualization, Financial Reporting, AI-Assisted Development',
		'experience' => array(
			array(
				'role'     => 'Senior Software Engineer',
				'company'  => 'ABCoA — Deal Pack',
				'dates'    => 'Dec 2025 – Present',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Led engineering of Deal Pack Web, a SOC 2 Type II and PCI DSS compliant financial management and loan-servicing platform serving a national base of automotive dealerships and subprime finance companies',
					'Delivered features across the full financial lifecycle: loan origination, payment processing, collections, accounting, leasing, CRM, and service/parts management',
					'Built AI-assisted internal developer tooling that reduced environment-conflict errors and streamlined database migration workflows across the development team',
				),
			),
			array(
				'role'     => 'Software Engineer',
				'company'  => 'ABCoA — Deal Pack',
				'dates'    => 'Nov 2019 – Nov 2025',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Developed 100+ custom T-SQL stored procedures and SSRS reports for dealership financial reporting, collections tracking, and compliance documentation',
					'Engineered interactive financial analytics dashboards using JavaScript and Highcharts — real-time visualization of sales volume, payment performance, and portfolio health',
					'Built custom ASP.NET controls adopted across the team; maintained WCAG accessibility compliance on all UI components',
				),
			),
			array(
				'role'     => 'Frontend Developer',
				'company'  => 'ABCoA — Deal Pack',
				'dates'    => 'Dec 2017 – Nov 2019',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Led UI/UX design and development for Deal Pack Web, Dealer Sales Tools (DST), and cyclCRM',
					'Built custom WordPress themes in PHP for company marketing sites',
				),
			),
			array(
				'role'     => 'UI Developer',
				'company'  => 'Florida Blue',
				'dates'    => 'Apr 2017 – Sep 2017',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Upgraded legacy health insurance applications to WCAG accessibility compliance; contributed to a HealthCare.gov integration requiring strict data-handling standards',
				),
			),
			array(
				'role'     => 'Application Developer',
				'company'  => 'Organizational Strategies, Inc.',
				'dates'    => 'May 2015 – Apr 2017',
				'location' => '',
				'bullets'  => array(
					'Developed C#/ASP.NET features for DesignAsBuilt.com; designed and built the UI for Enfusion, a real-time video analytics application built with React.js and Material-UI',
				),
			),
			array(
				'role'     => 'Multimedia Designer & Developer',
				'company'  => 'L3 Technologies',
				'dates'    => 'Jan 2004 – Oct 2014',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Built interactive training courseware and XML-driven simulation software for U.S. military aircrew programs; developed a reusable component library adopted across the team',
				),
			),
			array(
				'role'     => 'Visual Information Specialist',
				'company'  => 'United States Air Force',
				'dates'    => 'Jul 1998 – Jul 2003',
				'location' => 'Vandenberg AFB, CA',
				'bullets'  => array(
					'Produced web, print, and multimedia content supporting base communications; held a Secret security clearance throughout service',
				),
			),
		),
		'projects'   => array(
			array(
				'role'     => 'Founder & Engineer',
				'company'  => 'Toon & Tails (toonandtails.com)',
				'dates'    => '2025 – Present',
				'location' => '',
				'bullets'  => array(
					'Built and launched a production SaaS with Stripe payment processing, live Printful fulfillment integration, and OpenAI image generation — a full e-commerce transaction lifecycle, solo-built on Next.js 15 and Firebase',
				),
			),
		),
		'skills'     => $skills,
		'education'  => $education,
	);

	/* ---- DEFENSE ---- */
	$seed['defense'] = array(
		'name'       => 'David Hicka',
		'title_line' => 'Senior Software Engineer · Defense & Government · Full-Stack / Frontend',
		'location'   => 'Ponte Vedra, FL',
		'contact'    => $contact,
		'clearance'  => 'Security Clearance: Secret (held continuously during USAF service and L3 employment — eligible for reinstatement)',
		'summary'    => 'Senior software engineer and U.S. Air Force veteran with 10 years of defense contractor experience building interactive training systems for CV-22 Osprey, MV-22, and MH-60R programs at L3 Communications. Previously held Secret clearance throughout military service and contractor tenure. Now a full-stack engineer with 7+ years in compliance-driven enterprise software (SOC 2 / PCI DSS) and hands-on experience building AI-powered products using modern web technologies. Active practitioner of AI-assisted development using Claude, GitHub Copilot, Cursor, and local LLMs.',
		'keywords'   => 'Defense Software Engineering, Secret Security Clearance, U.S. Air Force Veteran, Interactive Training Systems, Simulation Software, Mission-Critical Software, SOC 2 Type II, PCI DSS, Secure Full-Stack Development, C#, ASP.NET, JavaScript, TypeScript, React, Next.js, AI Integration, Government Contracting',
		'experience' => array(
			array(
				'role'     => 'Senior Software Engineer',
				'company'  => 'ABCoA — Deal Pack',
				'dates'    => 'Dec 2025 – Present',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Led engineering of Deal Pack Web, a SOC 2 Type II and PCI DSS compliant financial management platform covering loan origination, payment processing, collections, accounting, and customer management',
					'Built AI-assisted internal developer tooling: DPW Local Environment Manager (tracks 6 parallel test environments across app, database, and report-server layers) and Database Conversion Studio (batch-converts customer databases with queue management and activity logging)',
				),
			),
			array(
				'role'     => 'Software Engineer',
				'company'  => 'ABCoA — Deal Pack',
				'dates'    => 'Nov 2019 – Nov 2025',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Engineered interactive sales analytics dashboards using JavaScript and Highcharts for real-time dealership performance visualization',
					'Developed 100+ custom T-SQL stored procedures and SSRS reports for dealership financial reporting and compliance',
					'Designed and built custom ASP.NET controls adopted across the development team, expanding the shared component library',
					'Ensured WCAG accessibility compliance across all UI components',
				),
			),
			array(
				'role'     => 'Frontend Developer',
				'company'  => 'ABCoA — Deal Pack',
				'dates'    => 'Dec 2017 – Nov 2019',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Led UI/UX design and development for Deal Pack Web, Dealer Sales Tools (DST), and cyclCRM',
					'Built custom WordPress themes in PHP for company marketing sites',
				),
			),
			array(
				'role'     => 'UI Developer',
				'company'  => 'Florida Blue',
				'dates'    => 'Apr 2017 – Sep 2017',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Upgraded legacy applications to WCAG accessibility compliance; contributed to a HealthCare.gov integration with strict data-handling requirements',
				),
			),
			array(
				'role'     => 'Application Developer',
				'company'  => 'Organizational Strategies, Inc.',
				'dates'    => 'May 2015 – Apr 2017',
				'location' => '',
				'bullets'  => array(
					'C#/ASP.NET development for DesignAsBuilt.com; UI design and development for Enfusion, a real-time analytics app built with React.js and Material-UI',
				),
			),
			array(
				'role'     => 'Multimedia Designer & Developer',
				'company'  => 'L3 Communications',
				'dates'    => 'Jan 2004 – Oct 2014',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Built interactive Level 3 training courseware for CV-22 Osprey (AFSOC), MV-22 (USMC), and MH-60R (USN/USMC) programs — covering aircraft systems, crew procedures, and mission equipment',
					'Developed reusable Adobe Flash / JavaScript component library used by the broader development team — including video players, interactive graphic templates, and XML-driven MFD (Multifunctional Display) simulators that loaded configuration data to emulate cockpit instruments',
					'Built Flash extensions used by other graphic artists on the team to add Level 3 interactivity to courseware without custom scripting',
					'Collaborated directly with pilot and aircrew SMEs referencing official Air Force, Marine Corps, and Navy documentation to ensure technical accuracy',
				),
			),
			array(
				'role'     => 'Visual Information Specialist',
				'company'  => 'United States Air Force',
				'dates'    => 'Jul 1998 – Jul 2003',
				'location' => 'Vandenberg AFB, CA',
				'bullets'  => array(
					'Produced web, print, and multimedia content in support of base public affairs and mission communications; mentored junior enlisted',
					'Held Secret security clearance throughout service',
				),
			),
		),
		'projects'   => array(
			array(
				'role'     => 'Founder & Engineer',
				'company'  => 'Toon & Tails (toonandtails.com)',
				'dates'    => '2025 – Present',
				'location' => '',
				'bullets'  => array(
					'Built and launched a production AI SaaS using Next.js 15, React 19, OpenAI gpt-image-2, Firebase, Stripe, and Printful — demonstrates current full-stack capability and AI integration skills',
				),
			),
		),
		'skills'     => $skills,
		'education'  => $education,
	);

	/* ---- HEALTHCARE ---- */
	$seed['healthcare'] = array(
		'name'       => 'David Hicka',
		'title_line' => 'Senior Software Engineer · Healthcare & Compliance-Focused · Full-Stack / Frontend',
		'location'   => 'Ponte Vedra, FL',
		'contact'    => $contact,
		'clearance'  => '',
		'summary'    => 'Senior software engineer with 20+ years of experience building secure, compliance-driven web applications. Direct healthcare industry experience at Florida Blue, including WCAG accessibility upgrades and a HealthCare.gov integration. Deep background in financial software with SOC 2 Type II and PCI DSS compliance at ABCoA, reflecting the same regulatory discipline required in healthcare environments. Proven ability to ship full-stack products independently, and an active practitioner of AI-assisted development using Claude, GitHub Copilot, Cursor, and local LLMs.',
		'keywords'   => 'Healthcare Software Engineering, HIPAA, WCAG 2.0 AA Accessibility, HealthCare.gov Integration, Compliance-Driven Development, Data Privacy, SOC 2 Type II, PCI DSS, Secure Full-Stack Development, C#, ASP.NET, JavaScript, TypeScript, React, Next.js, Regulatory Compliance, AI-Assisted Development',
		'experience' => array(
			array(
				'role'     => 'Senior Software Engineer',
				'company'  => 'ABCoA — Deal Pack',
				'dates'    => 'Dec 2025 – Present',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Led engineering of Deal Pack Web, a SOC 2 Type II and PCI DSS compliant financial management platform covering loan origination, payment processing, collections, accounting, and customer management',
					'Built AI-assisted internal developer tooling: DPW Local Environment Manager (tracks 6 parallel test environments across app, database, and report-server layers) and Database Conversion Studio (batch-converts customer databases with queue management and activity logging)',
					'Maintained SOC 2 Type II and PCI DSS compliance standards across all feature development — regulatory discipline directly applicable to HIPAA-governed environments',
				),
			),
			array(
				'role'     => 'Software Engineer',
				'company'  => 'ABCoA — Deal Pack',
				'dates'    => 'Nov 2019 – Nov 2025',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Engineered interactive sales analytics dashboards using JavaScript and Highcharts for real-time dealership performance visualization',
					'Developed 100+ custom T-SQL stored procedures and SSRS reports for dealership financial reporting and compliance',
					'Designed and built custom ASP.NET controls adopted across the development team, expanding the shared component library',
					'Ensured WCAG accessibility compliance across all UI components',
				),
			),
			array(
				'role'     => 'Frontend Developer',
				'company'  => 'ABCoA — Deal Pack',
				'dates'    => 'Dec 2017 – Nov 2019',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Led UI/UX design and development for Deal Pack Web, Dealer Sales Tools (DST), and cyclCRM',
					'Built custom WordPress themes in PHP for company marketing sites',
				),
			),
			array(
				'role'     => 'UI Developer',
				'company'  => 'Florida Blue',
				'dates'    => 'Apr 2017 – Sep 2017',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Upgraded legacy health insurance applications to WCAG 2.0 AA accessibility compliance, improving access for members with disabilities across core benefit management tools',
					'Contributed to a proxy integration bridging FloridaBlue.com with HealthCare.gov to streamline individual market exchange enrollment — required working within strict regulatory and data-handling constraints',
				),
			),
			array(
				'role'     => 'Application Developer',
				'company'  => 'Organizational Strategies, Inc.',
				'dates'    => 'May 2015 – Apr 2017',
				'location' => '',
				'bullets'  => array(
					'Developed C#/ASP.NET features for DesignAsBuilt.com; designed and built the UI for Enfusion, a real-time video analytics application built with React.js and Material-UI',
				),
			),
			array(
				'role'     => 'Multimedia Designer & Developer',
				'company'  => 'L3 Technologies',
				'dates'    => 'Jan 2004 – Oct 2014',
				'location' => 'Jacksonville, FL',
				'bullets'  => array(
					'Built interactive training courseware and XML-driven simulation software for U.S. military aircrew programs; developed a reusable component library adopted across the team',
				),
			),
			array(
				'role'     => 'Visual Information Specialist',
				'company'  => 'United States Air Force',
				'dates'    => 'Jul 1998 – Jul 2003',
				'location' => 'Vandenberg AFB, CA',
				'bullets'  => array(
					'Produced web, print, and multimedia content supporting base communications; held a Secret security clearance throughout service',
				),
			),
		),
		'projects'   => array(
			array(
				'role'     => 'Founder & Engineer',
				'company'  => 'Toon & Tails (toonandtails.com)',
				'dates'    => '2025 – Present',
				'location' => '',
				'bullets'  => array(
					'Built and launched a production AI SaaS using Next.js 15, React 19, OpenAI gpt-image-2, Firebase, Stripe, and Printful — demonstrates the ability to architect and ship secure, authenticated, full-stack consumer applications independently',
				),
			),
		),
		'skills'     => $skills,
		'education'  => $education,
	);

	/* ---- GENERAL ---- (cross-industry; mirrors the single public LinkedIn
	 * profile). Reuses the finance work history/skills with broadened framing. */
	$seed['general']               = $seed['finance'];
	$seed['general']['title_line'] = 'Senior Software Engineer · Full-Stack / Frontend · AI-Assisted Development';
	$seed['general']['summary']    = 'Senior software engineer with 20+ years building secure, reliable software across finance, defense, and healthcare. I ship full-stack products end to end — from SOC 2 Type II and PCI DSS compliant enterprise platforms to AI-powered consumer SaaS — with deep experience in C#/.NET, modern JavaScript/TypeScript (React, Next.js), and data. Active practitioner of AI-assisted development using Claude, GitHub Copilot, Cursor, and local LLMs.';
	$seed['general']['keywords']   = 'Software Engineering, Full-Stack Development, Frontend Development, React, Next.js, TypeScript, JavaScript, C#, ASP.NET, Node.js, T-SQL, SOC 2 Type II, PCI DSS, WCAG Accessibility, Secure Software Development, AI-Assisted Development, Stripe, Firebase, Cloud';

	return $seed;
}
