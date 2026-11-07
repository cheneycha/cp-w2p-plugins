import React, { Component } from "react";
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import rowStyles from './row.css';
import { updateCheckedItemsAction, toggleStatusAction } from '../../actions/designers';

class Row extends Component {
    toggleStatus(event, designer_id, type){
        const value = event.target.checked ? 'on' : '';
        this.props.toggleStatusAction( designer_id, type, value );
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
    getLinkEditUser( designer_id ){
        return nbdl.edit_user_link.replace('replace_user_id', designer_id);
    }

    render(){
        const { designer } = this.props;
        return (
            <tr>
                <th className="check-column">
                    <input type="checkbox" name="item[]" value={designer.id} onChange={(event) => this.updatecheckeItems(event)}/>
                </th>
                <td className="column artist_name column-primary">
                    <img src={designer.gravatar} width="50" className={rowStyles.avatar} />
                    <strong>
                        <a href={this.getLinkEditUser(designer.id)}>{designer.artist_name != '' ? designer.artist_name : nbdl.langs.no_name }</a>
                    </strong>
                    <div className="row-actions">
                        <span className="edit">
                            <a href={`#/designer/${designer.id}/edit`}>{ nbdl.langs.edit }</a>
                        </span>&nbsp;|&nbsp;
                        <span className="view">
                            <a href={`#/designer/${designer.id}`}>{ nbdl.langs.view }</a>
                        </span>
                    </div>
                </td>
                <td className="column email">
                    <a href={`mailto:${designer.email}`}>{designer.email}</a>
                </td>
                <td className="column registered">
                    {designer.registered}
                </td>
                <td className="column featured">
                    <label>
                        <input type="checkbox" checked={designer.featured} onChange={(event) => this.toggleStatus(event, designer.id, 'featured')} />
                    </label>
                </td>
                <td className="column enabled">
                    <label>
                        <input type="checkbox" checked={designer.enabled} onChange={(event) => this.toggleStatus(event, designer.id, 'enabled')} />
                    </label>
                </td>
            </tr>
        );
    }
}

Row.propTypes = {
    designer: PropTypes.object.isRequired,
    checkedItems: PropTypes.array,
    updateCheckedItemsAction: PropTypes.func,
    toggleStatusAction: PropTypes.func
}

const mapStateToProps = state => ({
    checkedItems: state.designers.checkedItems
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    updateCheckedItemsAction: items => dispatch( updateCheckedItemsAction(items) ),
    toggleStatusAction: ( designer_id, type, value ) => dispatch( toggleStatusAction( designer_id, type, value ) )
});

export default connect(mapStateToProps, mapDispatchToProps)(Row);