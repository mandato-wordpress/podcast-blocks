/**
 * Podcast Blocks – Podcast Player block frontend script.
 *
 * Handles the subscribe popup modal: open on button click, close on overlay
 * click / close-button click / ESC key, and basic focus management so the
 * panel is keyboard and screen-reader accessible.
 *
 * This script is loaded automatically by WordPress only on pages that contain
 * the podcast-player block (declared via "viewScript" in block.json).
 */

( function () {
	'use strict';

	/** @type {HTMLElement|null} Button that triggered the currently open modal. */
	let lastFocusedBtn = null;

	/**
	 * Open a subscribe modal.
	 *
	 * @param {HTMLElement} modal   The .pb-subscribe-modal element.
	 * @param {HTMLElement} trigger The button that was clicked.
	 */
	function openModal( modal, trigger ) {
		lastFocusedBtn = trigger;

		modal.classList.add( 'is-open' );
		modal.removeAttribute( 'aria-hidden' );
		document.body.classList.add( 'pb-modal-open' );

		trigger.setAttribute( 'aria-expanded', 'true' );

		// Move focus to the close button so keyboard users can immediately
		// navigate to the service links or dismiss the panel.
		const closeBtn = modal.querySelector( '.pb-subscribe-modal-close' );
		if ( closeBtn ) {
			closeBtn.focus();
		}
	}

	/**
	 * Close a subscribe modal and restore focus to the triggering button.
	 *
	 * @param {HTMLElement} modal The .pb-subscribe-modal element.
	 */
	function closeModal( modal ) {
		modal.classList.remove( 'is-open' );
		modal.setAttribute( 'aria-hidden', 'true' );
		document.body.classList.remove( 'pb-modal-open' );

		if ( lastFocusedBtn ) {
			lastFocusedBtn.setAttribute( 'aria-expanded', 'false' );
			lastFocusedBtn.focus();
			lastFocusedBtn = null;
		}
	}

	/**
	 * Trap keyboard focus inside the modal box while it is open.
	 * Cycles between the close button and the last service link.
	 *
	 * @param {KeyboardEvent} e
	 * @param {HTMLElement}   modal
	 */
	function trapFocus( e, modal ) {
		const focusable = Array.from(
			modal.querySelectorAll(
				'.pb-subscribe-modal-close, .pb-service, [tabindex]:not([tabindex="-1"])'
			)
		);
		if ( ! focusable.length ) {
			return;
		}

		const first = focusable[ 0 ];
		const last = focusable[ focusable.length - 1 ];
		const doc = modal.ownerDocument;

		if ( e.shiftKey ) {
			if ( doc.activeElement === first ) {
				e.preventDefault();
				last.focus();
			}
		} else if ( doc.activeElement === last ) {
			e.preventDefault();
			first.focus();
		}
	}

	// -------------------------------------------------------------------------
	// Wire up event listeners after the DOM is ready.
	// -------------------------------------------------------------------------

	document.addEventListener( 'DOMContentLoaded', function () {
		// Open: click on any .pb-subscribe-btn
		document
			.querySelectorAll( '.pb-subscribe-btn' )
			.forEach( function ( btn ) {
				btn.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					const modalId = btn.getAttribute( 'data-modal' );
					const modal = document.getElementById( modalId );
					if ( modal ) {
						openModal( modal, btn );
					}
				} );
			} );

		// Close: click on overlay or close button (delegated to document so
		// it works even if blocks are injected dynamically).
		document.addEventListener( 'click', function ( e ) {
			// Overlay click
			if ( e.target.classList.contains( 'pb-subscribe-modal-overlay' ) ) {
				const modal = e.target.closest( '.pb-subscribe-modal' );
				if ( modal ) {
					closeModal( modal );
				}
			}
			// Close button click
			if ( e.target.closest( '.pb-subscribe-modal-close' ) ) {
				const closeModal2 = e.target.closest( '.pb-subscribe-modal' );
				if ( closeModal2 ) {
					closeModal( closeModal2 );
				}
			}
		} );

		// Close: ESC key
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) {
				const open = document.querySelector(
					'.pb-subscribe-modal.is-open'
				);
				if ( open ) {
					closeModal( open );
				}
			}

			// Tab key – focus trap
			if ( e.key === 'Tab' ) {
				const openModal2 = document.querySelector(
					'.pb-subscribe-modal.is-open'
				);
				if ( openModal2 ) {
					trapFocus( e, openModal2 );
				}
			}
		} );
	} );
} )();
