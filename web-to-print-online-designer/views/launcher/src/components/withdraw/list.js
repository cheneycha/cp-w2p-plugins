import React, { Component } from "react";
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { updateCheckedItemsAction } from '../../actions/withdraws';

import Row from './row';

class List extends Component {

    componentDidMount() {
        const checkboxes = document.querySelectorAll('.column-cb input');
        for ( let i = 0; i < checkboxes.length; i++ ){
            checkboxes[i].checked = false;
        }
    }

    updatecheckeItems( event ){
        const { checkedItems, updateCheckedItemsAction } = this.props;
        let _checkedItems = JSON.parse( JSON.stringify( checkedItems ) );
        let isChecked = event.target.checked;

        setTimeout(() => {
            _checkedItems = [];
            if( isChecked ){
                let checkboxes = document.querySelectorAll('input[name="item[]"]');
                for( let i = 0; i< checkboxes.length; i++ ){
                    _checkedItems.push( checkboxes[i].value );
                }
            }
            updateCheckedItemsAction( _checkedItems );
        });
    }

    render(){
        const { withdraws } = this.props;

        const row = (
            <tr>
                <td className="manage-column column-cb check-column">
                    <input type="checkbox" onClick={(event) => this.updatecheckeItems(event)} />
                </td>
                <th className="column designer">
                    {nbdl.langs.designer}
                </th>
                <th className="column amount">
                    {nbdl.langs.amount}
                </th>
                <th className="column status">
                    {nbdl.langs.status}
                </th>
                <th className="column note">
                    {nbdl.langs.note}
                </th>
                <th className="column date">
                    {nbdl.langs.date}
                </th>
                <th className="column actions">
                    {nbdl.langs.actions}
                </th>
            </tr>
        );

        return (
            <table className="wp-list-table widefat fixed striped">
                <thead>
                    {row}
                </thead>
                <tbody>
                    {withdraws.map(withdraw => (
                        <Row key={withdraw.id} withdraw={withdraw} />
                    ))}
                    {withdraws.length == 0 && (
                        <tr>
                            <td colSpan="7">{nbdl.langs.no_transaction_found}</td>
                        </tr>
                    )}
                </tbody>
                <tfoot>
                    {row}
                </tfoot>
            </table>
        );
    }
}

List.propTypes = {
    withdraws: PropTypes.array.isRequired,
    checkedItems: PropTypes.array,
    updateCheckedItemsAction: PropTypes.func
}

const mapStateToProps = state => ({
    checkedItems: state.withdraws.checkedItems,
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    updateCheckedItemsAction: items => dispatch( updateCheckedItemsAction(items) )
});

export default connect(mapStateToProps, mapDispatchToProps)(List);