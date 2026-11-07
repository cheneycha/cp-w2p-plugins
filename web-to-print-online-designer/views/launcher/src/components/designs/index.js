import React, { Component } from "react";
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { getDesigns, setFilters, setDesignsFilters } from '../../actions/designs';

import Loading from '../loading';
import Filters from '../filters';
import List from './list';
import TableNav from './tablenav';

class Designs extends Component {

    componentDidMount() {
        const { history, setDesignsFilters, getDesigns } = this.props;
        const params = new URLSearchParams(history.location.search); 
        let filters = {};

        ['status', 'page', 'per_page', 'user_id', 'product_id'].map(query => {
            if( params.has(query) ){
                filters[query] = parseInt( params.get(query) );
            }
        });

        setDesignsFilters(filters);
        this.initWooSelect();
        getDesigns();
        this.unlisten = history.listen((location, action) => {
            getDesigns();
           
            this.initWooSelect();
        });
    }

    componentWillUnmount() {
        this.props.onUnload();
        this.unlisten();
    }

    initWooSelect(){
        ['user_id', 'product_id'].map( filter => {
            const val = this.props[filter];
            if( !val ){
                setTimeout(() => {
                    jQuery(`[name="_${filter}"]`).val('').removeClass("enhanced").remove('option');
                    jQuery( document.body ).trigger( 'wc-enhanced-select-init' );
                }, 200);
            }
        })
    }

    render() {
        const { designs, loading } = this.props;

        return (
            <div className="post-type-shop_order">
                <Loading loading={loading} />
                <Filters list="designs" />
                <TableNav position="top" />
                <List designs={designs} />
                <TableNav position="bottom" />
            </div>
        );
    }
}

Designs.propTypes = {
    designs: PropTypes.array.isRequired,
    loading: PropTypes.bool.isRequired,
    getDesigns: PropTypes.func.isRequired,
    error: PropTypes.string,
    filter: PropTypes.string,
    user_id: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
    product_id: PropTypes.oneOfType([PropTypes.string, PropTypes.number])
};

const mapStateToProps = state => ({
    designs: state.designs.designs,
    loading: state.designs.loading,
    error: state.designs.error,
    filter: state.designFilter.filter,
    user_id: state.designFilter.user_id,
    product_id: state.designFilter.product_id
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    setFilters: ( filters ) => dispatch( setFilters( filters, ownProps.history ) ),
    setDesignsFilters: filters => dispatch( setDesignsFilters( filters ) ),
    getDesigns: () => {
        return dispatch( getDesigns( ownProps.history.location.search ) )
    },
    onUnload: () => dispatch({ type: 'DESIGNS_PAGE_UNLOADED' })
});

export default connect(mapStateToProps, mapDispatchToProps)(Designs);