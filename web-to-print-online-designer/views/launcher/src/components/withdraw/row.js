import React, { Component } from "react";
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import classNames from 'classnames';
import rowStyles from './row.css';
import { updateCheckedItemsAction, toggleStatusAction, deleteWithdraw } from '../../actions/withdraws';
import { openModal, setModalContent } from '../../actions/modal';
import NoteForm from '../modal/changeWithdrawNoteForm';

class Row extends Component {
    constructor(props) {
        super(props);
        this.checkboxRef = React.createRef();
    }

    componentDidMount() {
        this.checkboxRef.current.checked = false;
    }

    updatecheckeItems(event){
        const { checkedItems, updateCheckedItemsAction } = this.props;
        let _checkedItems = JSON.parse( JSON.stringify( checkedItems ) );
        const val = event.target.value;
        const index = _checkedItems.indexOf( val );
        if( event.target.checked ){
            if( index == -1 ) _checkedItems.push( val );
        }else{
            if( index > -1 ) _checkedItems.splice( index, 1 );
        }
        updateCheckedItemsAction( _checkedItems );
    }

    deleteWithdraw( e, withdraw_id ){
        e.preventDefault();
        const con = confirm( nbdl.langs.confirm_delete_withdraw );
        if( con ){
            this.props.deleteWithdraw( withdraw_id );
        }
    }

    getDisplayStatus( status ){
        let _status;
        switch( status ){
            case 0:
                _status = nbdl.langs.pending;
                break;
            case 1:
                _status = nbdl.langs.approved;
                break;
            case 2:
                _status = nbdl.langs.cancelled;
                break;
        }
        return _status;
    }

    _openModal(){
        const { openModal, setModalContent, withdraw } = this.props;
        setModalContent(<NoteForm withdraw={withdraw} />);
        openModal();
    }

    render(){

        const { withdraw, toggleStatusAction } = this.props;

        var statusClass = classNames(
            rowStyles.status,
            {
                [rowStyles.pending]: withdraw.status == 0,
                [rowStyles.approved]: withdraw.status == 1,
                [rowStyles.cancelled]: withdraw.status == 2
            }
        );

        return(
            <tr>
                <th className="check-column">
                    <input ref={this.checkboxRef} type="checkbox" name="item[]" value={withdraw.id} onChange={(event) => this.updatecheckeItems(event)}/>
                </th>
                <td className="column artist_name">
                    <img src={withdraw.user.gravatar} width="50" className={rowStyles.avatar} />
                    <strong>
                        <a href={`#/designer/${withdraw.user.id}/edit`}>{withdraw.user.artist_name != '' ? withdraw.user.artist_name : nbdl.langs.no_name }</a>
                    </strong>
                    {withdraw.status != 1 && <div className="row-actions">
                        <span className="trash">
                            <a href="#" onClick={(e) => this.deleteWithdraw(e, withdraw.id)}>{ nbdl.langs.delete }</a>
                        </span>
                    </div>}
                </td>
                <td className="column amount" dangerouslySetInnerHTML={{__html: withdraw.amount}}></td>
                <td className="column status" >
                    <span className={statusClass}>{this.getDisplayStatus( withdraw.status )}</span>
                </td>
                <td className="column note" >
                    {withdraw.note}
                </td>
                <td className="column date" >
                    {withdraw.date}
                </td>
                <td className="column action" >
                    <div className="button-group">
                        {withdraw.status != 1 && <React.Fragment>
                            {withdraw.status != 1 && <button title={nbdl.langs.approve_request} className="button button-small" 
                                onClick={() => toggleStatusAction( withdraw.id, 'status', 1 )}>
                                <span className="dashicons dashicons-yes"></span>
                            </button>}
                            {withdraw.status != 0 && <button title={nbdl.langs.pending_request} className="button button-small"
                                onClick={() => toggleStatusAction( withdraw.id, 'status', 0 )}>
                                <span className="dashicons dashicons-backup"></span>
                            </button>}
                            {withdraw.status != 2 && <button title={nbdl.langs.cancel_request} className="button button-small"
                                onClick={() => toggleStatusAction( withdraw.id, 'status', 2 )}>
                                <span className="dashicons dashicons-no-alt"></span>
                            </button>}
                        </React.Fragment>}
                        <button title={nbdl.langs.add_note} className="button button-small" onClick={() => this._openModal()}>
                            <span className="dashicons dashicons-testimonial"></span>
                        </button>
                    </div>
                </td>
            </tr>
        )
    }
}

Row.propTypes = {
    withdraw: PropTypes.object.isRequired,
    checkedItems: PropTypes.array,
    updateCheckedItemsAction: PropTypes.func,
    toggleStatusAction: PropTypes.func,
    deleteWithdraw: PropTypes.func,
    openModal: PropTypes.func,
    setModalContent: PropTypes.func
}

const mapStateToProps = state => ({
    checkedItems: state.withdraws.checkedItems
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    openModal: () => dispatch( openModal() ),
    setModalContent: content => dispatch( setModalContent( content ) ),
    updateCheckedItemsAction: items => dispatch( updateCheckedItemsAction(items) ),
    toggleStatusAction: ( withdraw_id, type, value ) => dispatch( toggleStatusAction( withdraw_id, type, value, ownProps.history ) ),
    deleteWithdraw: withdraw_id => dispatch( deleteWithdraw( withdraw_id ) )
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Row));