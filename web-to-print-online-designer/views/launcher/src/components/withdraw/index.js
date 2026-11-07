import React, { Component } from "react";
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { getWithdraws, setFilters, setWithdrawsFilters } from '../../actions/withdraws';

import Loading from '../loading';
import Filters from '../filters';
import List from './list';
import TableNav from './tablenav';

class Withdraw extends Component {

    componentDidMount() {
        const { history, setWithdrawsFilters, getWithdraws } = this.props;
        const params = new URLSearchParams(history.location.search); 
        let filters = {};
        if( params.has('status') ){
            filters.status = params.get('status');
        }
        if( params.has('page') ){
            filters.page = parseInt( params.get('page') );
        }
        if( params.has('per_page') ){
            filters.per_page = parseInt( params.get('per_page') );
        }
        setWithdrawsFilters(filters);
        getWithdraws();
        this.unlisten = history.listen((location, action) => {
            getWithdraws();
        });
    }

    componentWillUnmount() {
        this.props.onUnload();
        this.unlisten();
    }

    render() {
        const { withdraws, loading } = this.props;

        return (
            <div>
                <Loading loading={loading} />
                <Filters list="withdraws" />
                <TableNav position="top" />
                <List withdraws={withdraws} />
                <TableNav position="bottom" />
            </div>
        );
    }
}

Withdraw.propTypes = {
    withdraws: PropTypes.array.isRequired,
    loading: PropTypes.bool.isRequired,
    getWithdraws: PropTypes.func.isRequired,
    error: PropTypes.string,
    filter: PropTypes.string,
};

const mapStateToProps = state => ({
    withdraws: state.withdraws.withdraws,
    loading: state.withdraws.loading,
    error: state.withdraws.error,
    filter: state.withdrawFilter.filter
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    setFilters: ( filters ) => dispatch( setFilters( filters, ownProps.history ) ),
    setWithdrawsFilters: filters => dispatch( setWithdrawsFilters( filters ) ),
    getWithdraws: () => {
        return dispatch( getWithdraws( ownProps.history.location.search ) )
    },
    onUnload: () => dispatch({ type: 'WITHDRAW_PAGE_UNLOADED' })
});

export default connect(mapStateToProps, mapDispatchToProps)(Withdraw);