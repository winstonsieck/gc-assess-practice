// exObj imported from php
//   exIds exemplars exGoldLevels

const { Component } = wp.element;
//import './judgmentapp.scss';

const nTrials = exObj.exIds.length;

class JudgmentApp extends Component {
    constructor( props ) {
        super(props);
        this.handleChoice = this.handleChoice.bind(this);
        this.handleNext = this.handleNext.bind(this);
        this.getCase = this.getCase.bind(this);
//        this.handleFeedbackVisibility = this.handleFeedbackVisibility.bind(this);

        this.state = {
            trial: 1,
            exId: exObj.exIds[0],
            choice: null,
            fbVisibility: false
        };
    }
    handleChoice(option) {
//        alert(option);
//        this.handleFeedbackVisibility();
        this.setState(() => {
            return {
                choice: option,
                fbVisibility: true
            };
        });
    }

    // handleFeedbackVisibility() {
    //     this.setState((prevState) => {
    //         return {
    //             fbVisibility: !prevState.fbVisibility
    //         };
    //     });
    // }

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
                fbVisibility: false
            };
        });
    }

    render() {
//        console.log( nTrials );
        return (
            <div>
                <h2>Case: {this.state.exId}</h2>
                <PresentEx exemplar={ exObj.exemplars[this.state.exId] } />
                <Options handleChoice={ this.handleChoice } />
                {this.state.fbVisibility &&
                <CorrectAns
                    choice={ this.state.choice }
                    actual={ exObj.exGoldLevels[this.state.exId] }
                />
                }
                { this.state.fbVisibility && <button onClick={ this.handleNext }>Next</button> }
            </div>
        );
    }
}



const PresentEx = (props) => {
    return (
        <div>
            <p>{props.exemplar}</p>
        </div>
    );
};

const Options = (props) => {
  return (
      <div>
          <Option optionText={'low'}
                  handleChoice={ props.handleChoice }
          />
          <Option optionText={'medium'}
                  handleChoice={ props.handleChoice }
          />
          <Option optionText={'high'}
                  handleChoice={ props.handleChoice }
          />
      </div>
  );
};

const Option = (props) => {
  return (
      <button
          onClick={(e) => {
              props.handleChoice(props.optionText);
          }}>
          {props.optionText}
      </button>
  );
};

const CorrectAns = (props) => {
        return (
            <div>
                <p>You chose: <b>{props.choice}</b> <br />
                    The correct level for this case is: <b>{props.actual}</b></p>
            </div>
        );
};

export default JudgmentApp;