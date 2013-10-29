<?php /**
 * @var $this YzModuleCode
 */
?>
<?php echo "<?php\n"; ?>

return array(
    '<?php echo CHtml::encode($this->moduleName); ?>' => '<?php echo CHtml::encode($this->moduleName); ?>',
    '<?php echo CHtml::encode($this->moduleDescription); ?>' => '<?php echo CHtml::encode($this->moduleDescription); ?>',
    '<?php echo CHtml::encode($this->moduleAuthor); ?>' => '<?php echo CHtml::encode($this->moduleAuthor); ?>',
);