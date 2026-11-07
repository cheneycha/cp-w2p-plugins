import {
    GET_WITHDRAW_SUCCESS,
    GET_WITHDRAW_FAILURE,
    GET_WITHDRAW_STARTED,
    DELETE_WITHDRAW,
    GET_WITHDRAW_ENDED,
    TEMPORARY_TOGGLE_WITHDRAW_STATUS,
    BATCH_UPDATE_WITHDRAW_SUCCESS,
    SET_WITHDRAW_FILTER,
    UPDATE_WITHDRAW_CHECKED_ITEMS
} from "../actionTypes";

import axios from 'axios';
import { notifySuccess, notifyFailure, hideNotify } from './notify';
import { headers } from './util';

/* ---------Get withdraws--------- */

export const getWithdraws = search => {
    return ( dispatch, getState ) => {
        dispatch(getWithdrawsStarted());

        const defaultFilter = getState().withdrawFilter.defaultFilter;
        let query = search !== '' ? search : defaultFilter;
        if( query.indexOf("per_page") == -1 ){
            query += '&' + defaultFilter.substring(1);
        }

        axios.get(nbdl.rest_url + 'withdraws' + query, { params:{}, headers: headers })
        .then(res => {
            dispatch(getWithdrawsSuccess(
                res.data, 
                parseInt( res.headers['x-status-cancelled'] ),
                parseInt( res.headers['x-status-approved'] ),
                parseInt( res.headers['x-status-pending'] ),
                parseInt( res.headers['x-wp-total'] ),
                parseInt( res.headers['x-wp-totalpages'] )
            ));
        })
        .catch(err => {
            dispatch(getWithdrawsFailure(err.message));
        });
    }
};

const getWithdrawsSuccess = ( withdraws, cancelled, approved, pending, total, totalpages ) => ({
    type: GET_WITHDRAW_SUCCESS,
    withdraws,
    cancelled,
    approved,
    pending,
    total,
    totalpages
});

const getWithdrawsStarted = () => ({
    type: GET_WITHDRAW_STARTED
});

const getWithdrawsFailure = error => ({
    type: GET_WITHDRAW_FAILURE,
    payload: {
        error
    }
});

export const setFilters = ( filters, history ) => {
    return ( dispatch, getState ) => {
        dispatch(setWithdrawsFilters(filters));
        const filter = getState().withdrawFilter.filter;
        history.push( `/withdraws${filter}` );
    }
};

export const setWithdrawsFilters = ( filters ) => ({
    type: SET_WITHDRAW_FILTER,
    filters
});

export const updateCheckedItemsAction = items => ({
    type: UPDATE_WITHDRAW_CHECKED_ITEMS,
    items
});


/* ---------Toggle withdraw status--------- */

export const toggleStatusAction = ( withdraw_id, type, value, history ) => {
    return ( dispatch, getState ) => {

        dispatch( temporaryToggleStatus( withdraw_id, type, value ) );

        const params = {
            [type]: value
        };

        axios.put(nbdl.rest_url + `withdraws/${withdraw_id}`, params, { headers: headers })
        .then(res => {
            if( type == 'note' ){
                dispatch(notifySuccess(
                    res.data.message
                ));
                dispatch( hideWithdrawsLoading() );
                setTimeout(() => dispatch( hideNotify() ), 2e3);
            } else {
                dispatch( getWithdraws( history.location.search ) );
            }
        })
        .catch(err => {
            dispatch(notifyFailure(err.message));
        });
    }
}

const temporaryToggleStatus = ( withdraw_id, propType, value ) => ({
    type: TEMPORARY_TOGGLE_WITHDRAW_STATUS,
    withdraw_id,
    propType,
    value
});

export const bulkToggleStatusAction = (status, history) => {
    return ( dispatch, getState ) => {
        dispatch(getWithdrawsStarted());

        const checkedItems = getState().withdraws.checkedItems;

        const params = {
            [status]: checkedItems
        };

        axios.put(nbdl.rest_url + 'withdraws/batch', params, { headers: headers })
        .then(res => {
            dispatch( getWithdraws( history.location.search ) );
        })
        .catch(err => {
            dispatch(notifyFailure(err.message));
            dispatch( hideWithdrawsLoading() );
        });
    }
}

const hideWithdrawsLoading = () => ({
    type: GET_WITHDRAW_ENDED
});

export const deleteWithdraw = withdraw_id => {
    return ( dispatch, getState ) => {
        dispatch(getWithdrawsStarted());

        axios.delete(nbdl.rest_url + `withdraws/${withdraw_id}`, { headers: headers })
        .then(res => {
            dispatch( hideDeletedWithdraw( withdraw_id ) );
            dispatch(notifySuccess(
                res.data.message
            ));
            setTimeout(() => dispatch( hideNotify() ), 2e3);
        })
        .catch(err => {
            dispatch(notifyFailure(err.message));
            dispatch( hideWithdrawsLoading() );
        });
    }
}

const hideDeletedWithdraw = withdraw_id => ({
    type: DELETE_WITHDRAW,
    withdraw_id
});