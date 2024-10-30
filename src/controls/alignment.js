import {alignCenter, alignJustify, alignLeft, alignNone, alignRight} from "@wordpress/icons";
import {__} from "@wordpress/i18n";
import {AlignmentToolbar} from "@wordpress/block-editor";

const CrumbsAlign = ({attributes, setAttributes}) => {
    return (
        <AlignmentToolbar
            alignmentControls={[
                {icon: alignNone, title: __('Inherit', 'breadcrumbspress'), align: 'inherit'},
                {icon: alignLeft, title: __('Align left', 'breadcrumbspress'), align: 'flex-start'},
                {icon: alignCenter, title: __('Align center', 'breadcrumbspress'), align: 'center'},
                {icon: alignRight, title: __('Align right', 'breadcrumbspress'), align: 'flex-end'},
                {icon: alignJustify, title: __('Space Evenly', 'breadcrumbspress'), align: 'space-evenly'},
                {icon: alignJustify, title: __('Space Between', 'breadcrumbspress'), align: 'space-between'},
                {icon: alignJustify, title: __('Space Around', 'breadcrumbspress'), align: 'space-around'}
            ]}
            value={attributes.align}
            onChange={(value) => setAttributes({align: value})}
        />)
};

export default CrumbsAlign;
