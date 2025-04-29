import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';

registerBlockType('top5blogs/block', {
    title: 'Top 5 WordPress Blogs',
    icon: 'admin-post',
    category: 'widgets',
    attributes: {
        order: {
            type: 'string',
            default: 'DESC',
        },
        orderBy: {
            type: 'string',
            default: 'date',
        },
        numberOfPosts: {
            type: 'number',
            default: 5,
        },
    },
    edit({ attributes, setAttributes }) {
        const { order, orderBy, numberOfPosts } = attributes;

        return (
            <>
                <InspectorControls>
                    <PanelBody title="Settings">
                        <SelectControl
                            label="Order"
                            value={order}
                            options={[
                                { label: 'ASC', value: 'ASC' },
                                { label: 'DESC', value: 'DESC' },
                            ]}
                            onChange={(value) => setAttributes({ order: value })}
                        />
                        <SelectControl
                            label="Order By"
                            value={orderBy}
                            options={[
                                { label: 'Name', value: 'name' },
                                { label: 'Publish Date', value: 'date' },
                            ]}
                            onChange={(value) => setAttributes({ orderBy: value })}
                        />
                        <TextControl
                            label="Number of Posts"
                            type="number"
                            value={numberOfPosts}
                            onChange={(value) => setAttributes({ numberOfPosts: parseInt(value) })}
                            min="1"
                            max="10"
                        />
                    </PanelBody>
                </InspectorControls>
                <p><strong>Top 5 Blogs Block:</strong> Settings available in the sidebar.</p>
            </>
        );
    },
    save() {
        return null; // Server-side render
    }
});
