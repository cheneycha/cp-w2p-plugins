import { 
    GET_DESIGNERS_SUCCESS, 
    GET_DESIGNERS_FAILURE, 
    GET_DESIGNERS_STARTED, 
    SET_DESIGNERS_FILTER, 
    UPDATE_CHECKED_ITEMS,
    TEMPORARY_TOGGLE_STATUS,
    GET_DESIGNERS_ENDED,
    BATCH_UPDATE_DESIGNERS_SUCCESS
} from "../actionTypes";

import axios from 'axios';
import { notifySuccess, notifyFailure, hideNotify } from './notify';
import { headers } from './util';

/* ---------Get designers--------- */

export const getDesigners = search => {
    return ( dispatch, getState ) => {
        dispatch(getDesignersStarted());

        const defaultFilter = getState().designerRouter.defaultFilter;
        let query = search !== '' ? search : defaultFilter;
        if( query.indexOf("per_page") == -1 ){
            query += '&' + defaultFilter.substring(1);
        }

        axios.get(nbdl.rest_url + 'designers' + query, { params:{}, headers: headers })
        .then(res => {
            dispatch(getDesignersSuccess(
                res.data, 
                parseInt( res.headers['x-status-all'] ),
                parseInt( res.headers['x-status-approved'] ),
                parseInt( res.headers['x-status-pending'] ),
                parseInt( res.headers['x-wp-total'] ),
                parseInt( res.headers['x-wp-totalpages'] )
            ));
        })
        .catch(err => {
            dispatch(getDesignersFailure(err.message));
        });
    }
};

const getDesignersSuccess = ( designers, all, approved, pending, total, totalpages ) => ({
    type: GET_DESIGNERS_SUCCESS,
    designers,
    all,
    approved,
    pending,
    total,
    totalpages
});

const getDesignersStarted = () => ({
    type: GET_DESIGNERS_STARTED
});

const getDesignersFailure = error => ({
    type: GET_DESIGNERS_FAILURE,
    payload: {
        error
    }
});

export const setFilters = ( filters, history, filterType ) => {
    return ( dispatch, getState ) => {
        dispatch(setDesignersFilters(filters, filterType));
        const filter = getState().designerRouter.filter;
        history.push( `/designers${filter}` );
    }
};

export const setDesignersFilters = ( filters, filterType ) => ({
    type: SET_DESIGNERS_FILTER,
    filters,
    filterType
});

export const updateCheckedItemsAction = items => ({
    type: UPDATE_CHECKED_ITEMS,
    items
});


/* ---------Toggle designer status--------- */

export const toggleStatusAction = ( designer_id, statusType, value ) => {
    return ( dispatch, getState ) => {

        dispatch( temporaryToggleStatus( designer_id, statusType, value ) );

        const params = {
            id: designer_id,
            [statusType]: value
        };

        axios.put(nbdl.rest_url + `designers/${designer_id}/status`, params, { headers: headers })
        .then(res => {
            dispatch(notifySuccess(
                res.data.message
            ));
            setTimeout(() => dispatch( hideNotify() ), 2e3);
        })
        .catch(err => {
            dispatch(notifyFailure(err.message));
        });
    }
}

const temporaryToggleStatus = ( designer_id, statusType, value ) => ({
    type: TEMPORARY_TOGGLE_STATUS,
    designer_id,
    statusType,
    value
});

export const bulkToggleStatusAction = status => {
    return ( dispatch, getState ) => {
        dispatch(getDesignersStarted());

        const checkedItems = getState().designers.checkedItems;

        const params = {
            [status]: checkedItems
        };

        axios.put(nbdl.rest_url + 'designers/batch', params, { headers: headers })
        .then(res => {
            dispatch(notifySuccess(
                res.data.message
            ));
            setTimeout(() => dispatch( hideNotify() ), 1.5e3);
            dispatch( hideDesignersLoading() );

            const payload = {};
            if( res.data.approved ){
                payload.enabled = true;
                payload.designers = res.data.approved;
            }else{
                payload.enabled = false;
                payload.designers = res.data.pending;
            }
            dispatch( batchUpdateSuccess( payload ) );
        })
        .catch(err => {
            dispatch(notifyFailure(err.message));
            dispatch( hideDesignersLoading() );
        });
    }
}

const hideDesignersLoading = () => ({
    type: GET_DESIGNERS_ENDED
});

const batchUpdateSuccess = payload => ({
    type: BATCH_UPDATE_DESIGNERS_SUCCESS,
    payload
});