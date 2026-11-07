<div class="nbd-popup popup-keyboard" data-animate="fixed-top">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <i class="icon-nbd icon-nbd-clear close-popup"></i>
        <div class="head">
            <div class="nbd-tab-nav">
                <ul class="nbd-tabs">
                    <li class="nbd-tab active" data-tab="nbd-keyboard-shortcut">
                        <span class="title"><?php esc_html_e('Keyboard shortcuts','web-to-print-online-designer'); ?></span>
                    </li>
                    <li class="nbd-tab" data-tab="nbd-keyboard-about">
                        <span class="title"><?php esc_html_e('About','web-to-print-online-designer'); ?></span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="body">
            <div class="main-body">
                <div class="nbd-tab-contents">
                    <div class="tab-scroll">
                        <div id="nbd-keyboard-shortcut" class="nbd-tab-content active">
                            <table class="keyboard-mapping">
                                <tbody>
                                <!-- CODTeam task copy/paste -->
								<?php do_action('nbd_add_new_hotkey'); ?>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>A</kbd>
                                    </td>
                                    <td><?php esc_html_e('Select all layers','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>D</kbd>
                                    </td>
                                    <td><?php esc_html_e('Deselect all layers','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>E</kbd>
                                    </td>
                                    <td><?php esc_html_e('Clear all layers','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>Z</kbd>
                                    </td>
                                    <td><?php esc_html_e('Undo changes','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>Y</kbd>
                                    </td>
                                    <td><?php esc_html_e('Redo changes','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>L</kbd>
                                    </td>
                                    <td><?php esc_html_e('Show/hide Bleed line','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>R</kbd>
                                    </td>
                                    <td><?php esc_html_e('Show/hide Ruler','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>H</kbd>
                                    </td>
                                    <td><?php esc_html_e('Align layer horizontal center','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>+</kbd>
                                    </td>
                                    <td><?php esc_html_e('Zoom In stage','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>-</kbd>
                                    </td>
                                    <td><?php esc_html_e('Zoom Out stage','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>0</kbd>
                                    </td>
                                    <td><?php esc_html_e('Fit stage with viewport','web-to-print-online-designer'); ?></td>
                                </tr>   
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>1</kbd>
                                    </td>
                                    <td><?php esc_html_e('Resize stage to real size','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>]</kbd>
                                    </td>
                                    <td><?php esc_html_e('Bring layer forward','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>[</kbd>
                                    </td>
                                    <td><?php esc_html_e('Bring layer backward','web-to-print-online-designer'); ?></td>
                                </tr>  
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>O</kbd>
                                    </td>
                                    <td><?php esc_html_e('Load your designs','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>B</kbd>
                                    </td>
                                    <td><?php esc_html_e('Make your text bold','web-to-print-online-designer'); ?></td>
                                </tr> 
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>I</kbd>
                                    </td>
                                    <td><?php esc_html_e('Italicize your text','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>G</kbd>
                                    </td>
                                    <td><?php esc_html_e('Group layers','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>P</kbd>
                                    </td>
                                    <td><?php esc_html_e('Duplicate layers','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Alt</kbd> +<kbd>U</kbd>
                                    </td>
                                    <td><?php esc_html_e('Make your text UPPERCASE','web-to-print-online-designer'); ?></td>
                                </tr>  
                                <tr>
                                    <td class="keys">
                                        <kbd>Alt</kbd> +<kbd>L</kbd>
                                    </td>
                                    <td><?php esc_html_e('Make your text lowercase','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Alt</kbd> +<kbd>←</kbd>
                                    </td>
                                    <td><?php esc_html_e('Move the selected layers to left 1px','web-to-print-online-designer'); ?></td>
                                </tr> 
                                <tr>
                                    <td class="keys">
                                        <kbd>Alt</kbd> +<kbd>→</kbd>
                                    </td>
                                    <td><?php esc_html_e('Move the selected layers to right 1px','web-to-print-online-designer'); ?></td>
                                </tr>  
                                <tr>
                                    <td class="keys">
                                        <kbd>Alt</kbd> +<kbd>↑</kbd>
                                    </td>
                                    <td><?php esc_html_e('Move the selected layers to top 1px','web-to-print-online-designer'); ?></td>
                                </tr> 
                                <tr>
                                    <td class="keys">
                                        <kbd>Alt</kbd> +<kbd>↓</kbd>
                                    </td>
                                    <td><?php esc_html_e('Move the selected layers to bottom 1px','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Alt</kbd> +<kbd><i class="icon-nbd icon-nbd-arrows-v rotate-45 shortcut-free-transform"></i></kbd>
                                    </td>
                                    <td><?php esc_html_e('Free transform','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Shift</kbd> +<kbd>+</kbd>
                                    </td>
                                    <td><?php esc_html_e('Zoom out selected layers','web-to-print-online-designer'); ?></td>
                                </tr>    
                                <tr>
                                    <td class="keys">
                                        <kbd>Shift</kbd> +<kbd>-</kbd>
                                    </td>
                                    <td><?php esc_html_e('Zoom in selected layer','web-to-print-online-designer'); ?></td>
                                </tr>  
                                <tr>
                                    <td class="keys">
                                        <kbd>Shift</kbd> +<kbd>G</kbd>
                                    </td>
                                    <td><?php esc_html_e('Show/hide Grid','web-to-print-online-designer'); ?></td>
                                </tr> 
                                <tr>
                                    <td class="keys">
                                        <kbd>Shift</kbd> +<kbd>D</kbd>
                                    </td>
                                    <td><?php esc_html_e('Show/hide stage dimensions','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Shift</kbd> +<kbd>L</kbd>
                                    </td>
                                    <td><?php esc_html_e('Clear all guidelines','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Shift</kbd> +<kbd><i class="icon-nbd icon-nbd-arrows-v rotate-45 shortcut-free-transform"></i></kbd>
                                    </td>
                                    <td><?php esc_html_e('Centered scaling','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>←</kbd>
                                    </td>
                                    <td><?php esc_html_e('Move the selected layers to left 10px','web-to-print-online-designer'); ?></td>
                                </tr> 
                                <tr>
                                    <td class="keys">
                                        <kbd>→</kbd>
                                    </td>
                                    <td><?php esc_html_e('Move the selected layers to right 10px','web-to-print-online-designer'); ?></td>
                                </tr>  
                                <tr>
                                    <td class="keys">
                                        <kbd>↑</kbd>
                                    </td>
                                    <td><?php esc_html_e('Move the selected layers to top 10px','web-to-print-online-designer'); ?></td>
                                </tr> 
                                <tr>
                                    <td class="keys">
                                        <kbd>↓</kbd>
                                    </td>
                                    <td><?php esc_html_e('Move the selected layers to bottom 10px','web-to-print-online-designer'); ?></td>
                                </tr> 
                                <tr>
                                    <td class="keys">
                                        <kbd>Delete</kbd>
                                    </td>
                                    <td><?php esc_html_e('Delete selected layers','web-to-print-online-designer'); ?></td>
                                </tr> 
                                <tr>
                                    <td class="keys">
                                        <kbd>V</kbd>
                                    </td>
                                    <td><?php esc_html_e('Disable drawing mode','web-to-print-online-designer'); ?></td>
                                </tr>     
                                <tr>
                                    <td class="keys">
                                        <kbd>B</kbd>
                                    </td>
                                    <td><?php esc_html_e('Enable drawing mode','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Esc</kbd>
                                    </td>
                                    <td><?php esc_html_e('Quit text editing','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>Shift</kbd> +<kbd>]</kbd>
                                    </td>
                                    <td><?php esc_html_e('Bring layer to front','web-to-print-online-designer'); ?></td>
                                </tr> 
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>Shift</kbd> +<kbd>[</kbd>
                                    </td>
                                    <td><?php esc_html_e('Send layer to back','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>Shift</kbd> +<kbd>V</kbd>
                                    </td>
                                    <td><?php esc_html_e('Align layer vertical center','web-to-print-online-designer'); ?></td>
                                </tr> 
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>Shift</kbd> +<kbd>I</kbd>
                                    </td>
                                    <td><?php esc_html_e('Import Design','web-to-print-online-designer'); ?></td>
                                </tr>     
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>Shift</kbd> +<kbd>E</kbd>
                                    </td>
                                    <td><?php esc_html_e('Export Design','web-to-print-online-designer'); ?></td>
                                </tr>  
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>Shift</kbd> +<kbd>S</kbd>
                                    </td>
                                    <td><?php esc_html_e('Save Design for later','web-to-print-online-designer'); ?></td>
                                </tr>  
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>Shift</kbd> +<kbd>O</kbd>
                                    </td>
                                    <td><?php esc_html_e('Load Your Design in Cart','web-to-print-online-designer'); ?></td>
                                </tr>  
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>Shift</kbd> +<kbd>G</kbd>
                                    </td>
                                    <td><?php esc_html_e('Ungroup','web-to-print-online-designer'); ?></td>
                                </tr>
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>Shift</kbd> +<kbd>L</kbd>
                                    </td>
                                    <td><?php esc_html_e('Clear all stages','web-to-print-online-designer'); ?></td>
                                </tr>  
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>Shift</kbd> +<kbd>&lt;</kbd>
                                    </td>
                                    <td><?php esc_html_e('Decreate font size','web-to-print-online-designer'); ?></td>
                                </tr>  
                                <tr>
                                    <td class="keys">
                                        <kbd>Ctrl</kbd> +<kbd>Shift</kbd> +<kbd>&gt;</kbd>
                                    </td>
                                    <td><?php esc_html_e('Increate font size','web-to-print-online-designer'); ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="nbd-keyboard-about" class="nbd-tab-content">
                            <div class="text-center nbd-about-logo-wrap" >
                                <?php if(!$without_logo): ?>
                                <img src="<?php echo esc_url( $logo_url );?>" alt="online design">
                                <?php else: ?>
                                <?php echo get_bloginfo( 'name' ); ?>
                                <?php endif; ?>
                            </div>
                            <div class="copy-right">
                                <p class="text-center">Copyright © <script>document.write(new Date().getFullYear())</script>. <?php echo get_bloginfo( 'name' ); ?></p>
                                <p class="text-center">版權所有 All Rights Reserved</p>
                                <p class="text-center"><a href="https://www.charmingprint.com" target="_blank">雀印良行</a> version <?php echo NBDESIGNER_VERSION; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</div>