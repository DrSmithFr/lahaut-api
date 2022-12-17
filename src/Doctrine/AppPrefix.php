<?php

namespace App\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * Prefix all table with "app" to prevent name collision
 */
class AppPrefix implements EventSubscriber
{
    private string $prefix = 'app_';

    public function getSubscribedEvents(): array
    {
        return ['loadClassMetadata'];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
    {
        $classMetadata = $args->getClassMetadata();

        // Only add the prefixes to our own entities.
        if (str_contains($classMetadata->namespace, 'App\Entity')) {
            // Do not re-apply the prefix when the table is already prefixed
            if (!str_contains($classMetadata->getTableName(), $this->prefix)) {
                $tableName = $this->prefix . $classMetadata->getTableName();
                $classMetadata->setPrimaryTable(['name' => $tableName]);
            }

            foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
                if ($mapping['type'] == ClassMetadataInfo::MANY_TO_MANY && $mapping['isOwningSide'] == true) {
                    $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];

                    // Do not re-apply the prefix when the association is already prefixed
                    if (str_contains((string) $mappedTableName, $this->prefix)) {
                        continue;
                    }

                    $classMetadata->associationMappings[$fieldName]['joinTable']['name']
                        = $this->prefix . $mappedTableName;
                }
            }
        }
    }
}
