/**
 * Smooth scroll for newsletter anchor links
 */
document.addEventListener( 'DOMContentLoaded', () => {
	document.querySelectorAll( 'a[href="#newsletter"]' ).forEach( ( link ) => {
		link.addEventListener( 'click', ( e ) => {
			e.preventDefault();

			// Close mobile menu if open
			const mobileMenu = document.querySelector(
				'.wp-block-navigation__responsive-container.is-menu-open'
			);
			if ( mobileMenu ) {
				const closeButton = mobileMenu.querySelector(
					'.wp-block-navigation__responsive-container-close'
				);
				if ( closeButton ) {
					closeButton.click();
				}
			}

			// Smooth scroll to newsletter
			const target = document.getElementById( 'newsletter' );
			if ( target ) {
				target.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			}
		} );
	} );
} );
