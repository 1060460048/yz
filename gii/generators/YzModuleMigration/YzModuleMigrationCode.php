<?php

class YzModuleMigrationCode extends CCodeModel
{
    public $moduleId;
    public $migrationsDir='migrations';
    public $migrationSql;
    public $migrationName;
    public $migrationDate;

    public $safeMigration = false;
    public $includeDate = true;

    public function rules()
    {
        return array_merge(parent::rules(),array(
            array('moduleId,migrationDate,migrationSql,migrationName,migrationsDir','required'),
            array('safeMigration,includeDate','boolean'),
            array('moduleId', 'sticky'),
            array('migrationDate','match','pattern'=>'/^\d{2}[01]\d[0123]\d_[012]\d[012345]\d[012345]\d$/'),

            array('moduleId','myModuleIdValidator'),
        ));
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'moduleId'=>'Module',
            'migrationsDir' => 'Migrations directory',
            'migrationSql' => 'SQL for migration',
            'migrationDate' => 'Date for migration',
            'safeMigration' => 'Use transaction',
            'includeDate' => 'Include date in migration name',
        ));
    }

    public function myModuleIdValidator()
    {
        if(!Yii::app()->hasModule($this->moduleId))
            $this->addError('moduleId','Specified module was not found');
    }

    public function init()
    {
        parent::init();
        $this->migrationDate = date('ymd_His');
    }


    /**
     * Prepares the code files to be generated.
     * This is the main method that child classes should implement. It should contain the logic
     * that populates the {@link files} property with a list of code files to be generated.
     */
    public function prepare()
    {
        $migrationName = 'm' .
            ($this->includeDate?$this->migrationDate:'') .
            '_' . $this->migrationName;
        $migrationFile = $migrationName . '.php';

        $migrationPath = Yii::getPathOfAlias($this->moduleId)
            . DIRECTORY_SEPARATOR . $this->migrationsDir . DIRECTORY_SEPARATOR . $migrationFile;

        $templatePath=$this->templatePath;

        $params = array(
            'migrationName' => $migrationName,
            'migrationSql' => $this->migrationSql,
            'safeMigration' => $this->safeMigration,
        );

        $this->files[] = new CCodeFile(
            $migrationPath,
            $this->render($templatePath . '/migration.php',$params)
        );
    }

    public function requiredTemplates()
    {
        return array(
            'migration.php',
        );
    }
}