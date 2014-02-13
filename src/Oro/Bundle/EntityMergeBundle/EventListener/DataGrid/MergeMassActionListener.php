<?php

namespace Oro\Bundle\EntityMergeBundle\EventListener\DataGrid;

use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Oro\Bundle\EntityMergeBundle\Metadata\MetadataRegistry;

class MergeMassActionListener
{
    protected $metadataRegistry;

    public function __construct(MetadataRegistry $metadataRegistry)
    {
        $this->metadataRegistry = $metadataRegistry;
    }

    /**
     * Remove mass action if entity config mass action disabled
     *
     * @param BuildBefore $event
     */
    public function onBuildBefore(BuildBefore $event)
    {
        $config = $event->getConfig();
        $massActions = $config['mass_actions'];
        if (!isset($massActions['merge']) || empty($massActions['merge']['entity_name'])) {
            return;
        }

        $entityName = $massActions['merge']['entity_name'];

        $entityMergeEnable = $this->metadataRegistry->getEntityMetadata($entityName)
            ->get('enable');

        if (!$entityMergeEnable) {
            $config->offsetUnsetByPath('[mass_actions][merge]');
        }
    }
}
