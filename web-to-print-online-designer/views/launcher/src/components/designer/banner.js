import React, { Component } from "react";
import styles from './banner.css';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import classNames from 'classnames';

import { changeInfo } from '../../actions/designer';
import { uploadMedia, addUrlParam } from '../../util';

class Banner extends Component {
    _changeInfo( e, type ){
        const value = e.target.value;
        this.props.changeInfo({ [type]: value });
    }
    changeBanner(){
        if( !this.props.editMode ) return;
        const _changeBanner = image => {
            this.props.changeInfo({ artist_banner: image.url, artist_banner_id: image.id });
        }
        uploadMedia( nbdl.banner_width, nbdl.banner_height, _changeBanner );
    }
    getBannerStyle(){
        const width = parseInt( nbdl.banner_width ), height = parseInt( nbdl.banner_height );
        return {
            'paddingBottom': height / width * 100 + '%'
        }
    }
    render(){

        const { infos, editMode } = this.props;

        return (
            <div className={styles.banner} >
                <div className={styles['banner-inner']} style={this.getBannerStyle()} onClick={() => this.changeBanner()}>
                    <div className={classNames(styles['image-wrap'], {[styles['in-view']]: !editMode})}>
                        {infos.artist_banner && <img src={infos.artist_banner} />}
                    </div>
                    {editMode ? <div className={styles['change-banner']}>{nbdl.langs.change_banner}</div> : (
                        <div className={styles.top_actions}>
                            <a className="button button-primary" target="_blank" href={addUrlParam(nbdl.designer_url, 'id', infos.id)}>{nbdl.langs.view_gallery}</a>
                            <a className={classNames("button", styles.edit_btn)} href={`#/designer/${infos.id}/edit`}><span className="dashicons dashicons-edit"></span></a>
                        </div>
                    )}
                </div>
            </div>
        )
    }
}

Banner.propTypes = {
    infos: PropTypes.object,
    changeInfo: PropTypes.func
};

const mapStateToProps = state => ({
    infos: state.designer.infos
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    changeInfo: infos => dispatch( changeInfo( infos ) )
});

export default connect(mapStateToProps, mapDispatchToProps)(Banner);