import {__} from "@wordpress/i18n";
import {FontSizePicker, PanelBody} from "@wordpress/components";

const CrumbsDisplay = ({attributes, setAttributes}) => {
    return (
        <PanelBody title={__('Display', 'breadcrumbspress')} initialOpen={true}>
            <FontSizePicker
                value={attributes.font_size}
                onChange={(value) => setAttributes({font_size: value})}
                fontSizes={[
                    {
                        name: __('Small', 'breadcrumbspress'),
                        slug: 'small',
                        size: 10,
                    },
                    {
                        name: __('Normal', 'breadcrumbspress'),
                        slug: 'normal',
                        size: 14,
                    },
                    {
                        name: __('Medium', 'breadcrumbspress'),
                        slug: 'medium',
                        size: 18,
                    },
                    {
                        name: __('Big', 'breadcrumbspress'),
                        slug: 'big',
                        size: 22,
                    },
                    {
                        name: __('Large', 'breadcrumbspress'),
                        slug: 'large',
                        size: 26,
                    }
                ]}
                fallBackFontSize={14}
                disableCustomFontSizes={false}
                withSlider={true}
                withReset={true}
            />
        </PanelBody>
    )
};

export default CrumbsDisplay;
