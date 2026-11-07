import React, { Component } from "react";
import styles from './infos.css';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';

import { changeInfo } from '../../actions/designer';

class Infos extends Component {
    _changeInfo( e, type, subtype, subsubtype ){
        const value = e.target.value,
        infos = JSON.parse( JSON.stringify( this.props.infos ) );
        if( typeof subsubtype != 'undefined' ){
            infos[type][subtype][subsubtype] = value;
            this.props.changeInfo(infos);
        } else if( typeof subtype != 'undefined' ){
            infos[type][subtype] = value;
            this.props.changeInfo(infos);
        }else{
            infos[type] = value;
            this.props.changeInfo(infos);
        }
    }
    render(){

        const { infos } = this.props;

        return (
            <div>
                <div className={styles['contact-infos']}>
                    <div className={styles['contact-infos-column']}>
                        <div className={styles.header}>
                            {nbdl.langs.address}
                        </div>
                        <div className={styles['contact-infos-column-inner']}>
                            <div className={styles['contact-info']}>
                                <label>{nbdl.langs.address}</label>
                                <div>
                                    <input type="text" defaultValue={infos.artist_address} onChange={event => this._changeInfo(event, 'artist_address')}/>
                                </div>
                            </div>
                            <div className={styles['contact-info']}>
                                <label>{nbdl.langs.phone_number}</label>
                                <div>
                                    <input type="text" defaultValue={infos.artist_phone} onChange={event => this._changeInfo(event, 'artist_phone')}/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className={styles['contact-infos-column']}>
                        <div className={styles.header}>
                            {nbdl.langs.social_information}
                        </div>
                        <div className={styles['contact-infos-column-inner']}>
                            {['facebook', 'twitter', 'linkedin', 'youtube', 'instagram', 'flickr'].map(network => (
                                <div className={styles['contact-info']} key={network}>
                                    <label>{nbdl.langs[network]}</label>
                                    <div>
                                        <input type="text" defaultValue={infos['artist_' + network]} onChange={event => this._changeInfo(event, 'artist_' + network)}/>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
                <div className={styles['contact-infos']}>
                    <div className={styles['contact-infos-column']}>
                        <div className={styles.header}>
                            {nbdl.langs.payment}
                        </div>
                        <div className={styles['contact-infos-column-inner']}>
                            <div className={styles['contact-info']}>
                                <label>{nbdl.langs.payment_info}</label>
                                <div>
                                    <textarea onChange={event => this._changeInfo(event, 'payment')} value={infos.payment} rows="3"></textarea>
                                </div>
                            </div>
                            <div className={styles['contact-info']}>
                                <label>{nbdl.langs.designer_commission_type}</label>
                                <div>
                                    <select onChange={event => this._changeInfo(event, 'artist_commission_type')} value={infos.artist_commission_type}>
                                        <option value="flat" >{nbdl.langs.flat}</option>
                                        <option value="percentage" >{nbdl.langs.percentage}</option>
                                        <option value="combine" >{nbdl.langs.combine}</option>
                                    </select>
                                </div>
                            </div>
                            {infos.artist_commission_type != 'combine' ? (
                            <div className={styles['contact-info']}>
                                <label>{nbdl.langs.designer_commission}</label>
                                <div>
                                    <input type="text" defaultValue={infos.artist_commission} onChange={event => this._changeInfo(event, 'artist_commission')} />
                                </div>
                            </div>) : (
                            <div className={styles['contact-info']}>
                                <label>{nbdl.langs.designer_commission}</label>
                                <div>
                                    {infos.artist_commission2 ? <input type="number" step="any" 
                                        defaultValue={infos.artist_commission2[0]} onChange={event => this._changeInfo(event, 'artist_commission2', 0)}
                                        className={styles['commission-combine']}/> : null}
                                    {' ' + nbdl.langs.combine_text + ' '}
                                    {infos.artist_commission2 ? <input type="number" step="any" 
                                        defaultValue={infos.artist_commission2[1]} onChange={event => this._changeInfo(event, 'artist_commission2', 1)}
                                        className={styles['commission-combine']}/> : null}
                                </div>
                            </div>)}
                        </div>
                    </div>
                    <div className={styles['contact-infos-column']}>
                        <div className={styles.header}>
                            {nbdl.langs.status}
                        </div>
                        <div className={styles['contact-infos-column-inner']}>
                            <div className={styles['contact-info']}>
                                <label>
                                    <input type="checkbox" defaultChecked={infos.enabled} onChange={event => this._changeInfo(event, 'enabled')} /> {nbdl.langs.enable_selling_designs}
                                </label>
                            </div>
                            <div className={styles['contact-info']}>
                                <label>
                                    <input type="checkbox" defaultChecked={infos.featured} onChange={event => this._changeInfo(event, 'featured')} /> {nbdl.langs.make_mesigner_featured}
                                </label>
                            </div>
                            <div className={styles['contact-info']}>
                                <label>
                                    <input type="checkbox" defaultChecked={infos.auto_approve_design} onChange={event => this._changeInfo(event, 'auto_approve_design')}/> {nbdl.langs.auto_publish_new_design}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
        )
    }
}

Infos.propTypes = {
    infos: PropTypes.object,
    changeInfo: PropTypes.func
};

const mapStateToProps = state => ({
    infos: state.designer.infos
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    changeInfo: infos => dispatch( changeInfo( infos ) )
});

export default connect(mapStateToProps, mapDispatchToProps)(Infos);