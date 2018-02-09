<?php
/**
 * This view is used by console/controllers/MigrateController.php.
 *
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name without namespace */
/* @var $namespace string the new migration class namespace */
/* @var $table string the name table */
/* @var $fields array the fields */

echo "<?php\n";
if (!empty($namespace)) {
    echo "\nnamespace {$namespace};\n";
}
?>

use console\components\Migration;

/**
 * Handles the dropping of table `<?= $table ?>`.
<?= $this->render('_foreignTables', [
    'foreignKeys' => $foreignKeys,
]) ?>
 */
class <?= $className ?> extends Migration
{
    const TABLE_NAME = '<?= $table; ?>';

    /**
     * @inheritdoc
     */
    public function up()
    {
<?= $this->render('_dropTable', [
    'table' => "self::TABLE_NAME",
    'foreignKeys' => $foreignKeys,
])
?>
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
<?= $this->render('_createTable', [
    'table' => self::TABLE_NAME,
    'fields' => $fields,
    'foreignKeys' => $foreignKeys,
])
?>
    }
}
