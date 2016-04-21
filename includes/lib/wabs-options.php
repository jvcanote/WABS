<?php
/**
 * Return an array of js options
 *
 * @return array
 */
function wabs_js_options() {

	// todo: get_option( $option );
	return apply_filters( 'wabs_js_options', array( 
				          'behavior'      => 'toggle',
				          'zIndex'		  => 800,
		                  'speedIn'       => 600,
		                  'speedOut'      => 400,
		                  'daysHidden'    => 15, 
		                  'daysReminder'  => 90,
		                  'debug'         => false ) );
}
/**
 * Return an array of built in available icons
 *
 * @return array
 */
function wabs_action_symbols() {

    return apply_filters( 'wabs_action_symbols', array(
              'delta' => "<svg xmlns='http://www.w3.org/2000/svg' id='action_symbol_delta' width='44' height='44' data-name='action symbol delta' viewBox='0 0 44 44'><defs><style>.cls-1{fill:currentColor;opacity:0.2;}</style></defs><path id='delta' d='M38 0H6a6 6 0 0 0-6 6v32a6 6 0 0 0 6 6h32a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6zM13 28.53a.96.96 0 0 1-.87-1.65l8.74-12.75a1.28 1.28 0 0 1 2.26 0l8.74 12.75a.96.96 0 0 1-.87 1.65H13z' class='cls-1'/></svg>",
             'vector' => "<svg xmlns='http://www.w3.org/2000/svg' id='action_symbol_vector' width='44' height='44' data-name='action symbol vector' viewBox='0 0 44 44'><defs><style>.cls-1{fill:currentColor;opacity:0.2;}</style></defs><path id='vector' d='M38 4H6a2 2 0 0 0-2 2v32a2 2 0 0 0 2 2h32a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm-6.1 25.07a2 2 0 0 1-2.83 2.83L22 24.83l-7.07 7.07a2 2 0 0 1-2.83-2.83L19.17 22l-7.07-7.07a2 2 0 0 1 2.83-2.83L22 19.17l7.07-7.07a2 2 0 0 1 2.83 2.83L24.83 22z' class='cls-1'/></svg>",
       'delta_hollow' => "<svg xmlns='http://www.w3.org/2000/svg' id='action_symbol_delta_hollow' width='44' height='44' data-name='action symbol delta hollow' viewBox='0 0 44 44'><defs><style>.cls-1{fill:currentColor;opacity:0.2;}</style></defs><path id='delta_hollow' d='M38 4a2 2 0 0 1 2 2v32a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h32m0-4H6a6 6 0 0 0-6 6v32a6 6 0 0 0 6 6h32a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6zm-7 28.53a.96.96 0 0 0 .87-1.65l-8.74-12.75a1.28 1.28 0 0 0-2.26 0l-8.74 12.75a.96.96 0 0 0 .87 1.65h18z' data-name='delta hollow' class='cls-1'/></svg>",
      'vector_hollow' => "<svg xmlns='http://www.w3.org/2000/svg' id='action_symbol_vector_hollow' width='44' height='44' data-name='action symbol vector hollow' viewBox='0 0 44 44'><defs><style>.cls-1{fill:currentColor;opacity:0.2;}</style></defs><path id='vector_hollow' d='M38 0H6a6 6 0 0 0-6 6v32a6 6 0 0 0 6 6h32a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6zm2 38a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h32a2 2 0 0 1 2 2v32zm-8.8-22.74l-6.37 6.37L31.2 28a2 2 0 1 1-2.85 2.8L22 24.46l-6.36 6.37A2 2 0 0 1 12.8 28l6.37-6.38-6.36-6.36a2 2 0 0 1 2.86-2.83L22 18.8l6.36-6.37a2 2 0 0 1 2.83 2.83z' data-name='vector hollow' class='cls-1'/></svg>",
    ) );

}
