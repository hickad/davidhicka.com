<?php
/**
 * The footer for the modern editorial DigitalResume theme.
 *
 * @package DigitalResume
 */
?>

<footer class="dh-footer">
	&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> &middot; Senior Software Engineer &middot; Ponte Vedra, FL
</footer>

<?php
// Preview-only audience switcher. DELETE this block for production, or gate it
// behind `if ( current_user_can('manage_options') )` to keep it admin-only.
if ( apply_filters( 'digitalresume_show_audience_switch', current_user_can( 'manage_options' ) ) ) :
	$current = digitalresume_audience();
	$opts = array( 'finance' => 'Finance', 'defense' => 'Defense', 'healthcare' => 'Healthcare' );
	?>
	<div class="dh-aud-switch">
		<?php foreach ( $opts as $key => $label ) : ?>
			<a href="<?php echo esc_url( add_query_arg( 'for', $key ) ); ?>"
			   style="text-decoration:none;">
				<button class="<?php echo $current === $key ? 'active' : ''; ?>"><?php echo esc_html( $label ); ?></button>
			</a>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
