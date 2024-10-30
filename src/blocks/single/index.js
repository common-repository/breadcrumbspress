import {registerBlockType} from "@wordpress/blocks";
import {BlockControls, useBlockProps} from "@wordpress/block-editor";
import ServerSideRender from "@wordpress/server-side-render";
import {InspectorControls} from "@wordpress/editor";
import {Disabled} from "@wordpress/components";
import icons from "../../helpers/icons";
import CrumbsAdvanced from "../../controls/advanced";
import CrumbsDisplay from "../../controls/display";
import CrumbsAlign from "../../controls/alignment";
import metadata from "./block.json";

registerBlockType(metadata, {
    icon: icons.single,
    edit: ({attributes, setAttributes, clientId}) => {
        const {block_id} = attributes;
        if (!block_id) {
            setAttributes({block_id: clientId.substring(0, 8)});
        }

        return (
            <div {...useBlockProps()}>
                <Disabled>
                    <ServerSideRender
                        block='breadcrumbspress/single'
                        attributes={attributes}
                    />
                </Disabled>
                <BlockControls>
                    <CrumbsAlign
                        attributes={attributes}
                        setAttributes={setAttributes}
                    />
                </BlockControls>
                <InspectorControls key="settings">
                    <CrumbsDisplay
                        attributes={attributes}
                        setAttributes={setAttributes}
                    />
                    <CrumbsAdvanced
                        attributes={attributes}
                        setAttributes={setAttributes}
                    />
                </InspectorControls>
            </div>
        );
    },
    save: () => {
        return null
    }
});
