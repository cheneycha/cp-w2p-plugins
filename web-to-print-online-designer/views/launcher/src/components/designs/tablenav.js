import React, { Component } from "react";
import classNames from 'classnames';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { withRouter } from 'react-router-dom';
import { bulkToggleStatusAction, setFilters } from '../../actions/designs';
import Pagination from '../pagination';

class TableNav extends Component {

    constructor(props) {
        super(props);
        this.userRef = React.createRef();
    }

    bulkToggleStatus(){
        const status = document.querySelector('#bulk-action-selector-top').value;
        if( status == '-1' ) return;
        this.props.bulkToggleStatusAction( status );
        setTimeout(() => {
            jQuery('.check-column input').prop('checked', false);
        })
    }

    componentDidMount() {

    }

    submitFilter(e){
        e.preventDefault();

        const filter_obj = {};
        ['user_id', 'product_id'].map( filter => {
            const val = jQuery(`[name="_${filter}"]`).val()
            filter_obj[filter] = !!val ? val : '';

            if( !val ){
                setTimeout(() => {
                    jQuery(`[name="_${filter}"]`).val('').removeClass("enhanced").remove('option');
                    jQuery( document.body ).trigger( 'wc-enhanced-select-init' );
                }, 200);
            }
        });

        const { setFilters, history } = this.props,
        search = history.location.search;
        let status = '';
        if( search.indexOf( 'approved' ) > -1 ){
            status = 'approved';
        }else if( search.indexOf( 'pending' ) > -1 ){
            status = 'pending';
        }
        filter_obj['status'] = status;

        setFilters( filter_obj );
    }

    getUserName( user_id ){
        const { designs } = this.props;
        let name = '';
        if( !!designs ){
            designs.map(design => {
                if( design.user.id == user_id ){
                    name = design.user.artist_name != '' ? design.user.artist_name : nbdl.langs.no_name;
                }
            });
            setTimeout(() => {
                this.userRef.current.selected = true;
                this.userRef.current.setAttribute('selected', 'selected');
                this.userRef.current.parentNode.classList.remove("enhanced");
                jQuery( document.body ).trigger( 'wc-enhanced-select-init' );
            });
        }
        return name;
    }

    render(){

        const { position, checkedItems, user_id } = this.props;

        return(
            <div className={classNames("tablenav", position)}>
                <div className="alignleft actions bulkactions">
                    <label htmlFor={`bulk-action-selector-${position}`} className="screen-reader-text">{nbdl.langs.select_bulk_action}</label>
                    <select name="action" id={`bulk-action-selector-${position}`}>
                        <option value="-1">{nbdl.langs.bulk_actions}</option>
                        <option value="approved">{nbdl.langs.publish}</option>
                        <option value="pending">{nbdl.langs.pending}</option>
                        <option value="delete">{nbdl.langs.delete}</option>
                        <option value="re_generate_preview">{nbdl.langs.re_generate_preview}</option>
                    </select>
                    <button className="button action" disabled={!checkedItems.length} onClick={() => this.bulkToggleStatus()}>{nbdl.langs.apply}</button>
                </div>
                {position == 'top' && <div className="alignleft actions">
                    <label htmlFor="filter-by-user" className="screen-reader-text">{nbdl.langs.all_user}</label>
                    <select className="wc-customer-search" name="_user_id" data-placeholder={nbdl.langs.filter_by_user} data-allow_clear="true">
                        {user_id != '' && <option value={user_id} ref={this.userRef}>{this.getUserName(user_id)}</option>}
                    </select>
                    <select className="wc-product-search" name="_product_id" data-action="woocommerce_json_search_products" data-placeholder={nbdl.langs.filter_by_product} data-allow_clear="true">
                        
                    </select>
                    <input type="submit" name="filter_action" id="post-query-submit" className="button" value={nbdl.langs.filter} onClick={(e) => this.submitFilter(e)}/>
                </div>}
                <Pagination list="designs" />
            </div>
        );
    }
}

TableNav.propTypes = {
    position: PropTypes.string,
    user_id: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
    product_id: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
    checkedItems: PropTypes.array,
    bulkToggleStatusAction: PropTypes.func,
    submitFilter: PropTypes.func,
    setFilters: PropTypes.func
}

const mapStateToProps = state => ({
    checkedItems: state.designs.checkedItems,
    user_id: state.designFilter.user_id,
    product_id: state.designFilter.product_id,
    designs: state.designs.designs
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    bulkToggleStatusAction: ( status ) => dispatch( bulkToggleStatusAction( status, ownProps.history ) ),
    setFilters: filters => dispatch( setFilters( filters, ownProps.history ) )
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(TableNav));