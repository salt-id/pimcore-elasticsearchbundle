<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 17/12/2019
 * Time: 17:52
 */

namespace SaltId\ElasticSearchBundle;

use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Extension\Bundle\Installer\MigrationInstaller;

class Installer extends MigrationInstaller
{
    const TABLE_INDEX_RULE = 'bundle_elasticsearch_index_rule';

    const TABLE_CONFIG = 'bundle_elasticsearch_config';

    /**
     * Executes install migration. Used during installation for initial creation of database tables and other data
     * structures (e.g. pimcore classes). The version object is the version object which can be used to add raw SQL
     * queries via `addSql`.
     *
     * If possible, use the Schema object to manipulate DB state (see Doctrine Migrations)
     *
     * @param Schema $schema
     * @param Version $version
     */
    public function migrateInstall(Schema $schema, Version $version)
    {
        $this->installIndexRuleTable($schema, $version);
        $this->installConfigTable($schema, $version);
    }

    /**
     * Opposite of migrateInstall called on uninstallation of a bundle.
     *
     * @param Schema $schema
     * @param Version $version
     */
    public function migrateUninstall(Schema $schema, Version $version)
    {
        $this->uninstallIndexRuleTable($schema, $version);
        $this->uninstallConfigTable($schema, $version);
    }

    private function installIndexRuleTable(Schema $schema, Version $version)
    {
        $table = $schema->createTable(self::TABLE_INDEX_RULE);
        $table->addColumn('id', 'integer', [
            'autoincrement' => true,
        ]);

        $table->addColumn('name', 'string', [
            'default' => ''
        ]);

        $table->addColumn('onDataObjectPreAdd', 'smallint', [
            'default' => 0,
            'notnull' => false
        ]);

        $table->addColumn('onDataObjectPostAdd', 'smallint', [
            'default' => 0,
            'notnull' => false
        ]);

        $table->addColumn('onDataObjectPreUpdate', 'smallint', [
            'default' => 0,
            'notnull' => false
        ]);

        $table->addColumn('onDataObjectPostUpdate', 'smallint', [
            'default' => 0,
            'notnull' => false
        ]);

        $table->addColumn('className', 'string', [
            'notnull' => false
        ]);
        $table->addColumn('classFieldConfig', 'text', [
            'notnull' => false
        ]);

        $table->addColumn('active', 'smallint', [
            'default' => 1,
            'notnull' => false
        ]);

        $table->addUniqueIndex(['className']);
        $table->setPrimaryKey(['id']);
    }

    private function uninstallIndexRuleTable(Schema $schema, Version $version)
    {
        if (!$schema->hasTable(self::TABLE_INDEX_RULE)) {
            return;
        }

        $schema->dropTable(self::TABLE_INDEX_RULE);
    }

    private function installConfigTable(Schema $schema, Version $version)
    {
        $table = $schema->createTable(self::TABLE_CONFIG);

        $table->addColumn('id', 'integer', [
            'autoincrement' => true,
        ]);

        $table->addColumn('name', 'string', [
            'notnull' => false
        ]);

        $table->addColumn('hostorip', 'string', [
            'notnull' => false
        ]);

        $table->addColumn('port', 'string', [
            'notnull' => false
        ]);

        $table->addColumn('httpBasicAuthUser', 'string', [
            'notnull' => false
        ]);

        $table->addColumn('httpBasicAuthPassword', 'string', [
            'notnull' => false
        ]);

        $table->addColumn('index', 'string', [
            'notnull' => false
        ]);

        $table->addUniqueIndex(['name']);
        $table->setPrimaryKey(['id']);
    }

    private function uninstallConfigTable(Schema $schema, Version $version)
    {
        if (!$schema->hasTable(self::TABLE_CONFIG)) {
            return;
        }

        $schema->dropTable(self::TABLE_CONFIG);
    }
}
