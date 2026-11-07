import React, { Component } from "react";
import classNames from 'classnames';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { withRouter } from 'react-router-dom';
import { bulkToggleStatusAction } from '../../actions/withdraws';
import Pagination from '../pagination';

class TableNav extends Component {

    bulkToggleStatus(){
        const status = document.querySelector('#bulk-action-selector-top').value;
        if( status == '-1' ) return;
        this.props.bulkToggleStatusAction( status )
    }

    render(){

        const { position, checkedItems, history } = this.props,
        search = history.location.search;
        let status = 'pending';
        if( search.indexOf( 'approved' ) > -1 ){
            status = 'approved';
        }else if( search.indexOf( 'cancelled' ) > -1 ){
            status = 'cancelled';
        }

        return(
            <div className={classNames("tablenav", position)}>
                <div className="alignleft actions bulkactions">
                    <label htmlFor={`bulk-action-selector-${position}`} className="screen-reader-text">{nbdl.langs.select_bulk_action}</label>
                    {status != 'approved' && <select name="action" id={`bulk-action-selector-${position}`}>
                        <option value="-1">{nbdl.langs.bulk_actions}</option>
                        <option value="approved">{nbdl.langs.approved}</option>
                        {status != 'pending' && <option value="pending">{nbdl.langs.pending}</option>}
                        {status != 'cancelled' && <option value="cancelled">{nbdl.langs.cancelled}</option>}
                    </select>}
                    {status != 'approved' && <button className="button action" disabled={!checkedItems.length} onClick={() => this.bulkToggleStatus()}>{nbdl.langs.apply}</button>}
                </div>
                <Pagination list="withdraws" />
            </div>
        );
    }
}

TableNav.propTypes = {
    position: PropTypes.string,
    checkedItems: PropTypes.array,
    bulkToggleStatusAction: PropTypes.func
}

const mapStateToProps = state => ({
    checkedItems: state.withdraws.checkedItems
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    bulkToggleStatusAction: ( status ) => dispatch( bulkToggleStatusAction( status, ownProps.history ) )
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(TableNav));