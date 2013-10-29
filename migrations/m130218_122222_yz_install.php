<?php

class m130218_122222_yz_install extends CDbMigration
{
    public function safeUp()
    {
        $sql=<<<SQL
CREATE TABLE IF NOT EXISTS {{modules_settings}} (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` varchar(150) NOT NULL,
  `parameters` text NOT NULL,
  `create_date` int(11) NOT NULL,
  `update_date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `moduleId` (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL;
        $this->execute($sql);
    }

    public function safeDown()
    {
        echo "m130218_105112_admin_install does not support migration down.\n";
        return false;
    }
}