<?php
/**
 * @var $model YzModuleCode
 * @var $form CCodeForm
 */
?>
<h1>Yz Module Generator</h1>

<p>This generator helps you to generate the skeleton code needed by a Yz module.</p>

<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'moduleID'); ?>
		<?php echo $form->textField($model,'moduleID',array('size'=>65)); ?>
		<div class="tooltip">
			Module ID is case-sensitive. It should only contain word characters.
			The generated module class will be named after the module ID.
			For example, a module ID <code>forum</code> will generate the module class
			<code>ForumModule</code>.
		</div>
		<?php echo $form->error($model,'moduleID'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'moduleName'); ?>
        <?php echo $form->textField($model,'moduleName',array('size'=>65)); ?>
        <div class="tooltip">
            Module name will be displayed in admin panel.
        </div>
        <?php echo $form->error($model,'moduleName'); ?>
    </div>

    <div class="row module-description">
        <?php echo $form->labelEx($model,'moduleDescription'); ?>
        <?php echo $form->textField($model,'moduleDescription',array('size'=>65)); ?>
        <div class="tooltip">
            Module description will be displayed in admin panel.
        </div>
        <?php echo $form->error($model,'moduleDescription'); ?>
    </div>

    <div class="row sticky">
        <?php echo $form->labelEx($model,'moduleVersion'); ?>
        <?php echo $form->textField($model,'moduleVersion',array('size'=>65)); ?>
        <div class="tooltip">
            Version of your module (for new module recomended value - 0.1).
        </div>
        <?php echo $form->error($model,'moduleVersion'); ?>
    </div>

    <div class="row sticky">
        <?php echo $form->labelEx($model,'moduleAuthor'); ?>
        <?php echo $form->textField($model,'moduleAuthor',array('size'=>65)); ?>
        <div class="tooltip">
            Name of the author of this module.
        </div>
        <?php echo $form->error($model,'moduleAuthor'); ?>
    </div>

    <div class="row sticky">
        <?php echo $form->labelEx($model,'moduleAuthorEmail'); ?>
        <?php echo $form->textField($model,'moduleAuthorEmail',array('size'=>65)); ?>
        <div class="tooltip">
            Email to contact to.
        </div>
        <?php echo $form->error($model,'moduleAuthorEmail'); ?>
    </div>

    <div class="row sticky">
        <?php echo $form->labelEx($model,'moduleUrl'); ?>
        <?php echo $form->textField($model,'moduleUrl',array('size'=>65)); ?>
        <div class="tooltip">
            Link to web site (maybe github, etc.).
        </div>
        <?php echo $form->error($model,'moduleUrl'); ?>
    </div>

    <div class="row sticky">
        <?php echo $form->labelEx($model,'moduleIcon'); ?>
        <?php echo $form->textField($model,'moduleIcon',array('size'=>65)); ?>
        <div class="tooltip">
            Currently this should be one of Twitter Bootstrap's icon
            Later custom icons will be available to use
        </div>
        <?php echo $form->error($model,'moduleIcon'); ?>
    </div>

<?php $this->endWidget(); ?>
