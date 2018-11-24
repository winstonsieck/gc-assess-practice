//const { Component } = wp.element;

const PresentEx = (props) => {
    return (
        <div>
            <h2>Case: {props.exId}</h2>

            <p>{props.exemplar}</p>
        </div>
    );
};

export default PresentEx;