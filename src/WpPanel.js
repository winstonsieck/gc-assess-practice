
// import { Panel, PanelBody, PanelRow } from '@wordpress/components';
const { Panel, PanelBody, PanelRow } = wp.components;
// const { Component } = wp.element;

const MyPanel = () => (
    <Panel header="My Panel">
        <PanelBody
            title="My Block Settings"
            // icon="welcome-widgets-menus"
            // initialOpen={ true }
        >
            <PanelRow>
                My Panel Inputs and Labels
            </PanelRow>
        </PanelBody>
    </Panel>
);

export default MyPanel;