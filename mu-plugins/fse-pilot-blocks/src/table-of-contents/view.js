/**
 * Table of Contents frontend JavaScript.
 *
 * Handles:
 * - Adding IDs to headings in post content
 * - Smooth scrolling to anchors
 * - Active state tracking via Intersection Observer
 */

/* global IntersectionObserver */

( function () {
	'use strict';

	/**
	 * Generate a sanitized ID from heading text (matching PHP logic).
	 *
	 * @param {string} text - Heading text content.
	 * @return {string} Sanitized ID with 'toc-' prefix.
	 */
	function generateHeadingId( text ) {
		return (
			'toc-' +
			text
				.toLowerCase()
				.trim()
				.replace( /[^\w\s-]/g, '' ) // Remove special characters
				.replace( /\s+/g, '-' ) // Replace spaces with hyphens
				.replace( /-+/g, '-' ) // Replace multiple hyphens with single
		);
	}

	/**
	 * Add IDs to H2 and H3 headings in post content.
	 */
	function addHeadingIds() {
		// Find post content container.
		const postContent = document.querySelector(
			'.entry-content, .wp-block-post-content'
		);
		if ( ! postContent ) {
			return;
		}

		// Get all H2 and H3 headings.
		const headings = postContent.querySelectorAll( 'h2, h3' );

		headings.forEach( ( heading ) => {
			// Skip if heading already has an ID.
			if ( heading.id ) {
				return;
			}

			const text = heading.textContent || '';
			if ( text.trim() ) {
				heading.id = generateHeadingId( text );
			}
		} );
	}

	/**
	 * Setup smooth scrolling for TOC links.
	 */
	function setupSmoothScroll() {
		const tocLinks = document.querySelectorAll( '.toc-item' );

		tocLinks.forEach( ( link ) => {
			link.addEventListener( 'click', ( event ) => {
				const href = link.getAttribute( 'href' );
				if ( ! href || ! href.startsWith( '#' ) ) {
					return;
				}

				const targetId = href.substring( 1 );
				const targetElement = document.getElementById( targetId );

				if ( targetElement ) {
					event.preventDefault();

					// Scroll to target with offset for sticky header.
					const headerOffset = 100; // Adjust based on sticky header height.
					const elementPosition =
						targetElement.getBoundingClientRect().top;
					const offsetPosition =
						elementPosition + window.pageYOffset - headerOffset;

					window.scrollTo( {
						top: offsetPosition,
						behavior: 'smooth',
					} );

					// Update URL hash without jumping.
					window.history.pushState( null, null, href );
				}
			} );
		} );
	}

	/**
	 * Setup scroll tracking with Intersection Observer.
	 */
	function setupScrollTracking() {
		const tocLinks = document.querySelectorAll( '.toc-item' );
		if ( ! tocLinks.length ) {
			return;
		}

		// Get all heading IDs from TOC links.
		const headingIds = Array.from( tocLinks )
			.map( ( link ) => {
				const href = link.getAttribute( 'href' );
				return href ? href.substring( 1 ) : null;
			} )
			.filter( Boolean );

		// Get corresponding heading elements.
		const headings = headingIds
			.map( ( id ) => document.getElementById( id ) )
			.filter( Boolean );

		if ( ! headings.length ) {
			return;
		}

		// Track the currently active heading.
		let activeHeadingId = null;

		/**
		 * Update active state on TOC links.
		 *
		 * @param {string|null} headingId - ID of the active heading.
		 */
		function setActiveLink( headingId ) {
			if ( headingId === activeHeadingId ) {
				return;
			}

			activeHeadingId = headingId;

			// Remove active class from all links.
			tocLinks.forEach( ( link ) => {
				link.classList.remove( 'is-active' );
			} );

			// Add active class to matching link.
			if ( headingId ) {
				const activeLink = document.querySelector(
					`.toc-item[href="#${ headingId }"]`
				);
				if ( activeLink ) {
					activeLink.classList.add( 'is-active' );
				}
			}
		}

		// Create Intersection Observer.
		const observerOptions = {
			root: null, // Viewport.
			rootMargin: '-100px 0px -66% 0px', // Top offset for header, bottom threshold.
			threshold: 0,
		};

		const observer = new IntersectionObserver( ( entries ) => {
			entries.forEach( ( entry ) => {
				if ( entry.isIntersecting ) {
					setActiveLink( entry.target.id );
				}
			} );
		}, observerOptions );

		// Observe all headings.
		headings.forEach( ( heading ) => {
			observer.observe( heading );
		} );

		// Set initial active state based on scroll position.
		const setInitialActive = () => {
			for ( const heading of headings ) {
				const rect = heading.getBoundingClientRect();
				if ( rect.top >= 0 && rect.top < window.innerHeight / 2 ) {
					setActiveLink( heading.id );
					break;
				}
			}
		};

		// Run on load.
		setInitialActive();
	}

	/**
	 * Initialize TOC functionality.
	 */
	function init() {
		// Check if TOC block exists on page.
		const tocBlock = document.querySelector(
			'.wp-block-fse-pilot-table-of-contents'
		);
		if ( ! tocBlock ) {
			return;
		}

		addHeadingIds();
		setupSmoothScroll();
		setupScrollTracking();
	}

	// Run when DOM is ready.
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
