<?php
/**
 * Display settings for checkout
 *
 * @author  ThimPress
 * @package LearnPress/Admin/Views
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$settings = LP()->settings;

?>
<h1><?php esc_html_e( 'Paypal Withdrawal Method Settings', 'learnpress' ); ?></h1>

<table class="form-table">
	<tbody>
	<?php do_action( 'learn_press_before_' . $this->id . '_' . $this->section['id'] . '_settings_fields', $this ); ?>
	<?php foreach ( $this->get_withdrawal_paypal() as $field ) { ?>
		<?php $this->output_field( $field ); ?>
	<?php } ?>
	</tbody>

</table>