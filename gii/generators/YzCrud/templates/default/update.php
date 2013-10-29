<?php
/**
 * The following variables are available in this template:
 * - $this: the YzCrudCode object
 */
?>
<?php
echo "<?php\n";
$nameColumn=$this->guessNameColumn($this->tableSchema->columns);
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	Yii::t('{$this->t9nCategory}','$label')=>array('index'),
	Yii::t('AdminModule.t9n','Update'),
);\n";
?>

$this->actions=array(
	array('label'=>Yii::t('AdminModule.t9n','List'),'icon'=>'arrow-left','url'=>array('index'),'type'=>'warning'),
	array('label'=>Yii::t('AdminModule.t9n','Add'),'url'=>array('create')),
);
?>

<div class="page-header subnav">
    <?php echo "<?php "; ?>$this->widget('yzAdmin.widgets.AdminActionsWidget', array(
    'actions' => $this->actions,
    )); ?>

    <h3><?php echo "<?php echo Yii::t('AdminModule.t9n','Updating record'); ?>"; ?></h3>
</div>

<?php echo "<?php echo \$this->renderPartial('_form',array('model'=>\$model)); ?>"; ?>