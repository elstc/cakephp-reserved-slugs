<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace ReservedSlugs\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use ReservedSlugs\Model\Entity\ReservedSlug;

/**
 * ReservedSlugs Model
 *
 * @method \ReservedSlugs\Model\Entity\ReservedSlug newEmptyEntity()
 * @method \ReservedSlugs\Model\Entity\ReservedSlug newEntity(array $data, array $options = [])
 * @method \ReservedSlugs\Model\Entity\ReservedSlug get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \ReservedSlugs\Model\Entity\ReservedSlug findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \ReservedSlugs\Model\Entity\ReservedSlug patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \ReservedSlugs\Model\Entity\ReservedSlug|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \ReservedSlugs\Model\Entity\ReservedSlug saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 */
class ReservedSlugsTable extends Table
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('reserved_slugs');
        $this->setDisplayField('slug');
        // Uses slug as the primary key since each slug is inherently unique
        // and lookups are always by slug value.
        $this->setPrimaryKey('slug');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_at' => 'new',
                ],
            ],
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('slug')
            ->maxLength('slug', 128)
            ->requirePresence('slug', 'create')
            ->notEmptyString('slug');

        return $validator;
    }

    /**
     * Check if a slug is reserved.
     *
     * @param string $slug The slug to check.
     * @return bool
     */
    public function slugExists(string $slug): bool
    {
        return $this->exists(['slug' => $slug]);
    }

    /**
     * Add a reserved slug.
     *
     * @param string $slug The slug to add.
     * @return \ReservedSlugs\Model\Entity\ReservedSlug|false
     */
    public function addSlug(string $slug): ReservedSlug|false
    {
        if ($this->slugExists($slug)) {
            return false;
        }

        $entity = $this->newEntity(['slug' => $slug]);

        return $this->save($entity) ?: false;
    }

    /**
     * Remove a reserved slug.
     *
     * @param string $slug The slug to remove.
     * @return bool True if the slug was removed, false if it did not exist.
     */
    public function removeSlug(string $slug): bool
    {
        return $this->deleteAll(['slug' => $slug]) > 0;
    }
}
