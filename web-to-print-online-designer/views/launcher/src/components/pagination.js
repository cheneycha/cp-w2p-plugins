import React, { Component } from "react";
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import PropTypes from 'prop-types';
import { setFilters as setDesignersFilters }  from '../actions/designers';
import { setFilters as setWithdrawsFilters }  from '../actions/withdraws';
import { setFilters as setDesignsFilters }  from '../actions/designs';

const setFilters = {
    designers: setDesignersFilters,
    withdraws: setWithdrawsFilters,
    designs: setDesignsFilters
};

const routeFilter = {
    designers: 'designerRouter',
    withdraws: 'withdrawFilter',
    designs: 'designFilter'
}

class Pagination extends Component {
    constructor(props){
        super(props);
        this.handleKeyUp = this.handleKeyUp.bind(this);
    }

    handleKeyUp( event ){
        if( event.keyCode == 13 ){
            const { totalpages, setFilters } = this.props;
            const page = parseInt( event.target.value );
            if( page > 0 && page <= totalpages ){
                setFilters( { page } );
                let pageInputs = document.querySelectorAll('input[name="paged"]');
                for ( let i = 0; i < pageInputs.length; i++ ){
                    pageInputs[i].value = page;
                }
            }
        }
    }

    goToPage(event, page){
        event.preventDefault();
        const { totalpages, setFilters } = this.props;
        page = parseInt( page );
        if( page > 0 && page <= totalpages ){
            setFilters( { page } );
            let pageInputs = document.querySelectorAll('input[name="paged"]');
            for ( let i = 0; i < pageInputs.length; i++ ){
                pageInputs[i].value = page;
            }
        }
    }

    render(){

        const { total, totalpages, page } = this.props;

        return(
            <div className="tablenav-pages">
                <span className="displaying-num">{total} {total > 1 ? nbdl.langs.items : nbdl.langs.item}</span>
                {totalpages > 1 && 
                (<span className="pagination-links">
                    {page > 2 ?
                    <a href="#" onClick={event => this.goToPage(event, 1)} className="first-page button" >
                        <span aria-hidden="true">«</span>
                    </a> 
                    : <span aria-hidden="true" className="tablenav-pages-navspan button disabled">«</span> }
                    {page > 1 ? 
                    <a href="#" onClick={event => this.goToPage(event, page - 1)} className="prev-page button" >
                        <span aria-hidden="true">‹</span>
                    </a> 
                    : <span aria-hidden="true" className="tablenav-pages-navspan button disabled">‹</span> }
                    <span className="paging-input">
                            <input type="text" name="paged" aria-describedby="table-paging" size="1" defaultValue={page} onKeyDown={this.handleKeyUp}
                                className="current-page" />
                        <span className="tablenav-paging-text">
                             {nbdl.langs.of} <span className="total-pages">{totalpages}</span>
                        </span>
                    </span> 
                    {page < totalpages ?
                    <a href="#" onClick={event => this.goToPage(event, page + 1)} className="next-page button">
                        <span aria-hidden="true">›</span>
                    </a> 
                    : <span aria-hidden="true" className="tablenav-pages-navspan button disabled">›</span> }
                    {page < (totalpages - 1) ?
                    <a href="#" onClick={event => this.goToPage(event, totalpages)} className="next-page button">
                        <span aria-hidden="true">»</span>
                    </a> 
                    :<span aria-hidden="true" className="tablenav-pages-navspan button disabled">»</span> }
                </span>)}
            </div>
        );
    }
}

Pagination.propTypes = {
    total: PropTypes.number,
    totalpages: PropTypes.number,
    page: PropTypes.number,
    history: PropTypes.object,
    setFilters: PropTypes.func
}

const mapStateToProps = (state, ownProps) => ({
    total: state[ownProps.list].total,
    totalpages: state[ownProps.list].totalpages,
    page: state[routeFilter[ownProps.list]].page,
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    setFilters: filters => dispatch( setFilters[ownProps.list]( filters, ownProps.history ) )
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Pagination));