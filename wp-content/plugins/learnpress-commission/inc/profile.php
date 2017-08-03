<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * @param $user
 */
function lp_commission_show_profile( $user ) {
    $user_data = $user->data;
    $user_id = $user_data->ID;
    ?>
    <h3><?php esc_html_e( 'Course Commission', 'learnpress' ); ?></h3>
    <table class="form-table lp_commission">
        <tbody>
            <tr>
                <th><?php _e( 'Your commission', 'learnpress' ); ?></th>
                <td>
                    <span class="count"><?php echo esc_html( lp_commission_get_total_commission( $user_id ) ); ?></span>
                    <span class="unit"><?php echo learn_press_get_currency_symbol(); ?></span>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}

add_action( 'show_user_profile', 'lp_commission_show_profile' );
add_action( 'edit_user_profile', 'lp_commission_show_profile' );

/**
 * @param $user_id
 *
 * @return int
 */
function lp_commission_get_total_commission( $user_id ) {
    $value = get_user_meta( $user_id, 'lp_commission_total', true );
    if ( empty( $value ) ) {
        $value = 0;
    }

    $value = floatval( $value );

    return $value;
}

/**
 * @param $user_id
 * @param $value
 *
 * @return bool|int
 */
function lp_commission_update_total_commission( $user_id, $value ) {
    $value = floatval( $value );

    return update_user_meta( $user_id, 'lp_commission_total', $value );
}

/**
 * @param $user_id
 * @param $value
 *
 * @return bool|int
 */
function lp_commission_add_commission( $user_id, $value ) {
    $old_value = lp_commission_get_total_commission( $user_id );
    $value = floatval( $value );
    $new_value = $old_value + $value;

    return lp_commission_update_total_commission( $user_id, $new_value );
}

/**
 * @param $user_id
 * @param $value
 *
 * @return bool|int
 */
function lp_commission_subtract_commission( $user_id, $value ) {
    $old_value = lp_commission_get_total_commission( $user_id );
    $value = floatval( $value );
    if ( $value > $old_value ) {
        return - 1;
    }

    $new_value = $old_value - $value;

    return lp_commission_update_total_commission( $user_id, $new_value );
}
