<?php
    if (!defined('ABSPATH')) exit;
?>
<div class="nbdl-dashboard">
    <div class="nbdl-dashboard-column left">
        <div class="nbdl-dashboard-inner">
            <div class="nbdl-dashboard-head">
                <?php _e('Created designs', 'web-to-print-online-designer'); ?>
            </div>
            <div>
                <div class="nbdl-dashboard-box">
                    <span><?php _e('Total', 'web-to-print-online-designer'); ?></span> <span><?php echo $designs['all']; ?></span>
                </div>
                <div class="nbdl-dashboard-box">
                    <span><?php _e('Approved', 'web-to-print-online-designer'); ?></span> <span><?php echo $designs['approved']; ?></span>
                </div>
                <div class="nbdl-dashboard-box">
                    <span><?php _e('Pending', 'web-to-print-online-designer'); ?></span> <span><?php echo $designs['pending']; ?></span>
                </div>
            </div>
        </div>
        <div class="nbdl-dashboard-inner">
            <div class="nbdl-dashboard-head">
                <?php _e('Design sold items', 'web-to-print-online-designer'); ?>
            </div>
            <div>
                <div class="nbdl-dashboard-box">
                    <span><?php _e('Total', 'web-to-print-online-designer'); ?></span> <span><?php echo $sales; ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="nbdl-dashboard-column right">
        <div class="nbdl-dashboard-inner">
            <div class="nbdl-dashboard-head">
                <?php _e('Designs and Sales this Month', 'web-to-print-online-designer'); ?>
            </div>
            <div>
                <canvas id="nbdl-chart"></canvas>
            </div>
        </div>
    </div>
</div>
<script type='text/javascript' src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/libs/moment.min.js'; ?>"></script>
<script type='text/javascript' src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/libs/Chart.min.js'; ?>"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        var ctx = document.getElementById('nbdl-chart').getContext('2d');
        var nbdlChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode( $report['labels'] ); ?>,
                datasets: <?php echo json_encode( $report['datasets'] ); ?>
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        type: 'time',
                        scaleLabel: {
                            display: false
                        },
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            fontColor: '#aaa',
                            fontSize: 11
                        }
                    }],
                    yAxes: [{
                        scaleLabel: {
                            display: false
                        },
                        ticks: {
                            fontColor: '#aaa'
                        }
                    }]
                },
                legend: {
                    position: 'top',
                    onClick: false
                },
                elements: {
                    line: {
                        tension: 0,
                        borderWidth: 4
                    },
                    point: {
                        radius: 5,
                        borderWidth: 3,
                        backgroundColor: '#fff',
                        borderColor: '#fff'
                    }
                },
                tooltips: {
                    displayColors: false,
                    callbacks: {
                        label: function (tooltipItems, data) {
                            let label = data.datasets[tooltipItems.datasetIndex].label || '';
                            let customLabel = data.datasets[tooltipItems.datasetIndex].tooltipLabel || '';
                            let prefix = data.datasets[tooltipItems.datasetIndex].tooltipPrefix || '';

                            let tooltipLabel = customLabel ? customLabel + ': ' : label + ': ';

                            tooltipLabel += prefix + tooltipItems.yLabel;

                            return tooltipLabel;
                        }
                    }
                }
            }
        });
    });
</script>