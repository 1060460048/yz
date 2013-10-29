<?php echo "<?php\n" ?>

class <?php echo $migrationName; ?> extends CDbMigration
{
    public function <?php echo $safeMigration?'safeUp':'up';?>()
    {
        $sql=<<<SQL
<?php echo $migrationSql."\n";?>
SQL;
        $this->execute($sql);
    }

    public function <?php echo $safeMigration?'safeDown':'down';?>()
    {
        echo "<?php echo $migrationName; ?> does not support migration down.\n";
        return false;
    }
}