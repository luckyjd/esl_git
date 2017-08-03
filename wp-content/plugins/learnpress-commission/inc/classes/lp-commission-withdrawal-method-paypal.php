<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
class LP_Commission_Withdrawal_Method_Paypal {

    public static function getForm( $total, $min, $currency ) {
        ob_start();
        ?>
        <h3><?php echo __( 'Paypal Withdrawal', 'learnpress' ); ?></h3>
        <form action="" method="post">
            <label for="lp_withdrawals_secret_code"><?php _e( 'Enter your password', 'learnpress' ); ?></label>
            <input type="password" name="lp_withdrawals_secret_code" id="lp_withdrawals_secret_code" required="required"/>
            <label for="lp_withdrawals_email"><?php _e( 'Paypal email account', 'learnpress' ); ?></label>
            <input type="text" name="lp_withdrawals_email" id="lp_withdrawals_email" required="required"/>
            <label for="lp_withdrawals"><?php _e( 'Amount', 'learnpress' ); ?> (<?php echo $currency; ?>)</label>
            <input type="number" step="any" max="<?php echo esc_attr( $total ); ?>" min="<?php echo esc_attr( $min ); ?>" name="lp_withdrawals" id="lp_withdrawals" data-all="<?php echo esc_attr( $total ); ?>" required="required"/>
            <div>
                <input type="checkbox" value="Request all" name="lp_all" id="lp_all">
                <label for="lp_all"><?php _e( 'Request all', 'learnpress' ); ?></label>
            </div>
        <?php LP_Request_Withdrawal::nonce(); ?>
            <button class="submit"><?php _e( 'Submit request', 'learnpress' ); ?></button>
            <input type="hidden" name="lp_payment_method" value="paypal"/>
        </form>
        <?php
        $html = ob_get_contents();
        ob_get_clean();
        return $html;
    }

}
