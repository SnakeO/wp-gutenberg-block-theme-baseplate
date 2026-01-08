/**
 * WordPress dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';

/**
 * Add offset control to the Query Loop block inspector.
 *
 * This higher-order component wraps the Query block's edit component
 * and injects an additional RangeControl for setting the query offset.
 */
const addOffsetControl = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		if ( props.name !== 'core/query' ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, setAttributes } = props;
		const { query } = attributes;

		return (
			<>
				<BlockEdit { ...props } />
				<InspectorControls>
					<PanelBody title="Offset Settings">
						<RangeControl
							label="Skip posts"
							value={ query?.offset || 0 }
							onChange={ ( value ) =>
								setAttributes( {
									query: { ...query, offset: value },
								} )
							}
							min={ 0 }
							max={ 20 }
							help="Number of posts to skip before displaying results"
						/>
					</PanelBody>
				</InspectorControls>
			</>
		);
	};
}, 'addOffsetControl' );

addFilter( 'editor.BlockEdit', 'fse-pilot/query-offset', addOffsetControl );
