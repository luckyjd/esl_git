<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class LP_Commission_Withdrawal_Method_SKrill {

    public static function getForm( $total, $min, $currency ) {
        ob_start();
        ?>
        <h1>SKrill Withdrawal</h1>
        <p>Chua co cai veo gi ca ^^</p>
        <?php
        $html = ob_get_contents();
        ob_get_clean();
        return $html;
    }

}
