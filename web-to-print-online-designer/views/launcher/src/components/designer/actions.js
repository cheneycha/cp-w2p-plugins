import React, { Component } from "react";
import styles from './actions.css';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { withRouter } from 'react-router-dom';

import { updateInfos } from '../../actions/designer';

class Actions extends Component {
    _updateInfos(){
        this.props.updateInfos();
    }
    cancelUpdate(){
        const { history, infos } = this.props,
        designer_id = infos ? infos.id : 0;
        history.push( `/designer/${designer_id}` );
    }
    render(){
        return (
            <div className={styles['acctions-wrap']} >
                <button className="button cancel" onClick={() => this.cancelUpdate()}>{nbdl.langs.cancel}</button>
                <button className="button button-primary" onClick={() => this._updateInfos()}>{nbdl.langs.save_changes}</button>
            </div>
        )
    }
}

Actions.propTypes = {
    infos: PropTypes.object,
    updateInfos: PropTypes.func
};

const mapStateToProps = state => ({
    infos: state.designer.infos
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    updateInfos: () => dispatch( updateInfos( ownProps.history ) )
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Actions));