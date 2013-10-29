<?php /**
 * @var $this YzModuleCode
 */
?>
<?php echo "<?php\n"; ?>

class <?php echo $this->moduleClass; ?> extends YzWebModule
{
	public function getParamsLabels()
    {
        return array(
            'adminMenuOrder' => Yii::t('AdminModule.t9n','Menu order'),
        );
    }

    public function  getVersion()
    {
        return '0.1';
    }

    public function getEditableParams()
    {
        return array(
            'adminMenuOrder' => 'integer',
        );
    }

    public function getName()
    {
        return Yii::t('<?php echo ucfirst($this->moduleID); ?>Module.t9n', '<?php echo CHtml::encode($this->moduleName); ?>');
    }

    public function getDescription()
    {
        return Yii::t('<?php echo ucfirst($this->moduleID); ?>Module.t9n', '<?php echo CHtml::encode($this->moduleDescription); ?>');
    }

    public function getAuthor()
    {
        return Yii::t('<?php echo ucfirst($this->moduleID); ?>Module.t9n', '<?php echo CHtml::encode($this->moduleAuthor); ?>');
    }

    public function getAuthorEmail()
    {
        return '<?php echo CHtml::encode($this->moduleAuthorEmail); ?>';
    }

    public function getUrl()
    {
        return '<?php echo CHtml::encode($this->moduleUrl); ?>';
    }

    public function getIcon()
    {
        return '<?php echo CHtml::encode($this->moduleIcon); ?>';
    }

    public function getAdminNavigation()
    {
        return parent::getAdminNavigation();
    }

    public function beforeControllerAction($controller, $action)
    {
        if(parent::beforeControllerAction($controller, $action))
        {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        }
        else
            return false;
    }

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components

        parent::init();

        if(self::$safeInit)
            return;

        $this->setImport(array(
            '<?php echo $this->moduleID; ?>.models.*',
            '<?php echo $this->moduleID; ?>.components.*',
            '<?php echo $this->moduleID; ?>.widgets.*',
        ));
    }

}
