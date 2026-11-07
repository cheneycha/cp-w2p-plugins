
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="nbdl-featured-designers">
    <?php 
        foreach( $designers as $designer ): 
            $name           = $designer['artist_name'] != '' ? $designer['artist_name'] : $designer['first_name'] . ' ' . $designer['last_name'];
            $link_designer  = add_query_arg( array( 'id' => $designer['id'] ), getUrlPageNBD( 'designer' ) );
            $avatar_url     = $designer['gravatar'] ? $designer['gravatar'] : NBDESIGNER_ASSETS_URL . 'images/avatar.png';
    ?>
    <div class="nbdl-featured-designer">
        <div class="nbdl-designer-wrap">
            <div class="nbdl-designer-header"
            <?php if( $designer['artist_banner'] ): ?>
                style="background: url('<?php echo $designer['artist_banner']; ?>') no-repeat center; background-size: cover;"
            <?php endif; ?>
            ></div>
            <div class="nbdl-designer-content">
                <div class="nbdl-designer-name"><a href="<?php echo $link_designer; ?>"><?php echo $name; ?></a></div>
                <?php if( $designer['artist_facebook'] != '' ): ?>
                <div class="nbdl-designer-info">
                    <a href="<?php echo $designer['artist_facebook']; ?>">
                        <span>
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="#3b5998" d="M13 10h3v3h-3v7h-3v-7h-3v-3h3v-1.255c0-1.189 0.374-2.691 1.118-3.512 0.744-0.823 1.673-1.233 2.786-1.233h2.096v3h-2.1c-0.498 0-0.9 0.402-0.9 0.899v2.101z"></path>
                            </svg>
                        </span>
                        <?php echo $designer['artist_facebook']; ?>
                    </a>
                </div>
                <?php endif; ?>
                <?php if( $designer['artist_twitter'] != '' ): ?>
                <div class="nbdl-designer-info">
                    <a href="<?php echo $designer['artist_twitter']; ?>">
                        <span>
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="#1da1f2" d="M18.89 7.012c0.808-0.496 1.343-1.173 1.605-2.034-0.786 0.417-1.569 0.703-2.351 0.861-0.703-0.756-1.593-1.14-2.66-1.14-1.043 0-1.924 0.366-2.643 1.078-0.715 0.717-1.076 1.588-1.076 2.605 0 0.309 0.039 0.585 0.117 0.819-3.076-0.105-5.622-1.381-7.628-3.837-0.34 0.601-0.51 1.213-0.51 1.846 0 1.301 0.549 2.332 1.645 3.089-0.625-0.053-1.176-0.211-1.645-0.47 0 0.929 0.273 1.705 0.82 2.388 0.549 0.676 1.254 1.107 2.115 1.291-0.312 0.080-0.641 0.118-0.979 0.118-0.312 0-0.533-0.026-0.664-0.083 0.23 0.757 0.664 1.371 1.291 1.841 0.625 0.472 1.344 0.721 2.152 0.743-1.332 1.045-2.855 1.562-4.578 1.562-0.422 0-0.721-0.006-0.902-0.038 1.697 1.102 3.586 1.649 5.676 1.649 2.139 0 4.029-0.542 5.674-1.626 1.645-1.078 2.859-2.408 3.639-3.974 0.784-1.564 1.172-3.192 1.172-4.892v-0.468c0.758-0.57 1.371-1.212 1.84-1.921-0.68 0.293-1.383 0.492-2.11 0.593z"></path>
                            </svg>
                        </span>
                        <?php echo $designer['artist_twitter']; ?>
                    </a>
                </div>
                <?php endif; ?>
                <?php if( $designer['artist_instagram'] != '' ): ?>
                <div class="nbdl-designer-info">
                    <a href="<?php echo $designer['artist_instagram']; ?>">
                        <span>
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="#e4405f" d="M17 1h-10c-3.3 0-6 2.7-6 6v10c0 3.3 2.7 6 6 6h10c3.3 0 6-2.7 6-6v-10c0-3.3-2.7-6-6-6zM21 17c0 2.2-1.8 4-4 4h-10c-2.2 0-4-1.8-4-4v-10c0-2.2 1.8-4 4-4h10c2.2 0 4 1.8 4 4v10z"></path>
                                <path fill="#e4405f" d="M12.8 7c-0.5-0.1-1-0.1-1.5 0-2.7 0.4-4.6 3-4.2 5.7 0.2 1.3 0.9 2.5 2 3.3 0.9 0.6 1.9 1 3 1 0.2 0 0.5 0 0.7-0.1 1.3-0.2 2.5-0.9 3.3-2s1.1-2.4 0.9-3.7c-0.3-2.2-2-3.9-4.2-4.2zM14.5 13.7c-0.5 0.6-1.2 1.1-2 1.2-1.6 0.2-3.2-0.9-3.4-2.5-0.3-1.6 0.9-3.2 2.5-3.4 0.1 0 0.3 0 0.4 0s0.3 0 0.4 0c1.3 0.2 2.3 1.2 2.5 2.5 0.2 0.8 0 1.6-0.4 2.2z"></path>
                                <path fill="#e4405f" d="M16.8 5.8c-0.2 0.2-0.3 0.4-0.3 0.7s0.1 0.5 0.3 0.7c0.2 0.2 0.5 0.3 0.7 0.3 0.3 0 0.5-0.1 0.7-0.3s0.3-0.5 0.3-0.7c0-0.3-0.1-0.5-0.3-0.7-0.4-0.4-1-0.4-1.4 0z"></path>
                            </svg>
                        </span>
                        <?php echo $designer['artist_instagram']; ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <div class="nbdl-designer-footer">
                <a href="<?php echo $link_designer; ?>" class="nbdl-designer-link"><?php esc_html_e( 'Visit gallery', 'web-to-print-online-designer' ); ?></a>
                <span class="nbdl-designer-avatar">
                    <img src="<?php echo $avatar_url; ?>" />
                </span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>