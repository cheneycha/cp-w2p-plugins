import React, { Component } from "react";
import classNames from 'classnames';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { bulkToggleStatusAction } from '../../actions/designers';
import Pagination from '../pagination';

class TableNav extends Component {
    bulkToggleStatus(){
        const status = document.querySelector('#bulk-action-selector-top').value;
        if( status == '-1' ) return;
        this.props.bulkToggleStatusAction( status )
    }
    render(){
        const { position, checkedItems } = this.props;
        return(
            <div className={classNames("tablenav", position)}>
                <div className="alignleft actions bulkactions">
                    <label htmlFor={`bulk-action-selector-${position}`} className="screen-reader-text">{nbdl.langs.select_bulk_action}</label>
                    <select name="action" id={`bulk-action-selector-${position}`}>
                        <option value="-1">{nbdl.langs.bulk_actions}</option>
                        <option value="approved">{nbdl.langs.approved}</option>
                        <option value="pending">{nbdl.langs.pending}</option>
                    </select>
                    <button className="button action" disabled={!checkedItems.length} onClick={() => this.bulkToggleStatus()}>{nbdl.langs.apply}</button>
                </div>
                <Pagination list="designers" />
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
    checkedItems: state.designers.checkedItems
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    bulkToggleStatusAction: ( status ) => dispatch( bulkToggleStatusAction( status ) )
});

export default connect(mapStateToProps, mapDispatchToProps)(TableNav);