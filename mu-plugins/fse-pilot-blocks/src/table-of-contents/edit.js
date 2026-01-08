/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';

/**
 * Editor styles
 */
import './editor.scss';

/**
 * Generate a sanitized ID from heading text.
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
			.replace( /[^\w\s-]/g, '' )
			.replace( /\s+/g, '-' )
			.replace( /-+/g, '-' )
	);
}

/**
 * Extract H2 and H3 headings from blocks.
 *
 * @param {Array} blocks - Array of parsed blocks.
 * @return {Array} Array of heading objects with level, text, and id.
 */
function extractHeadings( blocks ) {
	const headings = [];

	blocks.forEach( ( block ) => {
		if ( block.name === 'core/heading' ) {
			const level = block.attributes?.level || 2;

			if ( level === 2 || level === 3 ) {
				// Extract text from heading content.
				const content = block.attributes?.content || '';
				const text = content.replace( /<[^>]+>/g, '' ).trim();

				if ( text ) {
					headings.push( {
						level,
						text,
						id: generateHeadingId( text ),
					} );
				}
			}
		}

		// Recursively check inner blocks.
		if ( block.innerBlocks?.length ) {
			headings.push( ...extractHeadings( block.innerBlocks ) );
		}
	} );

	return headings;
}

/**
 * Build hierarchical TOC structure.
 *
 * @param {Array} headings - Flat array of headings.
 * @return {Array} Hierarchical array with H3s nested under H2s.
 */
function buildHierarchy( headings ) {
	const hierarchy = [];
	let currentH2Index = null;

	headings.forEach( ( heading ) => {
		if ( heading.level === 2 ) {
			hierarchy.push( {
				heading,
				children: [],
			} );
			currentH2Index = hierarchy.length - 1;
		} else if ( heading.level === 3 ) {
			if ( currentH2Index !== null ) {
				hierarchy[ currentH2Index ].children.push( heading );
			} else {
				hierarchy.push( {
					heading,
					children: [],
				} );
			}
		}
	} );

	return hierarchy;
}

/**
 * The edit function for the Table of Contents block.
 *
 * @param {Object} props         - Block props.
 * @param {Object} props.context - Block context including postId.
 * @return {import('@wordpress/element').WPElement} Element to render.
 */
export default function Edit( { context } ) {
	const { postId, postType } = context;

	// Get all blocks from the current post.
	const blocks = useSelect(
		( select ) => {
			if ( ! postId ) {
				// In site editor without specific post context.
				return select( 'core/block-editor' ).getBlocks();
			}
			// Get post content blocks.
			const post = select( 'core' ).getEntityRecord(
				'postType',
				postType || 'post',
				postId
			);
			if ( post?.content?.raw ) {
				return select( 'core/blocks' ).parse( post.content.raw );
			}
			return [];
		},
		[ postId, postType ]
	);

	// Extract headings from blocks.
	const headings = extractHeadings( blocks );
	const hierarchy = buildHierarchy( headings );

	const blockProps = useBlockProps( {
		className: 'wp-block-fse-pilot-table-of-contents',
	} );

	// Show placeholder if no headings found.
	if ( ! hierarchy.length ) {
		return (
			<div { ...blockProps }>
				<div className="toc-header">
					<p className="toc-title">
						{ __( 'Table of Contents', 'fse-pilot-blocks' ) }
					</p>
				</div>
				<nav className="toc-list">
					<p className="toc-placeholder">
						{ __(
							'Add H2 or H3 headings to your post to generate a table of contents.',
							'fse-pilot-blocks'
						) }
					</p>
				</nav>
			</div>
		);
	}

	return (
		<div { ...blockProps }>
			<div className="toc-header">
				<p className="toc-title">
					{ __( 'Table of Contents', 'fse-pilot-blocks' ) }
				</p>
			</div>
			<nav className="toc-list">
				{ hierarchy.map( ( item, index ) => (
					<div key={ index }>
						<span
							className={ `toc-item toc-item--h${ item.heading.level }` }
						>
							{ item.heading.text }
						</span>
						{ item.children.length > 0 && (
							<div className="toc-sublist">
								{ item.children.map( ( child, childIndex ) => (
									<span
										key={ childIndex }
										className="toc-item toc-item--h3"
									>
										{ child.text }
									</span>
								) ) }
							</div>
						) }
					</div>
				) ) }
			</nav>
		</div>
	);
}
