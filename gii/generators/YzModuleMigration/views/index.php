<?php
/**
 * @var $this CController
 * @var $model YzModuleMigrationCode
 */
?>
<h1>Module Migration Generator</h1>

<p>This generator generates a migration for module.</p>

<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); /** @var $form CCodeForm */ ?>

<div class="row sticky">
    <?php echo $form->labelEx($model,'moduleId'); ?>
    <?php echo $form->textField($model,'moduleId', array('size'=>65)); ?>
    <div class="tooltip">
        Module Id
    </div>
    <?php echo $form->error($model,'moduleId'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model,'migrationDate'); ?>
    <?php echo $form->textField($model,'migrationDate', array('size'=>65)); ?>
    <div class="tooltip">
        Migration datetime
    </div>
    <?php echo $form->error($model,'migrationDate'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model,'migrationName'); ?>
    <?php echo $form->textField($model,'migrationName', array('size'=>65)); ?>
    <div class="tooltip">
        Migration datetime
    </div>
    <?php echo $form->error($model,'migrationName'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model,'migrationsDir'); ?>
    <?php echo $form->textField($model,'migrationsDir', array('size'=>65)); ?>
    <div class="tooltip">
        Directory for migrations
    </div>
    <?php echo $form->error($model,'migrationsDir'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model,'safeMigration'); ?>
    <?php echo $form->checkBox($model,'safeMigration'); ?>
    <div class="tooltip">
        Migration datetime
    </div>
    <?php echo $form->error($model,'safeMigration'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model,'includeDate'); ?>
    <?php echo $form->checkBox($model,'includeDate'); ?>
    <div class="tooltip">
        Migration datetime
    </div>
    <?php echo $form->error($model,'includeDate'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model,'migrationSql'); ?>
    <?php echo $form->textArea($model,'migrationSql', array('cols'=>60, 'rows'=>7)); ?>
    <div class="tooltip">
        SQL code for migration
    </div>
    <?php echo $form->error($model,'migrationSql'); ?>
</div>

<?php $this->endWidget(); ?>
