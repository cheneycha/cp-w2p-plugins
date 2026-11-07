import designers from './designers';
import designerRouter from './designerRouter';
import notification from './notification';
import designer from './designer';
import designs from './designs';
import withdraws from './withdraw';
import modal from './modal';
import withdrawFilter from './withdrawFilter';
import designFilter from './designFilter';
import summary from './summary';
import overview from './overview';
import { combineReducers } from 'redux';

const allReducers = combineReducers({
    designers,
    designerRouter,
    notification,
    designer,
    designs,
    withdraws,
    modal,
    withdrawFilter,
    designFilter,
    summary,
    overview
});

export default allReducers;