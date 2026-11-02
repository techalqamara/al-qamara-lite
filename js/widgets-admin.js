/**
 * Al Qamara widget forms (Widgets screen + Customizer).
 *
 * The term lists come from the PHP side as the global `alqamaraWidgetData`
 * (see alqamara_widgets_admin_assets() in inc/widgets.php).
 */
( function ( $ ) {
	'use strict';

	var data = window.alqamaraWidgetData || {};

	/* Walk UP to the widget's own form container. Do NOT include
	 * [id^="widget-"]: WordPress names fields like "widget-..-__i__-field",
	 * so closest() would match the field itself instead of its parent. */
	function scopeOf( el ) {
		return $( el ).closest( '.widget-content, .widget-inside, form' );
	}

	/* Posts widget: post-type select -> rebuild the category dropdown. */
	$( document ).on( 'change', '.alqamara-pw-type-select', function () {
		var postType = $( this ).val();
		var taxonomy = ( data.typeMap && data.typeMap[ postType ] ) || '';
		var $wrap    = scopeOf( this ).find( '.alqamara-pw-cat-wrap' );
		var $select  = $wrap.find( '.alqamara-pw-term-select' );

		$wrap.find( '.alqamara-pw-tax-input' ).val( taxonomy );

		if ( ! taxonomy ) {
			$wrap.hide();
			return;
		}

		var terms = ( data.allTerms && data.allTerms[ postType ] ) || [];
		$select.empty().append( $( '<option>' ).val( 0 ).html( data.allOptions ) );
		$.each( terms, function ( i, term ) {
			$select.append( $( '<option>' ).val( parseInt( term.id, 10 ) ).text( term.name ) );
		} );
		$wrap.show();
	} );

	/* Category Showcase: category-type select -> rebuild the checkbox list.
	 * The checkbox name never changes between taxonomies, so it is read once
	 * from the wrapper's data-name attribute. */
	$( document ).on( 'change', '.alqamara-cs-tax-select', function () {
		var taxonomy = $( this ).val();
		var $list    = scopeOf( this ).find( '.alqamara-cs-cat-list' );
		var name     = $list.data( 'name' );
		var terms    = ( data.csTerms && data.csTerms[ taxonomy ] ) || [];

		$list.empty();

		if ( ! terms.length ) {
			$list.append( $( '<em>' ).text( data.noTerms || '' ) );
			return;
		}

		$.each( terms, function ( i, term ) {
			var $label = $( '<label>' );
			$( '<input type="checkbox">' )
				.attr( 'name', name + '[]' )
				.val( parseInt( term.id, 10 ) )
				.appendTo( $label );
			$label.append( document.createTextNode( ' ' + term.name + ' ' ) );
			$( '<span class="alqamara-cs-count">' ).text( '(' + parseInt( term.count, 10 ) + ')' ).appendTo( $label );
			$list.append( $label );
		} );
	} );

	/* "Read More" checkbox: show/hide its text field. */
	$( document ).on( 'change', '.alqamara-pw-readmore-chk', function () {
		scopeOf( this ).find( '.alqamara-pw-readmore-row' ).toggle( this.checked );
	} );

	/* "Slider" checkbox: show/hide the slider options. */
	$( document ).on( 'change', '.alqamara-pw-slider-chk', function () {
		scopeOf( this ).find( '.alqamara-pw-slider-opts' ).toggle( this.checked );
	} );

}( jQuery ) );
