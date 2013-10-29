<?php
/**
 * The following variables are available in this template:
 * @var $this YzCrudCode
 */
?>
<?php echo "<?php\n"; ?>
/** @var $this YzBackController */
/** @var $model <?php echo $this->modelClass; ?> */
?>
<?php echo "<?php /** @var \$form AdminActiveFormWidget */
\$form=\$this->beginWidget('yzAdmin.widgets.AdminActiveFormWidget',array(
	'id'=>'".$this->class2id($this->modelClass)."-form',
	'enableAjaxValidation'=>true,
	'type'=>'horizontal',
	'htmlOptions' => array('class' => 'well'),
)); ?>\n"; ?>

	<p class="help-block"><?php
        echo "<?php echo Yii::t('AdminModule.t9n','Fields with {star} are required.',array(
        '{star}'=>'<span class=\"required\">*</span>'
        )); ?>" ?></p>

	<?php echo "<?php echo \$form->errorSummary(\$model); ?>\n"; ?>

<?php
foreach($this->tableSchema->columns as $column)
{
	if($column->autoIncrement)
		continue;
?>
	<?php echo "<?php echo ".$this->generateActiveRow($this->modelClass,$column)."; ?>\n"; ?>

<?php
}
?>
	<div class="form-actions">
		<?php echo "<?php \$this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>Yii::t('AdminModule.t9n',\$model->isNewRecord ? 'Create & Leave' : 'Save & Leave'),
		)); ?>\n"; ?>
    <?php echo "<?php if(\$model->isNewRecord == false): ?>\n"; ?>
        <?php echo "<?php \$this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>Yii::t('AdminModule.t9n',\$model->isNewRecord ? 'Create' : 'Save'),
			'htmlOptions' => array(
                'name' => 'saveAndStay',
            ),
		)); ?>\n"; ?>
        <?php echo "<?php endif; ?>\n"; ?>
	</div>

<?php echo "<?php \$this->endWidget(); ?>\n"; ?>
