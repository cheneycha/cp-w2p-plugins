import React, { Component } from "react";
import styles from './leftSide.css';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';

import { changeInfo } from '../../actions/designer';
import { uploadMedia } from '../../util';

class LeftSide extends Component {
    _changeInfo( e, type ){
        const value = e.target.value;
        this.props.changeInfo({ [type]: value });
    }
    changeAvatar(){
        const _changeAvatar = image => {
            this.props.changeInfo({ gravatar: image.url, gravatar_id: image.id });
        }
        uploadMedia( 100, 100, _changeAvatar );
    }
    render(){
        
        const { infos } = this.props;

        return (
            <div className={styles.profile}>
                <div className={styles['profile-icon']} onClick={() => this.changeAvatar()}>
                    <div className={styles['upload-image']}>
                        <img className={styles.avatar} src={infos.gravatar} />
                    </div>
                    <div className={styles['edit-photo']}>
                        {nbdl.langs.change_designer_avatar}
                    </div>
                </div>
                <div>
                    <div className={styles['account-info']}>
                        <div className={styles['account-info-left']}>
                            {nbdl.langs.artist_name}
                        </div>
                        <div className={styles['account-info-right']}>
                            <input type="text" defaultValue={infos.artist_name} onChange={event => this._changeInfo(event, 'artist_name')}/>
                        </div>
                    </div>
                    <div className={styles['account-info']}>
                        <div className={styles['account-info-left']}>
                            {nbdl.langs.email}
                        </div>
                        <div className={styles['account-info-right']}>
                            <input type="text" defaultValue={infos.email} onChange={event => this._changeInfo(event, 'email')}/>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}

LeftSide.propTypes = {
    infos: PropTypes.object,
    changeInfo: PropTypes.func
};

const mapStateToProps = state => ({
    infos: state.designer.infos
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    changeInfo: infos => dispatch( changeInfo( infos ) )
});

export default connect(mapStateToProps, mapDispatchToProps)(LeftSide);