<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="nbc-popup-panel faq" ng-class="frontendLayout.activeNav == 'faq' ? 'active' : ''">
    <div class="nbc-faq-item" 
        ng-click="browserFaqCat(cat)"
        data-delay="dl-{{$index % 10}}"
        ng-class="cat.type" ng-repeat="cat in frontendLayout.faq.categories | faqFilter: {search: frontendLayout.faq.search, currentCatId: frontendLayout.faq.currentCatId}">
        <div class="nbc-faq-item-icon">
            <svg ng-if="cat.type == 'cat'" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24" viewBox="0 0 24 24" width="24"><g><rect fill="none" height="24" width="24"/><path fill="#404762" d="M20,6h-8l-2-2H4C2.9,4,2.01,4.9,2.01,6L2,18c0,1.1,0.9,2,2,2h16c1.1,0,2-0.9,2-2V8C22,6.9,21.1,6,20,6z M14,16H6v-2h8V16z M18,12H6v-2h12V12z"/></g></svg>
            <svg ng-if="cat.type != 'cat'" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24" viewBox="0 0 24 24" width="24"><g><rect fill="none" height="24" width="24"/><path fill="#404762" d="M20.41,8.41l-4.83-4.83C15.21,3.21,14.7,3,14.17,3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h14c1.1,0,2-0.9,2-2V9.83 C21,9.3,20.79,8.79,20.41,8.41z M7,7h7v2H7V7z M17,17H7v-2h10V17z M17,13H7v-2h10V13z"/></g></svg>
        </div>
        <div class="nbc-faq-item-title-wrap">
            <div class="nbc-faq-item-title" ng-bind-html="cat.title | message_trusted"></div>
            <div ng-if="cat.type == 'cat'" class="nbc-faq-item-desc" ng-bind-html="cat.desc | message_trusted"></div>
        </div>
    </div>
    <div class="nbc-faq-article-content" ng-bind-html="frontendLayout.faq.articleContent | message_trusted"></div>
    <div class="nbc-faq-vote" ng-if="frontendLayout.faq.articleContent != ''">
        <div class="nbc-faq-vote-intro"><?php esc_html_e('Was this article helpful?', 'web-to-print-online-designer'); ?></div>
        <div class="nbc-faq-vote-actions">
            <span ng-click="voteFaq('up')">😃</span>
            <span ng-click="voteFaq('down')">😞</span>
        </div>
    </div>
    <div ng-if="frontendLayout.faq.loading" class="nbc-faq-loading-wrap">
        <div class="nbc-faq-loading"></div>
        <div class="nbc-faq-loading dl2"></div>
        <div class="nbc-faq-loading dl4"></div>
    </div>
</div>