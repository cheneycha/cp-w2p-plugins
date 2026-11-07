import React, { Component } from 'react';
import { NavLink } from 'react-router-dom';
import styles from './nav.css';

class NavBarLink extends Component {
    render() {
        return(
            <NavLink className="nav-tab" {...this.props} activeClassName={styles.active}>
                {this.props.children}
            </NavLink>
        );
    }
}

export default NavBarLink;