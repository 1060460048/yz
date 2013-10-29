<?php
/**
 * The following variables are available in this template:
 * - $this: the YzCrudCode object
 * @var $this YzCrudCode
 */
?>
<?php
echo "<?php\n";
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	Yii::t('{$this->t9nCategory}','$label')
);\n";
?>

$this->actions=array(
	array('label'=>Yii::t('AdminModule.t9n','Add'),'url'=>array('create')),
    array('label'=>Yii::t('AdminModule.t9n','Delete selected...'), 'icon' => 'trash',
        'yzType'=>'deleteChecked', 'yzGridViewId' => '<?php echo $this->class2id($this->modelClass); ?>-grid',
        'yzColumnId'=>'<?php echo $this->class2id($this->modelClass); ?>-grid-rows-checkboxes',
        'type'=>'danger'),
);
<?php if($this->addSearch): ?>
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('<?php echo $this->class2id($this->modelClass); ?>-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
<?php endif; ?>
?>

<div class="page-header subnav">

    <?php echo "<?php "; ?>$this->widget('yzAdmin.widgets.AdminActionsWidget', array(
        'actions' => $this->actions,
    )); ?>

    <h3><?php echo "<?php echo Yii::t('".$this->t9nCategory."','". $this->pluralize($this->class2name($this->modelClass))."'); ?>"; ?></h3>
</div>

<?php if($this->addSearch): ?>
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo "<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>"; ?>

<div class="search-form" style="display:none">
<?php echo "<?php \$this->renderPartial('_search',array(
	'model'=>\$model,
)); ?>\n"; ?>
</div><!-- search-form -->
<?php endif; ?>

<?php echo "<?php"; ?> $this->widget('yzAdmin.widgets.AdminGridView',array(
	'id'=>'<?php echo $this->class2id($this->modelClass); ?>-grid',
    'type'=>'striped bordered condensed',
	'dataProvider'=>$model->search(),
    <?php if($this->addSearch): ?>'filter'=>$model,<? endif; ?>
	'columns'=>array(
            array(
                'class'=>'CCheckBoxColumn',
                'id'=>'<?php echo $this->class2id($this->modelClass); ?>-grid-rows-checkboxes',
                'selectableRows'=>2
            ),
<?php
$count=0;
foreach($this->tableSchema->columns as $column)
{
	echo "\t\t\t";
    if(++$count>=7)
		echo "// ";
	echo "'".$column->name."',\n";
}
?>
            array(
                'class'=>'yzAdmin.widgets.AdminExtendedColumn',
                //'links'=>array(),
            ),
            array(
                'class'=>'yzAdmin.widgets.AdminButtonColumn',
                'template'=>'{update} {delete}',
            ),
	),
)); ?>
