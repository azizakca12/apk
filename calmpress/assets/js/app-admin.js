/**
 * CalmPress app editor: screenshot media picker (up to 4 images).
 *
 * @package CalmPress
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var button = document.getElementById( 'calmpress-app-screenshots-button' );
		var list = document.getElementById( 'calmpress-app-screenshots-list' );
		var input = document.getElementById( 'calmpress_screenshots' );
		if ( ! button || ! list || ! input || typeof wp === 'undefined' || ! wp.media ) {
			return;
		}
		var settings = window.calmpressAppAdmin || {};
		var maxItems = parseInt( list.getAttribute( 'data-max' ), 10 ) || 4;
		var frame = null;

		function syncInput() {
			var ids = [];
			list.querySelectorAll( 'li[data-id]' ).forEach( function ( item ) {
				var id = item.getAttribute( 'data-id' );
				if ( id ) {
					ids.push( id );
				}
			} );
			input.value = ids.join( ',' );
		}

		function addItem( attachment ) {
			if ( list.querySelectorAll( 'li[data-id]' ).length >= maxItems ) {
				return;
			}
			if ( list.querySelector( 'li[data-id="' + attachment.id + '"]' ) ) {
				return;
			}
			var item = document.createElement( 'li' );
			item.setAttribute( 'data-id', String( attachment.id ) );

			var img = document.createElement( 'img' );
			img.src = ( attachment.sizes && attachment.sizes.thumbnail ) ? attachment.sizes.thumbnail.url : attachment.url;
			img.alt = '';
			item.appendChild( img );

			var remove = document.createElement( 'button' );
			remove.type = 'button';
			remove.className = 'button-link calmpress-app-screenshots__remove';
			remove.setAttribute( 'aria-label', settings.removeLabel || 'Kaldır' );
			remove.innerHTML = '&times;';
			item.appendChild( remove );

			list.appendChild( item );
			syncInput();
		}

		list.addEventListener( 'click', function ( event ) {
			var removeButton = event.target.closest( '.calmpress-app-screenshots__remove' );
			if ( ! removeButton ) {
				return;
			}
			event.preventDefault();
			var item = removeButton.closest( 'li' );
			if ( item ) {
				item.remove();
				syncInput();
			}
		} );

		button.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			if ( list.querySelectorAll( 'li[data-id]' ).length >= maxItems ) {
				window.alert( settings.limitReached || 'En fazla dört ekran görüntüsü ekleyebilirsiniz.' );
				return;
			}
			if ( frame ) {
				frame.open();
				return;
			}
			frame = wp.media( {
				title: settings.title || 'Ekran görüntüsü seç',
				button: { text: settings.buttonText || 'Kullan' },
				multiple: true,
				library: { type: 'image' }
			} );
			frame.on( 'select', function () {
				var selection = frame.state().get( 'selection' );
				selection.each( function ( attachment ) {
					addItem( attachment.toJSON() );
				} );
			} );
			frame.open();
		} );
	} );
}() );