
const ShowEnd = () => {
    return (
        <div>
            <h4>You're all done. Your percentage correct was: <span id="final-score"></span>%
            </h4>
            <a href="http://localhost/sandbox-genesis-scratch/react-in-wp" className="button">Try again</a>
        </div>
    );
};

export default ShowEnd;

{/*<p>Your final score was: <b>{ props.accuracy }%</b></p>*/}