import Option from './Option';

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

export default Options;