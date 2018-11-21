// exObj imported from php
//   exIds exemplars exGoldLevels

const { Component } = wp.element;
//import './judgmentapp.scss';
import PresentEx from './PresentEx';
import ShowFeedback from './ShowFeedback';
import Options from './Options';

const nTrials = exObj.exIds.length;

class JudgmentApp extends Component {
    constructor( props ) {
        super(props);
        this.handleChoice = this.handleChoice.bind(this);
        this.handleNext = this.handleNext.bind(this);
        this.getCase = this.getCase.bind(this);
        this.state = {
            trial: 1,
            exId: exObj.exIds[0],
            choice: null,
            fbVisible: false
        };
    }
    handleChoice(option) {
        this.setState(() => {
            return {
                choice: option,
                fbVisible: true
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
            alert("All done");
        }
    }

    getCase() {
        this.setState(() => {
            return {
                exId: exObj.exIds[this.state.trial - 1],
                fbVisible: false
            };
        });
    }

    render() {
        return (
            <div>
                <h2>Case: {this.state.exId}</h2>
                <PresentEx exemplar={ exObj.exemplars[this.state.exId] } />
                {!this.state.fbVisible &&
                    <Options handleChoice={this.handleChoice}/>
                }
                {this.state.fbVisible &&
                    <ShowFeedback
                        choice={ this.state.choice }
                        actual={ exObj.exGoldLevels[this.state.exId] }
                />
                }
                { this.state.fbVisible && <button onClick={ this.handleNext }>Next</button> }
            </div>
        );
    }
}

export default JudgmentApp;