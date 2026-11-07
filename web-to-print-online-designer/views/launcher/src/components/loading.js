import React, { Component } from "react";
import classNames from 'classnames';
import styles from './loading.css';

class Loading extends Component {
    render(){
        const { loading } = this.props;

        return (
            <div className={classNames(styles.loading, {[styles.active]: loading})}>
                <img src={`${nbdl.assets_images_url}/spinner.svg`} />
            </div>
        )
    }
}

export default Loading;