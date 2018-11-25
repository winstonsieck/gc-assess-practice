// exObj imported from php
//   exIds exemplars exGoldLevels

const { Component } = wp.element;
//import './judgmentapp.scss';
import SimpleExpansionPanel from './PresentContext';
//import MyPanel from './WpPanel';
import PresentEx from './PresentEx';
import Options from './Options';
import ShowFeedback from './ShowFeedback';
import ShowEnd from './ShowEnd';

const nTrials = exObj.exIds.length;

class JudgmentApp extends Component {
    constructor( props ) {
        super(props);
        this.handleChoice = this.handleChoice.bind(this);
        this.handleNext = this.handleNext.bind(this);
        this.getCase = this.getCase.bind(this);
        this.saveResults = this.saveResults.bind(this);
        this.state = {
            trial: 1,
            exId: exObj.exIds[0],
            choice: null,
            fbVisible: false,
            scores: [],
            accuracy: 0,
            allDone: false
        };
    }

    handleChoice(option) {

        const actual = exObj.exGoldLevels[this.state.exId];
        let correct = 0;
        if ( option === actual ) {
            correct = 1;
        }

        this.setState((prevState) => {
            return {
                choice: option,
                fbVisible: true,
                scores: prevState.scores.concat(correct)
            };
        });
    }

    handleNext() {
        if (this.state.trial < nTrials) {
            this.setState((prevState) => {
                return {
                    trial: prevState.trial + 1
                };
            },
                this.getCase
            );
        } else {
            let response = this.saveResults();
            this.setState(() => {
                return {
                    allDone: true,
                    accuracy: response
                };
            });
            // alert("All done");
        }
// console.log(exObj.percent_correct);
    }

    getCase() {
        this.setState(() => {
            return {
                exId: exObj.exIds[this.state.trial - 1],
                fbVisible: false
            };
        });
    }

    saveResults() {

        jQuery.ajax({
            url : exObj.ajax_url,
            type : 'post',
            data : {
                action : 'gcap_add_scores',
                scores: this.state.scores,
                _ajax_nonce: exObj.nonce
            },
            success : function( response ) {
                    if( response ) {
                        // alert( 'Your score is: ' + response );
                        jQuery('#final-score').html( 100*response );
                    } else {
                        alert( 'Something went wrong, try logging in!' );
                    }
                }
        });
    }

    render() {
        return (
            <div>
                { this.state.allDone && <ShowEnd /> }
                <SimpleExpansionPanel />
                {/*{!this.state.allDone &&*/}
                    {/*<SimpleExpansionPanel />*/}
                {/*}*/}
                { !this.state.allDone &&
                    <PresentEx
                        exId ={ this.state.exId }
                        exemplar={ exObj.exemplars[this.state.exId] }
                    /> }
                {!this.state.fbVisible &&
                    <Options handleChoice={this.handleChoice}/>
                }
                { (this.state.fbVisible && !this.state.allDone) &&
                    <ShowFeedback
                        choice={ this.state.choice }
                        actual={ exObj.exGoldLevels[this.state.exId] }
                        handleNext={ this.handleNext }
                    /> }
            </div>
        );
    }
}

export default JudgmentApp;