<?php
/**
 * Merge js options with defaults 
 *
 * @param  string $defaults
 * @return array js options
 */
function _wabs_js_options( $defaults = array() ) {
	return wp_parse_args( wabs_js_options(), $defaults );
}
/**
 * Get an action symbol by type
 *
 * @param  string $type
 * @return string $class, or "" if not found.
 */
function _wabs_action_symbol( $type = 'none' ) {

    $map = wabs_action_symbols();

    if ( isset( $map[$type] ) )
        return $map[$type];

    return "without_action";

}
/**
 * Get the top spacer to replace top margins when active
 *
 * @param  string $type
 * @return string $class, or "" if not found.
 */
function _wabs_top_spacer( $unique_id = 0 ) {

    $top_spacer = sprintf( '<div id=\'%1$stop_spacer_%2$d\' class=\'%1$stop_spacer\' />', WABS::TOKEN, $unique_id );
    
    return apply_filters( 'wabs_top_spacer', $top_spacer );

}
