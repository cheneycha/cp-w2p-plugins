import {  
    GET_DESIGNS_SUCCESS, 
    GET_DESIGNS_FAILURE, 
    GET_DESIGNS_STARTED, 
    DELETE_DESIGNS,
    GET_DESIGNS_ENDED,
    UPDATE_DESIGNS_CHECKED_ITEMS,
    TEMPORARY_TOGGLE_DESIGNS_STATUS,
    SET_DESIGNS_FILTER
} from "../actionTypes";

import axios from 'axios';
import { notifySuccess, notifyFailure, hideNotify } from './notify';
import { headers } from './util';

/* ---------Get designs--------- */

export const getDesigns = search => {
    return ( dispatch, getState ) => {
        dispatch(getDesignsStarted());

        const defaultFilter = getState().designFilter.defaultFilter;
        let query = search !== '' ? search : defaultFilter;
        if( query.indexOf("per_page") == -1 ){
            query += '&' + defaultFilter.substring(1);
        }

        axios.get(nbdl.rest_url + 'designs' + query, { params:{}, headers: headers })
        .then(res => {
            dispatch(getDesignsSuccess(
                res.data, 
                parseInt( res.headers['x-status-all'] ),
                parseInt( res.headers['x-status-approved'] ),
                parseInt( res.headers['x-status-pending'] ),
                parseInt( res.headers['x-wp-total'] ),
                parseInt( res.headers['x-wp-totalpages'] )
            ));
        })
        .catch(err => {
            dispatch(getDesigsFailure(err.message));
        });
    }
};

const getDesignsSuccess = ( designs, all, approved, pending, total, totalpages ) => ({
    type: GET_DESIGNS_SUCCESS,
    designs,
    all,
    approved,
    pending,
    total,
    totalpages
});

const getDesignsStarted = () => ({
    type: GET_DESIGNS_STARTED
});

const getDesigsFailure = error => ({
    type: GET_DESIGNS_FAILURE,
    payload: {
        error
    }
});

export const setFilters = ( filters, history, filterType ) => {
    return ( dispatch, getState ) => {
        dispatch(setDesignsFilters(filters, filterType));
        const filter = getState().designFilter.filter;
        history.push( `/designs${filter}` );
    }
};

export const setDesignsFilters = ( filters, filterType ) => ({
    type: SET_DESIGNS_FILTER,
    filters,
    filterType
});

export const updateCheckedItemsAction = items => ({
    type: UPDATE_DESIGNS_CHECKED_ITEMS,
    items
});

/* ---------Toggle design status--------- */

export const toggleStatusAction = ( design_id, type, value, history ) => {
    return ( dispatch, getState ) => {

        dispatch( temporaryToggleStatus( design_id, type, value ) );

        const params = {
            [type]: value
        };

        axios.put(nbdl.rest_url + `designs/${design_id}`, params, { headers: headers })
        .then(res => {
            dispatch( getDesigns( history.location.search ) );
        })
        .catch(err => {
            dispatch(notifyFailure(err.message));
        });
    }
}

const temporaryToggleStatus = ( design_id, propType, value ) => ({
    type: TEMPORARY_TOGGLE_DESIGNS_STATUS,
    design_id,
    propType,
    value
});

export const bulkToggleStatusAction = (status, history) => {
    return ( dispatch, getState ) => {
        dispatch(getDesignsStarted());

        const checkedItems = getState().designs.checkedItems;

        const params = {
            [status]: checkedItems
        };

        axios.put(nbdl.rest_url + 'designs/batch', params, { headers: headers })
        .then(res => {
            dispatch( getDesigns( history.location.search ) );
        })
        .catch(err => {
            dispatch(notifyFailure(err.message));
            dispatch( hideDesignsLoading() );
        });
    }
}

const hideDesignsLoading = () => ({
    type: GET_DESIGNS_ENDED
});

export const deleteDesign = design_id => {
    return ( dispatch, getState ) => {
        dispatch(getDesignsStarted());

        axios.delete(nbdl.rest_url + `designs/${design_id}`, { headers: headers })
        .then(res => {
            dispatch( hideDeletedDesign( design_id ) );
            dispatch(notifySuccess(
                res.data.message
            ));
            setTimeout(() => dispatch( hideNotify() ), 2e3);
        })
        .catch(err => {
            dispatch(notifyFailure(err.message));
            dispatch( hideDesignsLoading() );
        });
    }
}

const hideDeletedDesign = design_id => ({
    type: DELETE_DESIGNS,
    design_id
});