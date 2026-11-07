import React, { Component } from "react";
import { withRouter } from 'react-router-dom';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';

import { getDesigner, getDesignerStats } from '../../actions/designer';
import LeftSide from './leftSide';
import LeftSideInView from './leftSideInView';
import Banner from './banner';
import Infos from './infos';
import Stats from './stats';
import Actions from './actions';
import Loading from '../loading';
import styles from './styles.css';

class Designer extends Component {
    constructor(props) {
        super(props);
        this.designer_id = this.props.match.params.id;
        if( !!this.props.match.params.edit ){
            this.editMode = true;
        };
    }
    componentDidMount() {
        this.props.getDesigner( this.designer_id );
        this.props.getDesignerStats( this.designer_id );
    }
    componentWillUnmount() {
        this.props.onUnload();
    }
    render(){
        
        const { loading } = this.props;

        return(
            <div>
                {this.designer_id == 0 ? nbdl.langs.no_designer_found : (
                <React.Fragment>
                    <div className={styles.header}>
                        {this.editMode ? <LeftSide /> : <LeftSideInView />}
                        <Banner editMode={this.editMode} />
                    </div>
                    {this.editMode ? <Infos /> : <Stats />}
                    {this.editMode && <Actions />}
                </React.Fragment>
                )}
                <Loading loading={loading} />
            </div>
        );
    }
}

Designer.propTypes = {
    getDesigner: PropTypes.func.isRequired,
    getDesignerStats: PropTypes.func.isRequired,
    loading: PropTypes.bool,
    history: PropTypes.object,
    onUnload: PropTypes.func
};

const mapStateToProps = state => ({
    loading: state.designer.loading
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    getDesigner: id => dispatch( getDesigner( id ) ),
    getDesignerStats: id => dispatch( getDesignerStats( id ) ),
    onUnload: () => dispatch({ type: 'DESIGNER_PAGE_UNLOADED' })
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Designer));