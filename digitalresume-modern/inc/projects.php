<?php
/**
 * Projects — wire the "Selected Work" page to the Portfolio custom post type.
 *
 * Each project is a `portfolio` post. A "Project Details" meta box adds the
 * fields the Selected Work design needs (layout, role/eyebrow, dates, tech tags,
 * link, flag, placeholder image, gallery). dhm_projects_render() outputs the
 * featured / grid / dark sections from the CPT; a one-time seed populates
 * placeholder projects so the page is never empty.
 *
 * @package DigitalResumeModern
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Layout choices for a project. */
function dhm_projects_layouts() {
	return array(
		'card'     => 'Card (grid)',
		'featured' => 'Featured (large, light)',
		'dark'     => 'Featured (dark band, with gallery)',
	);
}

/* ---- Meta box -------------------------------------------------------------- */

function dhm_projects_meta_box() {
	add_meta_box( 'dhm_project_details', 'Project Details', 'dhm_projects_meta_box_html', 'portfolio', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'dhm_projects_meta_box' );

function dhm_projects_meta_box_html( $post ) {
	wp_nonce_field( 'dhm_projects_save', 'dhm_projects_nonce' );
	$f = function ( $k ) use ( $post ) {
		return get_post_meta( $post->ID, $k, true );
	};
	$layout = $f( '_dhm_proj_layout' ) ? $f( '_dhm_proj_layout' ) : 'card';
	?>
	<style>
		.dhm-pf p { margin:.6em 0; }
		.dhm-pf label { display:block; font-weight:600; margin-bottom:3px; }
		.dhm-pf input[type=text], .dhm-pf input[type=url], .dhm-pf textarea, .dhm-pf select { width:100%; }
		.dhm-pf .desc { color:#646970; font-weight:400; }
	</style>
	<div class="dhm-pf">
		<p>
			<label for="dhm_proj_layout">Layout</label>
			<select name="_dhm_proj_layout" id="dhm_proj_layout">
				<?php foreach ( dhm_projects_layouts() as $k => $label ) : ?>
					<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $layout, $k ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<span class="desc">Where/how this project appears on the Projects page.</span>
		</p>
		<p>
			<label for="dhm_proj_eyebrow">Role / eyebrow</label>
			<input type="text" name="_dhm_proj_eyebrow" id="dhm_proj_eyebrow" value="<?php echo esc_attr( $f( '_dhm_proj_eyebrow' ) ); ?>" placeholder="Lead Engineer · ABCoA · 2017 – Present">
			<span class="desc">Small line above/below the title (role · company · dates). On the dark layout this is the kicker.</span>
		</p>
		<p>
			<label for="dhm_proj_date">Date badge <span class="desc">(card layout)</span></label>
			<input type="text" name="_dhm_proj_date" id="dhm_proj_date" value="<?php echo esc_attr( $f( '_dhm_proj_date' ) ); ?>" placeholder="2004 – 2014">
		</p>
		<p>
			<label for="dhm_proj_tags">Tech tags</label>
			<input type="text" name="_dhm_proj_tags" id="dhm_proj_tags" value="<?php echo esc_attr( $f( '_dhm_proj_tags' ) ); ?>" placeholder="C#, ASP.NET, T-SQL">
			<span class="desc">Comma-separated.</span>
		</p>
		<p>
			<label for="dhm_proj_url">External link <span class="desc">(featured / dark)</span></label>
			<input type="url" name="_dhm_proj_url" id="dhm_proj_url" value="<?php echo esc_attr( $f( '_dhm_proj_url' ) ); ?>" placeholder="https://example.com">
		</p>
		<p>
			<label for="dhm_proj_flag">Badge <span class="desc">(featured)</span></label>
			<input type="text" name="_dhm_proj_flag" id="dhm_proj_flag" value="<?php echo esc_attr( $f( '_dhm_proj_flag' ) ); ?>" placeholder="★ Flagship">
		</p>
		<p>
			<label for="dhm_proj_image">Placeholder image URL</label>
			<input type="url" name="_dhm_proj_image" id="dhm_proj_image" value="<?php echo esc_attr( $f( '_dhm_proj_image' ) ); ?>">
			<span class="desc">Used when no Featured Image is set. Set a Featured Image (right sidebar) to override.</span>
		</p>
		<p>
			<label for="dhm_proj_gallery">Gallery <span class="desc">(dark layout)</span></label>
			<textarea name="_dhm_proj_gallery" id="dhm_proj_gallery" rows="6" placeholder="https://…/image.webp | Caption"><?php echo esc_textarea( $f( '_dhm_proj_gallery' ) ); ?></textarea>
			<span class="desc">One image per line, optional caption after a vertical bar: <code>URL | Caption</code>.</span>
		</p>
		<p class="desc">The description comes from this project's Excerpt (or the editor content if no excerpt).</p>
	</div>
	<?php
}

function dhm_projects_save_meta( $post_id ) {
	if ( ! isset( $_POST['dhm_projects_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['dhm_projects_nonce'] ), 'dhm_projects_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$text = array( '_dhm_proj_eyebrow', '_dhm_proj_date', '_dhm_proj_tags', '_dhm_proj_flag' );
	foreach ( $text as $k ) {
		if ( isset( $_POST[ $k ] ) ) {
			update_post_meta( $post_id, $k, sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) );
		}
	}
	if ( isset( $_POST['_dhm_proj_layout'] ) ) {
		$layout = sanitize_key( wp_unslash( $_POST['_dhm_proj_layout'] ) );
		update_post_meta( $post_id, '_dhm_proj_layout', array_key_exists( $layout, dhm_projects_layouts() ) ? $layout : 'card' );
	}
	foreach ( array( '_dhm_proj_url', '_dhm_proj_image' ) as $k ) {
		if ( isset( $_POST[ $k ] ) ) {
			update_post_meta( $post_id, $k, esc_url_raw( wp_unslash( $_POST[ $k ] ) ) );
		}
	}
	if ( isset( $_POST['_dhm_proj_gallery'] ) ) {
		update_post_meta( $post_id, '_dhm_proj_gallery', sanitize_textarea_field( wp_unslash( $_POST['_dhm_proj_gallery'] ) ) );
	}
}
add_action( 'save_post_portfolio', 'dhm_projects_save_meta' );

/* ---- Query + resolve ------------------------------------------------------ */

/** Resolve all portfolio posts into render-ready arrays, ordered by menu order. */
/** Resolve a single portfolio post into a render-ready array (list + detail). */
function dhm_project_resolve( $p ) {
	$default_img = get_stylesheet_directory_uri() . '/assets/img/projects/project-1.jpg';

	$img = has_post_thumbnail( $p->ID )
		? get_the_post_thumbnail_url( $p->ID, 'large' )
		: ( get_post_meta( $p->ID, '_dhm_proj_image', true ) ? get_post_meta( $p->ID, '_dhm_proj_image', true ) : $default_img );

	$desc = has_excerpt( $p->ID ) ? get_the_excerpt( $p ) : wp_trim_words( wp_strip_all_tags( $p->post_content ), 40 );

	$tags = array_filter( array_map( 'trim', explode( ',', (string) get_post_meta( $p->ID, '_dhm_proj_tags', true ) ) ) );

	$gallery = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) get_post_meta( $p->ID, '_dhm_proj_gallery', true ) ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$parts     = array_map( 'trim', explode( '|', $line, 2 ) );
		$gallery[] = array(
			'url'     => $parts[0],
			'caption' => isset( $parts[1] ) ? $parts[1] : '',
		);
	}

	$layout = get_post_meta( $p->ID, '_dhm_proj_layout', true );
	return array(
		'id'        => $p->ID,
		'title'     => get_the_title( $p ),
		'permalink' => get_permalink( $p->ID ),
		'layout'    => array_key_exists( $layout, dhm_projects_layouts() ) ? $layout : 'card',
		'eyebrow'   => (string) get_post_meta( $p->ID, '_dhm_proj_eyebrow', true ),
		'date'      => (string) get_post_meta( $p->ID, '_dhm_proj_date', true ),
		'flag'      => (string) get_post_meta( $p->ID, '_dhm_proj_flag', true ),
		'url'       => (string) get_post_meta( $p->ID, '_dhm_proj_url', true ),
		'desc'      => $desc,
		'tags'      => $tags,
		'image'     => $img,
		'gallery'   => $gallery,
	);
}

function dhm_projects_get() {
	$q = new WP_Query(
		array(
			'post_type'      => 'portfolio',
			'posts_per_page' => 50,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);
	$out = array();
	foreach ( $q->posts as $p ) {
		$out[] = dhm_project_resolve( $p );
	}
	wp_reset_postdata();
	return $out;
}

/* ---- Render --------------------------------------------------------------- */

function dhm_projects_tag_html( $tags, $class ) {
	$out = '';
	foreach ( $tags as $t ) {
		$out .= '<span class="' . esc_attr( $class ) . '">' . esc_html( $t ) . '</span>';
	}
	return $out;
}

function dhm_projects_render() {
	$items = dhm_projects_get();
	if ( empty( $items ) ) {
		echo '<section class="pj-shell pj-section"><p class="pj-lead">Projects are managed in <strong>Portfolio</strong> (wp-admin). Add one to see it here.</p></section>';
		return;
	}

	$featured = array_values( array_filter( $items, function ( $i ) { return 'featured' === $i['layout']; } ) );
	$cards    = array_values( array_filter( $items, function ( $i ) { return 'card' === $i['layout']; } ) );
	$dark     = array_values( array_filter( $items, function ( $i ) { return 'dark' === $i['layout']; } ) );

	// Featured (light) blocks.
	foreach ( $featured as $p ) {
		?>
		<section class="pj-shell pj-section">
			<div class="pj-feature">
				<a class="pj-feature-media" href="<?php echo esc_url( $p['permalink'] ); ?>" aria-label="<?php echo esc_attr( $p['title'] ); ?>">
					<img src="<?php echo esc_url( $p['image'] ); ?>" alt="<?php echo esc_attr( $p['title'] ); ?>">
					<?php if ( $p['flag'] ) : ?><span class="pj-flag"><?php echo esc_html( $p['flag'] ); ?></span><?php endif; ?>
				</a>
				<div>
					<?php if ( $p['eyebrow'] ) : ?><div class="pj-eyebrow"><?php echo esc_html( $p['eyebrow'] ); ?></div><?php endif; ?>
					<h2 class="pj-h2"><a class="pj-h2-link" href="<?php echo esc_url( $p['permalink'] ); ?>"><?php echo esc_html( $p['title'] ); ?></a></h2>
					<?php if ( $p['desc'] ) : ?><p class="pj-desc"><?php echo esc_html( $p['desc'] ); ?></p><?php endif; ?>
					<?php if ( $p['tags'] ) : ?><div class="pj-tags"><?php echo dhm_projects_tag_html( $p['tags'], 'pj-tag' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php endif; ?>
					<p style="margin-top:1.4rem;"><a class="pj-dark-link" style="color:#3d8a66;" href="<?php echo esc_url( $p['permalink'] ); ?>">View project →</a></p>
				</div>
			</div>
		</section>
		<?php
	}

	// Card grid.
	if ( $cards ) {
		?>
		<section class="pj-shell pj-section">
			<div class="pj-grid">
				<?php foreach ( $cards as $p ) : ?>
					<a class="pj-card pj-card-link" href="<?php echo esc_url( $p['permalink'] ); ?>">
						<div class="pj-card-media"><img src="<?php echo esc_url( $p['image'] ); ?>" alt="<?php echo esc_attr( $p['title'] ); ?>"></div>
						<div class="pj-card-body">
							<div class="pj-card-head">
								<h3 class="pj-card-title"><?php echo esc_html( $p['title'] ); ?></h3>
								<?php if ( $p['date'] ) : ?><span class="pj-card-date"><?php echo esc_html( $p['date'] ); ?></span><?php endif; ?>
							</div>
							<?php if ( $p['eyebrow'] ) : ?><div class="pj-card-role"><?php echo esc_html( $p['eyebrow'] ); ?></div><?php endif; ?>
							<?php if ( $p['desc'] ) : ?><p class="pj-card-desc"><?php echo esc_html( $p['desc'] ); ?></p><?php endif; ?>
							<?php if ( $p['tags'] ) : ?><div class="pj-card-tags"><?php echo dhm_projects_tag_html( $p['tags'], 'pj-card-tag' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php endif; ?>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}

	// Dark featured blocks (with optional gallery).
	foreach ( $dark as $p ) {
		?>
		<section class="pj-dark">
			<div class="pj-dark-inner">
				<?php if ( $p['eyebrow'] ) : ?><div class="pj-dark-kicker"><span class="rule"></span><?php echo esc_html( $p['eyebrow'] ); ?></div><?php endif; ?>
				<div class="pj-dark-titlewrap">
					<h2 class="pj-dark-h2"><?php echo esc_html( $p['title'] ); ?></h2>
					<?php if ( $p['url'] ) : ?><a href="<?php echo esc_url( $p['url'] ); ?>" target="_blank" rel="noopener" class="pj-dark-link"><?php echo esc_html( preg_replace( '#^https?://#', '', $p['url'] ) ); ?> ↗</a><?php endif; ?>
				</div>
				<?php if ( $p['desc'] ) : ?><p class="pj-dark-lead"><?php echo esc_html( $p['desc'] ); ?></p><?php endif; ?>
				<?php if ( $p['gallery'] ) : ?>
					<div class="pj-gallery">
						<?php foreach ( $p['gallery'] as $g ) : ?>
							<figure>
								<div class="frame"><img src="<?php echo esc_url( $g['url'] ); ?>" alt="<?php echo esc_attr( $g['caption'] ? $g['caption'] : $p['title'] ); ?>" loading="lazy"></div>
								<?php if ( $g['caption'] ) : ?><figcaption><?php echo esc_html( $g['caption'] ); ?></figcaption><?php endif; ?>
							</figure>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php if ( $p['tags'] ) : ?><div class="pj-dark-tags"><?php echo dhm_projects_tag_html( $p['tags'], 'pj-dark-tag' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php endif; ?>
			</div>
		</section>
		<?php
	}
}

/* ---- Placeholder seeding (one-time) --------------------------------------- */

function dhm_projects_maybe_seed() {
	if ( get_option( 'dhm_projects_seeded' ) ) {
		return;
	}
	// Don't clobber an existing portfolio the user already built.
	$existing = get_posts( array( 'post_type' => 'portfolio', 'posts_per_page' => 1, 'fields' => 'ids', 'post_status' => 'any' ) );
	if ( ! empty( $existing ) ) {
		update_option( 'dhm_projects_seeded', 1 );
		return;
	}
	$img = get_stylesheet_directory_uri() . '/assets/img/projects';
	$seed = array(
		array(
			'title'   => 'Deal Pack Web',
			'excerpt' => 'A financial-management & loan-servicing platform for automotive dealerships and subprime finance companies — origination, payments, collections, accounting and CRM. Shipped inside a SOC 2 Type II and PCI DSS compliant environment serving a national customer base.',
			'order'   => 1,
			'meta'    => array(
				'_dhm_proj_layout'  => 'featured',
				'_dhm_proj_eyebrow' => 'Lead Engineer · ABCoA · 2017 – Present',
				'_dhm_proj_tags'    => 'C#, ASP.NET, T-SQL, Highcharts, SSRS',
				'_dhm_proj_flag'    => '★ Flagship',
				'_dhm_proj_image'   => $img . '/project-1.jpg',
			),
		),
		array(
			'title'   => 'Aircrew Training Systems',
			'excerpt' => 'Interactive Level-3 courseware and XML-driven MFD cockpit simulators for CV-22 Osprey, MV-22 and MH-60R programs — built against official military documentation with pilot SMEs.',
			'order'   => 2,
			'meta'    => array(
				'_dhm_proj_layout'  => 'card',
				'_dhm_proj_eyebrow' => 'Multimedia Engineer · L3 Communications',
				'_dhm_proj_date'    => '2004 – 2014',
				'_dhm_proj_tags'    => 'XML, JavaScript, Simulation',
				'_dhm_proj_image'   => $img . '/project-2.jpg',
			),
		),
		array(
			'title'   => 'Florida Blue · Member Tools',
			'excerpt' => 'WCAG 2.0 AA accessibility upgrades across benefit-management tools, plus a proxy integration bridging FloridaBlue.com with HealthCare.gov for individual-market exchange enrollment.',
			'order'   => 3,
			'meta'    => array(
				'_dhm_proj_layout'  => 'card',
				'_dhm_proj_eyebrow' => 'UI Developer · Health Insurance',
				'_dhm_proj_date'    => '2017',
				'_dhm_proj_tags'    => 'JavaScript, WCAG 2.0 AA, Integration',
				'_dhm_proj_image'   => $img . '/project-3.jpg',
			),
		),
		array(
			'title'   => 'Enfusion · Real-time Analytics',
			'excerpt' => 'A real-time video-analytics application interface built with React.js and Material-UI — fast, data-dense dashboards designed for at-a-glance decision making.',
			'order'   => 4,
			'meta'    => array(
				'_dhm_proj_layout'  => 'card',
				'_dhm_proj_eyebrow' => 'UI Developer · OSI',
				'_dhm_proj_date'    => '2015 – 2017',
				'_dhm_proj_tags'    => 'React.js, Material-UI',
				'_dhm_proj_image'   => $img . '/project-4.jpg',
			),
		),
		array(
			'title'   => 'cyclCRM',
			'excerpt' => 'End-to-end interface design and frontend build for a customer-relationship platform — engineered for clarity and speed in the hands of daily operators.',
			'order'   => 5,
			'meta'    => array(
				'_dhm_proj_layout'  => 'card',
				'_dhm_proj_eyebrow' => 'Frontend & UI/UX · ABCoA',
				'_dhm_proj_date'    => '2017 – 2019',
				'_dhm_proj_tags'    => 'JavaScript, UI/UX',
				'_dhm_proj_image'   => $img . '/cyclcrm.jpg',
			),
		),
		array(
			'title'   => 'Toon & Tails',
			'excerpt' => 'A production AI SaaS I built and launched solo — it turns a pet photo into a custom cartoon portrait in about a minute, then prints it on mugs, canvas, ornaments and more. Loved by 2,000+ pet owners.',
			'order'   => 6,
			'meta'    => array(
				'_dhm_proj_layout'  => 'dark',
				'_dhm_proj_eyebrow' => 'Featured · Founder & Engineer · Public Product',
				'_dhm_proj_url'     => 'https://toonandtails.com',
				'_dhm_proj_tags'    => 'Next.js 15, React 19, OpenAI gpt-image-2, Firebase, Stripe, Printful',
				'_dhm_proj_gallery' => implode(
					"\n",
					array(
						'https://toonandtails.com/images/landing-page/1-dogs-cats-and-every-pet-in-between/previews/dog-golden-retreiver-preview.webp | Golden Retriever · 3D',
						'https://toonandtails.com/images/landing-page/1-dogs-cats-and-every-pet-in-between/previews/french-bulldog-preview.webp | French Bulldog',
						'https://toonandtails.com/images/landing-page/1-dogs-cats-and-every-pet-in-between/previews/cat-short-haired-preview.webp | Black Cat · Cute',
						'https://toonandtails.com/images/landing-page/1-dogs-cats-and-every-pet-in-between/previews/scarlet-macaw-preview.webp | Scarlet Macaw · 3D',
						'https://toonandtails.com/images/landing-page/1-dogs-cats-and-every-pet-in-between/previews/axolotl-preview.webp | Axolotl · Watercolor',
						'https://toonandtails.com/images/landing-page/1-dogs-cats-and-every-pet-in-between/previews/lop-rabbit-preview.webp | Lop Rabbit · 2D',
					)
				),
			),
		),
	);

	foreach ( $seed as $s ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'portfolio',
				'post_status'  => 'publish',
				'post_title'   => $s['title'],
				'post_excerpt' => $s['excerpt'],
				'menu_order'   => $s['order'],
			)
		);
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			foreach ( $s['meta'] as $k => $v ) {
				update_post_meta( $post_id, $k, $v );
			}
		}
	}
	update_option( 'dhm_projects_seeded', 1 );
}
add_action( 'admin_init', 'dhm_projects_maybe_seed' );
