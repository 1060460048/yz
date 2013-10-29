<?php
/**
 * The following variables are available in this template:
 * - $this: the YzCrudCode object
 */
?>
<?php
echo "<?php\n";
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	Yii::t('{$this->t9nCategory}','$label')=>array('index'),
	Yii::t('AdminModule.t9n','Create'),
);\n";
?>

$this->actions=array(
	array(
        'label'=>Yii::t('AdminModule.t9n','List'),
        'icon'=>'arrow-left',
        'url'=>array('index'),
        'type'=>'warning',
    ),
);
?>

<div class="page-header subnav">
    <?php echo "<?php "; ?>$this->widget('yzAdmin.widgets.AdminActionsWidget', array(
        'actions' => $this->actions,
    )); ?>

    <h3><?php echo "<?php echo Yii::t('AdminModule.t9n','Creating new record'); ?>"; ?></h3>
</div>

<?php echo "<?php echo \$this->renderPartial('_form', array('model'=>\$model)); ?>"; ?>
