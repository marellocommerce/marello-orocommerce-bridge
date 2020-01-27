<?php

namespace Marello\Bundle\ServicePointBundle\Migrations\Schema\v1_4;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class MarelloServicePointBundle implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->dropIndex($schema, 'marello_sp_timeperiod', 'idx_marello_sp_timeperiod_type');
        $this->dropIndex($schema, 'marello_sp_bh_override', 'UNIQ_26E95087AA9E377A9F5A2460');
        $this->dropIndex($schema, 'marello_sp_servicepoint', 'IDX_7C66571FF5B7AF75');

        $table = $schema->getTable('marello_sp_bh_override');
        $table->addUniqueIndex(['servicepoint_facility_id', 'date']);

        $table = $schema->getTable('marello_sp_servicepoint_fac');
        $table->changeColumn('phone', ['type' => Type::getType(Type::STRING), 'length' => 16, 'notnull' => false]);
        $table->changeColumn('email', ['type' => Type::getType(Type::STRING), 'length' => 64, 'notnull' => false]);

        $table = $schema->getTable('marello_sp_address');
        $table->addForeignKeyConstraint('oro_dictionary_country', ['country_code'], ['iso2_code']);
        $table->addForeignKeyConstraint('oro_dictionary_region', ['region_code'], ['combined_code']);

        $table = $schema->getTable('marello_sp_servicepoint');
        $table->changeColumn('address_id', ['notnull' => false]);
        $table->addUniqueIndex(['address_id']);
        $table->addUniqueIndex(['image_id']);
        $table->addForeignKeyConstraint('oro_attachment_file', ['image_id'], ['id']);
    }

    protected function dropForeignKey(Schema $schema, string $tableName, string $keyName): void
    {
        $table = $schema->getTable($tableName);
        if ($table->hasForeignKey($keyName)) {
            $table->removeForeignKey($keyName);
        }
    }

    protected function dropIndex(Schema $schema, string $tableName, string $indexName): void
    {
        $table = $schema->getTable($tableName);
        if ($table->hasIndex($indexName)) {
            $table->dropIndex($indexName);
        }
    }
}
