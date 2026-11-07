<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="pop-tools shadow active" ng-show="currentLayers.length > 0">
    <h2><i class="fa fa-arrows" aria-hidden="true"></i><span>{{(langs['TOOL']) ? langs['TOOL'] : "Tools"}}</span></h2>
    <div class="tools-con">
        <span class="fa fa-chevron-left" ng-click="ShiftLeft()" title="{{(langs['MOVE_LEFT']) ? langs['MOVE_LEFT'] : 'Move left'}}"></span>      
        <span class="fa fa-chevron-right" ng-click="ShiftRight()" title="{{(langs['MOVE_RIGHT']) ? langs['MOVE_RIGHT'] : 'Move right'}}"></span>  
        <span class="fa fa-chevron-up" ng-click="ShiftUp()" title="{{(langs['MOVE_UP']) ? langs['MOVE_UP'] : 'Move up'}}"></span>
        <span class="fa fa-chevron-down" ng-click="ShiftDown()" title="{{(langs['MOVE_DOWN']) ? langs['MOVE_DOWN'] : 'Move down'}}"></span>      
        <span class="fa fa-exchange" ng-click="flipVertical()" title="{{(langs['FLIP_HORIZONTAL']) ? langs['FLIP_HORIZONTAL'] : 'Flip Horizontal'}}"></span>      
        <span class="fa fa-exchange rotate90" ng-click="flipHorizontal()" title="{{(langs['FLIP_VERTICAL']) ? langs['FLIP_VERTICAL'] : 'Flip Vertical'}}"></span>      
        <span class="glyphicon glyphicon-object-align-vertical" ng-click="setHorizontalCenter()" title="{{(langs['CENTER_HORIZONTAL']) ? langs['CENTER_HORIZONTAL'] : 'Center Horizontal'}}"></span>      
        <span class="glyphicon glyphicon-object-align-horizontal" ng-click="setVerticalCenter()" title="{{(langs['CENTER_VERTICAL']) ? langs['CENTER_VERTICAL'] : 'Center Vertical'}}"></span>   
        <span class="fa fa-trash-o" onclick="deleteObject()" title="{{(langs['DELETE']) ? langs['DELETE'] : 'Delete'}}"></span>      
        <span class="fa fa-files-o" ng-click="duplicateItem()" title="{{(langs['COPY']) ? langs['COPY'] : 'Copy'}}"></span>      
        <span class="fa fa fa-plus" ng-click="scaleItem('+')" title="{{(langs['ZOOM_IN']) ? langs['ZOOM_IN'] : 'Zoom In'}}"></span>      
        <span class="fa fa fa-minus" ng-click="scaleItem('-')" title="{{(langs['ZOOM_OUT']) ? langs['ZOOM_OUT'] : 'Zoom Out'}}"></span>   
        <span ng-click="setStackPosition('bringToFront')" title="{{(langs['BRING_TO_FRONT']) ? langs['BRING_TO_FRONT'] : 'Bring To Front'}}">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" fit="" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" focusable="false">
                    <g id="bring-front">
                        <path d="M2,2H11V6H9V4H4V9H6V11H2V2M22,13V22H13V18H15V20H20V15H18V13H22M8,8H16V16H8V8Z"></path>
                    </g>
                </svg>  
            </span>   
        </span>
        <span ng-click="setStackPosition('bringForward')" title="{{(langs['BRING_FORWARD']) ? langs['BRING_FORWARD'] : 'Bring Forward'}}">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" fit="" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" focusable="false">
                    <g id="bring-forward">
                        <path d="M2,2H16V16H2V2M22,8V22H8V18H10V20H20V10H18V8H22Z"></path>
                    </g>
                </svg>                
            </span>
        </span>
        <span ng-click="setStackPosition('sendBackwards')" title="{{(langs['SEND_BACKWARD']) ? langs['SEND_BACKWARD'] : 'Send Backward'}}">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" fit="" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" focusable="false">
                    <g id="send-backward">
                        <path d="M2,2H16V16H2V2M22,8V22H8V18H18V8H22M4,4V14H14V4H4Z"></path>
                    </g>
                </svg> 
            </span>    
        </span>
        <span ng-click="setStackPosition('sendToBack')" title="{{(langs['SEND_TO_BACK']) ? langs['SEND_TO_BACK'] : 'Send To Back'}}">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" fit="" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" focusable="false"
                    <g id="send-back">
                        <path d="M2,2H11V11H2V2M9,4H4V9H9V4M22,13V22H13V13H22M15,20H20V15H15V20M16,8V11H13V8H16M11,16H8V13H11V16Z"></path>
                    </g>
                </svg>                
            </span>
        </span>
    </div>
</div>


