/**
 * Podcast Blocks – admin settings page script.
 *
 * Handles the WordPress media uploader for podcast artwork selection on the
 * Podcast Blocks settings page.
 */
/* global wp, jQuery */

( function ( $ ) {
	'use strict';

	var mediaFrame;

	$( document ).ready( function () {

		// Open the media uploader.
		$( '#podcast-upload-artwork' ).on( 'click', function ( e ) {
			e.preventDefault();

			// Re-use an existing frame if one was already opened.
			if ( mediaFrame ) {
				mediaFrame.open();
				return;
			}

			mediaFrame = wp.media( {
				title: 'Select or Upload Podcast Artwork',
				button: {
					text: 'Use this image',
				},
				multiple: false,
				library: {
					type: 'image',
				},
			} );

			mediaFrame.on( 'select', function () {
				var attachment = mediaFrame.state().get( 'selection' ).first().toJSON();
				var url        = attachment.url;
				var width      = attachment.width  || 0;
				var height     = attachment.height || 0;

				// Warn if dimensions are outside the iTunes-recommended range.
				if ( width && height ) {
					var min = 600;
					var max = 1400;
					if ( width !== height ) {
						// phpcs:ignore
						window.alert( 'Warning: Podcast artwork should be square. The selected image is ' + width + '×' + height + 'px.' );
					} else if ( width < min || width > max ) {
						// phpcs:ignore
						window.alert( 'Warning: Podcast artwork should be between ' + min + '×' + min + ' and ' + max + '×' + max + ' pixels. The selected image is ' + width + '×' + height + 'px.' );
					}
				}

				$( '#podcast-artwork-id' ).val( attachment.id );
				$( '#podcast-artwork-preview' ).attr( 'src', url ).show();
				$( '#podcast-remove-artwork' ).show();
			} );

			mediaFrame.open();
		} );

		// Remove the selected artwork.
		$( '#podcast-remove-artwork' ).on( 'click', function ( e ) {
			e.preventDefault();
			$( '#podcast-artwork-id' ).val( '' );
			$( '#podcast-artwork-preview' ).attr( 'src', '' ).hide();
			$( this ).hide();
		} );

	} );

}( jQuery ) );
