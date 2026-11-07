import React, { Component } from "react";
import { NavLink, withRouter } from 'react-router-dom';

class Filter extends Component {
    render(){
        const { children, filter, count, list, last } = this.props;
        return (
            <li>
                <NavLink 
                    exact={true}
                    to={{ pathname: `/${list}`, search: filter === 'all' ? '' : `?status=${filter}`}}
                    activeStyle={{
                        color: '#0073aa'
                    }}
                    isActive={(match, location) => {
                        if( location.search === '') return filter === 'all';
                        return location.search == `?status=${filter}`;
                    }}
                    className="current"
                >
                    {children}
                </NavLink>
                <span className="count">({count})</span>{!last && ' | '}
            </li>
        );
    }
}

export default withRouter( Filter );