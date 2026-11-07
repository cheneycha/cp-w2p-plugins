<div class="v-layout nbd-layout">
    <?php include 'stages.php';?>
    <?php include 'toolbar-zoom.php';?>
    <div class="v-toolbox" ng-show="stages[currentStage].states.isActiveLayer">
        <?php include 'toolbox/toolbox-text.php'?>
        <?php include 'toolbox/toolbox-image.php'?>
        <?php include 'toolbox/toolbox-group.php'?>
        <?php include 'toolbox/toolbox-path.php'?>
    </div>
    <?php include 'loading-workflow.php';?>
</div>