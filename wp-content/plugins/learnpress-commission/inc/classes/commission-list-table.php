<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
// WP_List_Table is not loaded automatically so we need to load it in our application
if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class LP_Commission_List_Table extends WP_List_Table {

    public $delete_posts = null;

    public function __construct( $args = array() ) {
        parent::__construct( $args );

        $this->prepare_items();
    }

    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();

        usort( $data, array( &$this, 'sort_data' ) );

        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count( $data );
        $this->set_pagination_args(
                array(
                    'total_items' => $totalItems,
                    'per_page' => $perPage
                )
        );
        $data = array_slice( $data, ( ( $currentPage - 1 ) * $perPage ), $perPage );
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->items = $data;
    }

    public function get_columns() {
        return array(
            'id' => 'ID',
            'course' => 'Courses',
            'instructor' => 'Main Instructors',
            'value' => 'Value',
            'active' => 'Active'
        );
    }

    public function get_hidden_columns() {
        return array();
    }

    public function get_sortable_columns() {
        return array( 'id' => array( 'id', false ) );
    }

    private function table_data() {
        $data = array();
        $the_query = lp_commission_query_all_course();

        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ) {
                $the_query->the_post();

                $course_id = get_the_ID();

                $data[] = array(
                    'id' => $course_id,
                    'course' => get_the_title(),
                    'instructor' => get_the_author(),
                    'value' => LPC()->get_commission_main_instructor( $course_id )
                );
            }
        }

        return $data;
    }

    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'id':
                return '<span class="lp_commission_course_id" data-value="' . $item[$column_name] . '">' . $item[$column_name] . '</span>';
            case 'course':
                return '<a href="' . get_edit_post_link( $item['id'] ) . '" target="_blank">' . $item[$column_name] . '</a>';
            case 'instructor':
                return $item[$column_name];
            case 'value':
                return '<input name="' . LPC()->key_main_instructor . '[' . $item['id'] . ']" type="number" min="0" max="100" value="' . $item[$column_name] . '"><span class="unit">%</span>';
            case 'active':
                return '<input name="' . LPC()->key_active . '[' . $item['id'] . ']" type="hidden" value="no">'
                        . '<input name="' . LPC()->key_active . '[' . $item['id'] . ']" type="checkbox" ' . checked( true, lp_commission_is_active( $item['id'] ), false ) . ' value="yes">';
            default:
                return print_r( $item, true );
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b ) {
        // Set defaults
        $orderby = 'id';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if ( !empty( $_GET['orderby'] ) ) {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if ( !empty( $_GET['order'] ) ) {
            $order = $_GET['order'];
        }
        $result = $a[$orderby] < $b[$orderby];
        if ( $order === 'asc' ) {
            return $result;
        }

        return - $result;
    }

}
