import React, { Component } from "react";
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import classNames from 'classnames';
import { updateCheckedItemsAction } from '../../actions/designers';

import Row from './row';

class List extends Component {
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
        const { designers, sortBy, order } = this.props;

        const row = (
            <tr>
                <td className="manage-column column-cb check-column">
                    <input type="checkbox" onClick={(event) => this.updatecheckeItems(event)} />
                </td>
                <th className="column artist_name column-primary">
                    {nbdl.langs.artist_name}
                </th>
                <th className="column email">
                    {nbdl.langs.email}
                </th>
                <th className={classNames("column registered sortable sorted", order)}>
                    <a href="#" onClick={event => sortBy(event, 'registered')}>
                        <span>{nbdl.langs.registered}</span>
                        <span className="sorting-indicator"></span>
                    </a>
                </th>
                <th className="column featured">
                    {nbdl.langs.featured}
                </th>
                <th className="column status">
                    {nbdl.langs.status}
                </th>
            </tr>
        );

        return (
            <table className="wp-list-table widefat fixed striped">
                <thead>
                    {row}
                </thead>
                <tbody>
                    {designers.map(designer => (
                        <Row key={designer.id} designer={designer} />
                    ))}
                    {designers.length == 0 && (
                        <tr>
                            <td colSpan="6">{nbdl.langs.no_designer_found}</td>
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
    designers: PropTypes.array.isRequired,
    checkedItems: PropTypes.array,
    order: PropTypes.string,
    updateCheckedItemsAction: PropTypes.func
}

const mapStateToProps = state => ({
    order: state.designerRouter.order,
    checkedItems: state.designers.checkedItems,
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    updateCheckedItemsAction: items => dispatch( updateCheckedItemsAction(items) )
});

export default connect(mapStateToProps, mapDispatchToProps)(List);