<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package thim
 */
if ( !is_active_sidebar( 'sidebar' ) ) {
	return;
}
$theme_options_data = get_theme_mods();
$sticky_sidebar     = !empty( $theme_options_data['thim_sticky_sidebar'] ) ? ' sticky-sidebar' : '';
?>

<div id="sidebar" class="widget-area col-sm-3<?php echo esc_attr( $sticky_sidebar ); ?>" role="complementary">
	<?php if ( !dynamic_sidebar( 'sidebar' ) ) :
		dynamic_sidebar( 'sidebar' );
	endif; // end sidebar widget area ?>
</div><!-- #secondary -->
