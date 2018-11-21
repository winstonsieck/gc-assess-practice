
const ShowFeedback = (props) => {
    return (
        <div>
            <p>You chose: <b>{props.choice}</b> <br />
                The correct level for this case is: <b>{props.actual}</b></p>
        </div>
    );
};

export default ShowFeedback;