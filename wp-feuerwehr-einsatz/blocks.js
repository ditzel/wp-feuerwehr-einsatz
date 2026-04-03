const { registerBlockType } = wp.blocks;
const { createElement, Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor || wp.editor;
const { PanelBody, TextControl, ToggleControl } = wp.components;

registerBlockType('ff/einsatz-form', {
    title: 'Einsatz Erfassen',
    icon: 'welcome-write-blog',
    category: 'widgets',
    edit: function() {
        return createElement(
            'div',
            { style: { padding: '20px', background: '#f5f5f5', border: '1px solid #ddd' } },
            createElement('h3', null, 'Feuerwehr Einsatz Formular'),
            createElement('p', null, 'Das Formular wird auf der Website angezeigt. Nur angemeldete Benutzer können es ausfüllen.')
        );
    },
    save: function() {
        return null; // Wird durch PHP gerendert
    }
});

registerBlockType('ff/einsatz-list', {
    title: 'Einsätze Anzeigen',
    icon: 'list-view',
    category: 'widgets',
    attributes: {
        limit: {
            type: 'number',
            default: 0
        },
        showYearFilter: {
            type: 'boolean',
            default: false
        }
    },
    edit: function(props) {
        const { attributes, setAttributes } = props;
        
        return createElement(
            Fragment,
            null,
            createElement(
                InspectorControls,
                null,
                createElement(
                    PanelBody,
                    { title: 'Einstellungen', initialOpen: true },
                    createElement(TextControl, {
                        label: 'Anzahl Einsätze (0 für alle)',
                        type: 'number',
                        value: attributes.limit,
                        onChange: (val) => setAttributes({ limit: parseInt(val, 10) || 0 })
                    }),
                    createElement(ToggleControl, {
                        label: 'Jahres-Filter anzeigen',
                        checked: attributes.showYearFilter,
                        onChange: (val) => setAttributes({ showYearFilter: val })
                    })
                )
            ),
            createElement(
                'div',
                { style: { padding: '20px', background: '#f5f5f5', border: '1px solid #ddd' } },
                createElement('h3', null, 'Feuerwehr Einsätze Liste'),
                createElement('p', null, 'Zeigt die erfassten Einsätze an.'),
                createElement('p', null, 'Limit an Einsätzen: ' + (attributes.limit > 0 ? attributes.limit : 'Alle') + 
                    ' | Jahres-Filter: ' + (attributes.showYearFilter ? 'An' : 'Aus'))
            )
        );
    },
    save: function() {
        return null;
    }
});
