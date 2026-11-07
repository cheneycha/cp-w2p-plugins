import React, { Component } from "react";
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { getDesigners, setFilters, setDesignersFilters } from '../../actions/designers';

import Loading from '../loading';
import List from './list';
import Filters from '../filters';
import TableNav from './tablenav';

class Designers extends Component {
    constructor(props) {
        super(props);
        this.sortBy = this.sortBy.bind(this);
    }
    componentDidMount() {
        const params = new URLSearchParams(this.props.history.location.search); 
        let filters = {};
        if( params.has('status') ){
            filters.status = params.get('status');
        }
        if( params.has('orderby') ){
            filters.orderby = params.get('orderby');
            filters.order = params.get('order');
        }
        if( params.has('page') ){
            filters.page = parseInt( params.get('page') );
        }
        if( params.has('per_page') ){
            filters.per_page = parseInt( params.get('per_page') );
        }
        this.props.setDesignersFilters(filters);
        this.props.getDesigners();
        this.unlisten = this.props.history.listen((location, action) => {
            this.props.getDesigners();
        });
    }
    componentWillUnmount() {
        this.props.onUnload();
        this.unlisten();
    }
    sortBy( event, type ){
        event.preventDefault();
        let filters = { page: 1 };
        switch ( type ){
            case 'registered':
                filters.orderby = 'registered';
                break;
            default:
                filters.orderby = '';
                break;
        }
        let pageInputs = document.querySelectorAll('input[name="paged"]');
        for ( let i = 0; i < pageInputs.length; i++ ){
            pageInputs[i].value = 1;
        }
        this.props.setFilters( filters, 'sort' );
    }

    render() {
        const { designers, loading } = this.props;
        
        return (
            <div>
                <Loading loading={loading} />
                <Filters list="designers" />
                <TableNav position="top" />
                <List designers={designers} sortBy={this.sortBy} />
                <TableNav position="bottom" />
            </div>
        );
    }
}

Designers.propTypes = {
    designers: PropTypes.array.isRequired,
    loading: PropTypes.bool.isRequired,
    getDesigners: PropTypes.func.isRequired,
    setDesignersFilters: PropTypes.func.isRequired,
    error: PropTypes.string,
    filter: PropTypes.string,
};

const mapStateToProps = state => ({
    designers: state.designers.designers,
    loading: state.designers.loading,
    error: state.designers.error,
    filter: state.designerRouter.filter
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    setFilters: ( filters, filterType ) => dispatch( setFilters( filters, ownProps.history, filterType ) ),
    setDesignersFilters: filters => dispatch( setDesignersFilters( filters ) ),
    getDesigners: () => {
        return dispatch( getDesigners( ownProps.history.location.search ) )
    },
    onUnload: () => dispatch({ type: 'DESIGNERS_PAGE_UNLOADED' })
});

export default connect(mapStateToProps, mapDispatchToProps)(Designers);