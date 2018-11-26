const { Component } = wp.element;

class Rationale extends Component {
    constructor(props) {
        super(props);
        this.handleRationaleObj = this.handleRationaleObj.bind(this);
        this.state = {
            error: undefined
        };
    }

    handleRationaleObj(e) {
        // e is event object
        e.preventDefault();

       const rationale = e.target.elements.rationale.value.trim();
       const error = this.props.handleRationale(rationale);

        this.setState(() => {
            // same as error: error (state being updated by the const)
            return { error };
        });

        //clear input if no error
        if (!error) {
            e.target.elements.rationale.value = "";
        }
    };
    render() {
        return (
            <div>
                <p>You chose: <b>{this.props.choice}</b> <br /></p>
                {/*{this.state.error && <p>{this.state.error}</p>}*/}
                <form onSubmit={this.handleRationaleObj}>
                    <span>Please explain your choice:</span>
                    <textarea name="rationale" cols={40} rows={5} />
                    {/*<input type="text" name="rationale" />*/}
                    <button className="button">Enter Rationale</button>
                </form>
            </div>
        );
    }
}

export default Rationale;