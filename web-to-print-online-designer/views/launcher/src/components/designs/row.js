import React, { Component } from "react";
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import classNames from 'classnames';
import rowStyles from './row.css';
import { updateCheckedItemsAction, toggleStatusAction, deleteDesign } from '../../actions/designs';

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

    deleteDesign( e, design_id ){
        e.preventDefault();
        const con = confirm( nbdl.langs.confirm_delete_design );
        if( con ){
            this.props.deleteDesign( design_id );
        }
    }

    getDisplayStatus( status ){
        let _status;
        switch( status ){
            case 0:
                _status = nbdl.langs.pending;
                break;
            case 1:
                _status = nbdl.langs.publish;
                break;
            case 2:
                _status = nbdl.langs.cancelled;
                break;
        }
        return _status;
    }

    render(){

        const { design, toggleStatusAction } = this.props;

        var statusClass = classNames(
            rowStyles.status,
            {
                [rowStyles.pending]: design.status == 0,
                [rowStyles.approved]: design.status == 1
            }
        );

        return(
            <tr>
                <th className="check-column">
                    <input ref={this.checkboxRef} type="checkbox" name="item[]" value={design.id} onChange={(event) => this.updatecheckeItems(event)}/>
                </th>
                <td className="column template_preview column-primary">
                    {design.previews.map(preview => <img key={preview.toString()} src={preview} className={rowStyles.design_preview}/>)}
                </td>
                <td className={classNames("column", rowStyles.column_designer)}>
                    {design.user.artist_name != '' ? design.user.artist_name : nbdl.langs.no_name }
                </td>
                <td className={classNames("column", rowStyles.column_status)} >
                    <span className={statusClass}>{this.getDisplayStatus( design.status )}</span>
                    {design.type == 'solid' ? <span className={classNames(rowStyles.status, rowStyles.solid)}>{nbdl.langs.solid}</span> : <span className={classNames(rowStyles.status, rowStyles.editable)}>{nbdl.langs.editable}</span>}
                </td>
                <td className="column product" >
                    <a href={design.product.link.replace(/&amp;/g, '&')}>{design.product.name}</a>
                </td>
                <td className="column date" >
                    {design.date}
                </td>
                <td className={classNames("column", rowStyles.actions)} >
                    <div className="button-group">
                        {design.type != 'solid' ? (
                        <a href={`${nbdl.edit_design_link}&product_id=${design.product.product_id}&nbd_item_key=${design.folder}`} target="_blank">
                            <button title={nbdl.langs.edit} className="button button-small">
                                <span className="dashicons dashicons-edit"></span>
                            </button>
                        </a>
                        ) : (
                        <a href={`${nbdl.download_design_link}/${design.resource}/design.zip`} target="_blank">
                            <button title={nbdl.langs.download_resource} className="button button-small">
                                <span className="dashicons dashicons-download"></span>
                            </button>
                        </a>)}
                    </div>
                </td>
            </tr>
        )
    }
}

Row.propTypes = {
    design: PropTypes.object.isRequired,
    checkedItems: PropTypes.array,
    updateCheckedItemsAction: PropTypes.func,
    toggleStatusAction: PropTypes.func,
    deleteDesign: PropTypes.func
}

const mapStateToProps = state => ({
    checkedItems: state.designs.checkedItems
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    updateCheckedItemsAction: items => dispatch( updateCheckedItemsAction(items) ),
    toggleStatusAction: ( design_id, type, value ) => dispatch( toggleStatusAction( design_id, type, value, ownProps.history ) ),
    deleteDesign: design_id => dispatch( deleteDesign( design_id ) )
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Row));