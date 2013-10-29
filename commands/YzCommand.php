<?php

class YzCommand extends CConsoleCommand
{
    public function actionInstallModule($name, $modulePath = 'application.modules')
    {
        echo "Installing module {$name}\n";

        if(strpos($name,'.')!==false) {
            $moduleAlias = $name;
            $moduleId = array_pop(explode('.',$name));
        } else {
            $moduleAlias = $modulePath . '.' . $name;
            $moduleId = $name;
        }

        $modulePath = Yii::getPathOfAlias($moduleAlias);
        $moduleClassFile = $modulePath . DIRECTORY_SEPARATOR . ucfirst($moduleId).'Module.php';

        if(!file_exists($moduleClassFile))
            $this->usageError(strtr('Module {module} not found',array(
                '{module}' => $moduleId,
            )));

        echo "Running migrations...\n";

        $migrationPath = $moduleAlias . '.migrations';
        if(!is_dir(Yii::getPathOfAlias($migrationPath))) {
            $this->usageError(strtr('Module {module} doesn\'t have migrations',array(
                '{module}' => $moduleId,
            )));
        } else {
            $this->commandRunner->createCommand('migrate')->run(array(
                "--migrationPath=$migrationPath",
                "--migrationTable={{migrations_yz_{$moduleId}}}",
            ));
        }

        echo "All is done!\n";

        return 0;
    }

    public function actionInstall()
    {
        echo "Installing Yz...\n";

        echo "Running migrations...\n";

        $migrationPath = 'yz.migrations';
        $this->commandRunner->createCommand('migrate')->run(array(
            "--migrationPath=$migrationPath",
            "--migrationTable={{migrations_yz}}",
        ));

        echo "All is done!\n";
    }
}