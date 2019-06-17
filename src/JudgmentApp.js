// exObj imported from php
//   exIds exemplars exGoldLevels

const { Component } = wp.element;
//import './judgmentapp.scss';
import PresentContext from './PresentContext';
import PresentEx from './PresentEx';
import Options from './Options';
import Rationale from './Rationale';
import ShowFeedback from './ShowFeedback';
import ShowEnd from './ShowEnd';

const nTrials = exObj.exIds.length;

class JudgmentApp extends Component {
    constructor( props ) {
        super(props);
        this.handleChoice = this.handleChoice.bind(this);
        this.handleRationale = this.handleRationale.bind(this);
        this.handleNext = this.handleNext.bind(this);
        this.getCase = this.getCase.bind(this);
        this.saveResults = this.saveResults.bind(this);
        this.state = {
            trial: 1,
            exId: exObj.exIds[0],
            choice: null,
            fbVisible: false,
            ratVisible: false,
            scores: [],
            accuracy: 0,
            allDone: false,
            rationales: []
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
                // fbVisible: true,
                ratVisible: true,
                scores: prevState.scores.concat(correct)
            };
        });
    }


    handleRationale(rationale) {
        if (!rationale) {
            return "Enter a valid rationale";
        }
        else if (rationale.length > 500) {
            return "Trim your rationale down to 500 characters";
        }
        this.setState(prevState => {
            return {
                rationales: prevState.rationales.concat(rationale),
                fbVisible: true,
                ratVisible: false
            };
        });
console.log(rationale.length);
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
    componentDidUpdate() {
        if ( document.getElementById("rationale") ) {
            const elDiv = document.getElementById("rationale");
            elDiv.scrollIntoView();
        }
    }
    render() {
        return (
            <div>
                { this.state.allDone && <ShowEnd /> }
                {!this.state.allDone &&
                    <PresentContext />
                }
                { !this.state.allDone &&
                    <PresentEx
                        exId ={ this.state.exId }
                        exemplar={ exObj.exemplars[this.state.exId] }
                    /> }
                { (!this.state.ratVisible && !this.state.fbVisible) &&
                    <Options handleChoice={this.handleChoice}/>
                }
                {this.state.ratVisible &&
                    <Rationale
                        choice={ this.state.choice }
                        handleRationale={this.handleRationale}
                    />
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