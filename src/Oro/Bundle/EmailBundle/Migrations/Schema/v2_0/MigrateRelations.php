<?php

namespace Oro\Bundle\EmailBundle\Migrations\Schema\v2_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\InstallerBundle\Migration\UpdateTableFieldQuery;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class MigrateRelations implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $queries->addQuery(new UpdateTableFieldQuery(
            'oro_email_template',
            'entityname',
            'OroCRM',
            'Oro'
        ));

        $queries->addQuery(new UpdateTableFieldQuery(
            'oro_email_template',
            'content',
            'OroCRM',
            'Oro'
        ));
    }
}
