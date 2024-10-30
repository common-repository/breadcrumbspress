import {__} from "@wordpress/i18n";
import {PanelBody, TextControl} from "@wordpress/components";

const CrumbsAdvanced = ({attributes, setAttributes}) => {
    return (
        <PanelBody title={__('Advanced', 'breadcrumbspress')} initialOpen={false}>
            <TextControl
                label={__('Additional CSS Class', 'breadcrumbspress')}
                value={attributes.class}
                onChange={(value) => setAttributes({class: value})}
            />
        </PanelBody>
    )
};

export default CrumbsAdvanced;
