<?php

namespace Marello\Bundle\ServicePointBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class MarelloServicePointBundleInstaller implements Installation
{
    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_4';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createMarelloSpAddressTable($schema);
        $this->createMarelloSpBhOverrideTable($schema);
        $this->createMarelloSpBusinesshoursTable($schema);
        $this->createMarelloSpFacilityTable($schema);
        $this->createMarelloSpFacilityLabelsTable($schema);
        $this->createMarelloSpServicepointTable($schema);
        $this->createMarelloSpServicepointDescrsTable($schema);
        $this->createMarelloSpServicepointFacTable($schema);
        $this->createMarelloSpServicepointLabelsTable($schema);
        $this->createMarelloSpTimeperiodTable($schema);

        /** Foreign keys generation **/
        $this->addMarelloSpAddressForeignKeys($schema);
        $this->addMarelloSpBhOverrideForeignKeys($schema);
        $this->addMarelloSpBusinesshoursForeignKeys($schema);
        $this->addMarelloSpFacilityLabelsForeignKeys($schema);
        $this->addMarelloSpServicepointForeignKeys($schema);
        $this->addMarelloSpServicepointDescrsForeignKeys($schema);
        $this->addMarelloSpServicepointFacForeignKeys($schema);
        $this->addMarelloSpServicepointLabelsForeignKeys($schema);
        $this->addMarelloSpTimeperiodForeignKeys($schema);
    }

    /**
     * Create marello_sp_address table
     *
     * @param Schema $schema
     */
    protected function createMarelloSpAddressTable(Schema $schema)
    {
        $table = $schema->createTable('marello_sp_address');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('country_code', 'string', ['length' => 2]);
        $table->addColumn('region_code', 'string', ['notnull' => false, 'length' => 16]);
        $table->addColumn('street', 'string', ['notnull' => false, 'length' => 500]);
        $table->addColumn('street2', 'string', ['notnull' => false, 'length' => 500]);
        $table->addColumn('city', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('postal_code', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('organization', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('region_text', 'text', ['notnull' => false]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->setPrimaryKey(['id']);
        $table->addIndex(['country_code']);
        $table->addIndex(['region_code']);
    }

    /**
     * Create marello_sp_bh_override table
     *
     * @param Schema $schema
     */
    protected function createMarelloSpBhOverrideTable(Schema $schema)
    {
        $table = $schema->createTable('marello_sp_bh_override');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('servicepoint_facility_id', 'integer');
        $table->addColumn('date', 'date');
        $table->addColumn('open_status', 'string', ['length' => 6]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['servicepoint_facility_id', 'date']);
        $table->addIndex(['date']);
        $table->addIndex(['servicepoint_facility_id']);
    }

    /**
     * Create marello_sp_businesshours table
     *
     * @param Schema $schema
     */
    protected function createMarelloSpBusinesshoursTable(Schema $schema)
    {
        $table = $schema->createTable('marello_sp_businesshours');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('servicepoint_facility_id', 'integer');
        $table->addColumn('day_of_week', 'integer');
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['day_of_week', 'servicepoint_facility_id']);
        $table->addIndex(['day_of_week']);
        $table->addIndex(['servicepoint_facility_id']);
    }

    /**
     * Create marello_sp_facility table
     *
     * @param Schema $schema
     */
    protected function createMarelloSpFacilityTable(Schema $schema)
    {
        $table = $schema->createTable('marello_sp_facility');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('code', 'string', ['length' => 32]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['code']);
    }

    /**
     * Create marello_sp_facility_labels table
     *
     * @param Schema $schema
     */
    protected function createMarelloSpFacilityLabelsTable(Schema $schema)
    {
        $table = $schema->createTable('marello_sp_facility_labels');
        $table->addColumn('facility_id', 'integer');
        $table->addColumn('localized_value_id', 'integer');
        $table->setPrimaryKey(['facility_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id']);
        $table->addIndex(['facility_id']);
    }

    /**
     * Create marello_sp_servicepoint table
     *
     * @param Schema $schema
     */
    protected function createMarelloSpServicepointTable(Schema $schema)
    {
        $table = $schema->createTable('marello_sp_servicepoint');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('address_id', 'integer', ['notnull' => false]);
        $table->addColumn('image_id', 'integer', ['notnull' => false]);
        $table->addColumn('latitude', 'decimal', ['scale' => 7]);
        $table->addColumn('longitude', 'decimal', ['scale' => 7]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['address_id']);
        $table->addUniqueIndex(['image_id']);
    }

    /**
     * Create marello_sp_servicepoint_descrs table
     *
     * @param Schema $schema
     */
    protected function createMarelloSpServicepointDescrsTable(Schema $schema)
    {
        $table = $schema->createTable('marello_sp_servicepoint_descrs');
        $table->addColumn('service_point_id', 'integer');
        $table->addColumn('localized_value_id', 'integer');
        $table->setPrimaryKey(['service_point_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id']);
        $table->addIndex(['service_point_id']);
    }

    /**
     * Create marello_sp_servicepoint_fac table
     *
     * @param Schema $schema
     */
    protected function createMarelloSpServicepointFacTable(Schema $schema)
    {
        $table = $schema->createTable('marello_sp_servicepoint_fac');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('service_point_id', 'integer');
        $table->addColumn('facility_id', 'integer');
        $table->addColumn('phone', 'string', ['notnull' => false, 'length' => 16]);
        $table->addColumn('email', 'string', ['notnull' => false, 'length' => 64]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->setPrimaryKey(['id']);
        $table->addIndex(['service_point_id']);
        $table->addIndex(['facility_id']);
    }

    /**
     * Create marello_sp_servicepoint_labels table
     *
     * @param Schema $schema
     */
    protected function createMarelloSpServicepointLabelsTable(Schema $schema)
    {
        $table = $schema->createTable('marello_sp_servicepoint_labels');
        $table->addColumn('service_point_id', 'integer');
        $table->addColumn('localized_value_id', 'integer');
        $table->setPrimaryKey(['service_point_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id']);
        $table->addIndex(['service_point_id']);
    }

    /**
     * Create marello_sp_timeperiod table
     *
     * @param Schema $schema
     */
    protected function createMarelloSpTimeperiodTable(Schema $schema)
    {
        $table = $schema->createTable('marello_sp_timeperiod');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('business_hours_id', 'integer', ['notnull' => false]);
        $table->addColumn('business_hours_override_id', 'integer', ['notnull' => false]);
        $table->addColumn('open_time', 'time');
        $table->addColumn('close_time', 'time');
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['open_time', 'close_time']);
        $table->addIndex(['business_hours_id']);
        $table->addIndex(['business_hours_override_id']);
    }

    /**
     * Add marello_sp_address foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloSpAddressForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_sp_address');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_dictionary_region'),
            ['region_code'],
            ['combined_code']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_dictionary_country'),
            ['country_code'],
            ['iso2_code']
        );
    }

    /**
     * Add marello_sp_bh_override foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloSpBhOverrideForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_sp_bh_override');
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_sp_servicepoint_fac'),
            ['servicepoint_facility_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
    }

    /**
     * Add marello_sp_businesshours foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloSpBusinesshoursForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_sp_businesshours');
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_sp_servicepoint_fac'),
            ['servicepoint_facility_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
    }

    /**
     * Add marello_sp_facility_labels foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloSpFacilityLabelsForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_sp_facility_labels');
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_sp_facility'),
            ['facility_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
    }

    /**
     * Add marello_sp_servicepoint foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloSpServicepointForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_sp_servicepoint');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_attachment_file'),
            ['image_id'],
            ['id']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_sp_address'),
            ['address_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
    }

    /**
     * Add marello_sp_servicepoint_descrs foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloSpServicepointDescrsForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_sp_servicepoint_descrs');
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_sp_servicepoint'),
            ['service_point_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
    }

    /**
     * Add marello_sp_servicepoint_fac foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloSpServicepointFacForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_sp_servicepoint_fac');
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_sp_servicepoint'),
            ['service_point_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_sp_facility'),
            ['facility_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
    }

    /**
     * Add marello_sp_servicepoint_labels foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloSpServicepointLabelsForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_sp_servicepoint_labels');
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_sp_servicepoint'),
            ['service_point_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
    }

    /**
     * Add marello_sp_timeperiod foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloSpTimeperiodForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_sp_timeperiod');
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_sp_bh_override'),
            ['business_hours_override_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_sp_businesshours'),
            ['business_hours_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );
    }
}
