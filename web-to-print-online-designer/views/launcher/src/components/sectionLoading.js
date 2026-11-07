import React, { Component } from "react";
import styles from './sectionLoading.css';

class SectionLoading extends Component {
    render(){

        return (
            <div className={styles.loading}>
                <img src={`${nbdl.assets_images_url}spinner.svg`} />
            </div>
        )
    }
}

export default SectionLoading;