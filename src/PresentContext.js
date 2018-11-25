

// import { Panel, PanelBody, PanelRow } from '@wordpress/components';
// const { Panel, PanelBody, PanelRow } = wp.components;

//import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import ExpansionPanel from '@material-ui/core/ExpansionPanel';
import ExpansionPanelSummary from '@material-ui/core/ExpansionPanelSummary';
import ExpansionPanelDetails from '@material-ui/core/ExpansionPanelDetails';
import Typography from '@material-ui/core/Typography';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';

const styles = theme => ({
    root: {
        width: '100%',
    },
    heading: {
        fontSize: theme.typography.pxToRem(24),
        fontWeight: theme.typography.fontWeightRegular,
    },
});

function PresentContext(props) {
    const { classes } = props;
    return (
        <div className={classes.root}>
            <ExpansionPanel>
                <ExpansionPanelSummary expandIcon={<ExpandMoreIcon />}>
                    <Typography className={classes.heading}>Problem Statement</Typography>
                </ExpansionPanelSummary>
                <ExpansionPanelDetails>
                    <Typography className={classes.heading}>
                        Please write a paragraph of <em>Lorem ipsum</em> from memory
                    </Typography>
                </ExpansionPanelDetails>
            </ExpansionPanel>
            <ExpansionPanel>
                <ExpansionPanelSummary expandIcon={<ExpandMoreIcon />}>
                    <Typography className={classes.heading}>Rubric Definition</Typography>
                </ExpansionPanelSummary>
                <ExpansionPanelDetails>
                    <Typography className={classes.heading}>
                        The rubric defines the quality of the Lorem Ipsum produced.
                        <ul>
                            <li>Low response will have indicators of bland Lorem ipsum</li>
                            <li>Mid level response will be better than the low level response, but not as good as the high level response </li>
                            <li>High level response show truly inspired Lorem ipsum</li>
                        </ul>
                    </Typography>
                </ExpansionPanelDetails>
            </ExpansionPanel>
        </div>
    );
}

PresentContext.propTypes = {
    classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(PresentContext);