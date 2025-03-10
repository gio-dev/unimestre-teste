<?php

namespace App\Traits;

use App\Models\File;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseRepository
 *
 */
trait BaseRepository
{
    /**
     * @var Model Class
     */
    protected Model $entity;

    protected $fileEntity;

    /**
     * @var $identify
     */
    protected mixed $identify;

    /**
     *
     * @var mixed
     */
    protected mixed $unique = null;

    /**
     * Summary of eagerLoading
     * @var array
     */
    protected array $eagerLoading = [];

    /**
     * Summary of eagerWith
     * @var bool
     */
    protected bool $eagerWith = true;

    /**
     * @return Model
     */
    public function getEntityClass()
    {
        return $this->entity;
    }

    /**
     * @param Model $model
     */
    public function setEntityClass(Model $model)
    {
        $this->entity = $model;
    }

    /**
     * @return string
     */
    public function getIdentify()
    {
        return $this->identify;
    }

    /**
     * @param string $identify
     */
    public function setIdentify(string $identify)
    {
        $this->identify = $identify;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnique()
    {
        return $this->unique;
    }

    /**
     * @param string $unique
     */
    public function setUnique(string $unique)
    {
        $this->unique = $unique;

        return $this;
    }

    /**
     * @return array
     */
    public function getEagerLoading()
    {
        return $this->eagerLoading;
    }

    /**
     * @param array $eagerLoading
     */
    public function setEagerLoading(array $eagerLoading)
    {
        $this->eagerLoading = $eagerLoading;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEagerWith()
    {
        return $this->eagerWith;
    }

    /**
     * @param bool $eagerWith
     */
    public function setEagerWith(bool $eagerWith)
    {
        $this->eagerWith = $eagerWith;

        return $this;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function createNewEntity(array $data)
    {
        if (!empty($this->getUnique())) {
            $data['deleted_at'] = null;
            $object = $this->entity->withTrashed()->updateOrCreate(
                [$this->getUnique() => $data[$this->getUnique()]],
                $data
            );

            if ($object->deleted_at != null) {
                $object->restore();
            }

            return $object;
        }

        $model = $this->entity->newInstance($data);
        $model->saveOrFail();
        return $model;
    }

    /**
     * @return mixed
     */
    public function search()
    {
        if ($this->getEagerWith() && !empty($this->getEagerLoading())) {
            $query = $this->entity->with($this->getEagerLoading())->useFilters()->dynamicPaginate();
        } else {
            $query = $this->entity->useFilters()->dynamicPaginate();
            if (!empty($this->getEagerLoading())) {
                $query->loadMissing($this->getEagerLoading());
            }
        }
        return $query;
    }

    /**
     * @return mixed
     */
    public function paginateDefault()
    {
        if ($this->getEagerWith() && !empty($this->getEagerLoading())) {
            $query = $this->entity->with($this->getEagerLoading())->dynamicPaginate();
        } else {
            $query = $this->entity->dynamicPaginate();
            if (!empty($this->getEagerLoading())) {
                $query->loadMissing($this->getEagerLoading());
            }
        }
        return $query;
    }

    /**
     * @return mixed
     */
    public function search_without_paginate()
    {
        if ($this->getEagerWith() && !empty($this->getEagerLoading())) {
            $query = $this->entity->with($this->getEagerLoading())->useFilters()->get();
        } else {
            $query = $this->entity->useFilters()->get();

            if (!empty($this->getEagerLoading())) {
                $query->loadMissing($this->getEagerLoading());
            }
        }
        return $query;
    }
    public function search_one()
    {
        if ($this->getEagerWith() && !empty($this->getEagerLoading())) {
            $query = $this->entity->with($this->getEagerLoading())->useFilters()->first();
        } else {
            $query = $this->entity->useFilters()->first();

            if (!empty($this->getEagerLoading())) {
                $query->loadMissing($this->getEagerLoading());
            }
        }
        return $query;
    }
    /**
     * @return mixed
     */
    public function findAll()
    {
        if ($this->getEagerWith() && !empty($this->getEagerLoading())) {
            $query = $this->entity->with($this->getEagerLoading())->all();
        } else {
            $query = $this->entity->all();

            if (!empty($this->getEagerLoading())) {
                $query->loadMissing($this->getEagerLoading());
            }
        }
        return $query;
    }

    public function countAll()
    {
        return $this->entity->count();
    }

    public function countAllFiltered()
    {
        return $this->entity->useFilters()->count();
    }

    public function countByIdentify($identify)
    {
        return $this->entity->where($this->identify, $identify)->count();
    }

    public function findInIdentify(array $values)
    {
        if ($this->getEagerWith() && !empty($this->getEagerLoading())) {
            $query = $this->entity->whereIn($this->identify, $values)->with($this->getEagerLoading())->get();
        } else {
            $query = $this->entity->whereIn($this->identify, $values)->get();

            if (!empty($this->getEagerLoading())) {
                $query->loadMissing($this->getEagerLoading());
            }
        }
        return $query;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function paginateByIdentify($identify)
    {
        if ($this->getEagerWith() && !empty($this->getEagerLoading())) {
            $query = $this->entity->where($this->identify, $identify)->with($this->getEagerLoading())->dynamicPaginate();
        } else {
            $query = $this->entity->where($this->identify, $identify)->dynamicPaginate();

            if (!empty($this->getEagerLoading())) {
                $query->loadMissing($this->getEagerLoading());
            }
        }
        return $query;
    }

    public function searchByIdentify($identify, $classFilter)
    {
        if ($this->getEagerWith() && !empty($this->getEagerLoading())) {
            $query = $this->entity->where($this->identify, $identify)->with($this->getEagerLoading())
                ->useFilters($classFilter)->dynamicPaginate();
        } else {
            $query = $this->entity->where($this->identify, $identify)->useFilters($classFilter)->dynamicPaginate();

            if (!empty($this->getEagerLoading())) {
                $query->loadMissing($this->getEagerLoading());
            }
        }
        return $query;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findOneById(int $id)
    {
        if ($this->getEagerWith() && !empty($this->getEagerLoading())) {
            $query = $this->entity->where('id', $id)->with($this->getEagerLoading())->first();
        } else {
            $query = $this->entity->where('id', $id)->first();

            if (!empty($this->getEagerLoading())) {
                $query->loadMissing($this->getEagerLoading());
            }
        }
        return $query;
    }

    /**
     * @param string $uuid
     * @return mixed
     */
    public function findOneByUuid(string $uuid)
    {
        if ($this->getEagerWith() && !empty($this->getEagerLoading())) {
            $query = $this->entity->where('uuid', $uuid)->with($this->getEagerLoading())->first();
        } else {
            $query = $this->entity->where('uuid', $uuid)->first();

            if (!empty($this->getEagerLoading())) {
                $query->loadMissing($this->getEagerLoading());
            }
        }
        return $query;
    }

    /**
     * @param mixed $identify
     * @return mixed
     */
    public function findOneByIdentify($identify)
    {
        if ($this->getEagerWith() && !empty($this->getEagerLoading())) {
            $query = $this->entity->where($this->identify, $identify)->with($this->getEagerLoading())->first();
        } else {
            $query = $this->entity->where($this->identify, $identify)->first();

            if (!empty($this->getEagerLoading())) {
                $query->loadMissing($this->getEagerLoading());
            }
        }
        return $query;
    }

    public function findByIdentify($identify)
    {
        if ($this->getEagerWith() && !empty($this->getEagerLoading())) {
            $query = $this->entity->where($this->identify, $identify)->with($this->getEagerLoading())->get();
        } else {
            $query = $this->entity->where($this->identify, $identify)->get();

            if (!empty($this->getEagerLoading())) {
                $query->loadMissing($this->getEagerLoading());
            }
        }
        return $query;
    }

    /**
     * @param mixed $identify
     * @param array $data
     * @return null
     */
    public function updateByIdentify($identify, array $data)
    {
        $entity = $this->entity->where($this->identify, $identify)->first();

        if ($entity) {
            return tap($entity)->updateOrFail($data);
        }

        return null;
    }

    /**
     * @param int $id
     * @param array $data
     * @return null
     */
    public function updateById(int $id, array $data)
    {
        $entity = $this->entity->where('id', $id)->first();

        if ($entity) {

            return tap($entity)->updateOrFail($data);
        }

        return null;
    }

    /**
     * @param string $uuid
     * @param array $data
     * @return null
     */
    public function updateByUuid(string $uuid, array $data)
    {
        $entity = $this->entity->where('uuid', $uuid)->first();

        if ($entity) {
            return tap($entity)->updateOrFail($data);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function deleteAll()
    {
        return $this->findAll()->delete();
    }

    /**
     * @param mixed $identify
     * @return mixed
     */
    public function deleteByIdentify($identify)
    {
        return $this->entity->where($this->identify, $identify)->first()->delete();
    }

    /**
     * @param mixed $identify
     * @return mixed
     */
    public function deleteAllByIdentify($identify)
    {
        return $this->entity->where($this->identify, $identify)->delete();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function deleteById(int $id)
    {
        return $this->entity->where('id', $id)->first()->delete();
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function bulkDelete(array $ids)
    {
        return $this->entity->whereIn('id', $ids)->delete();
    }

    /**
     * @param string $uuid
     * @return mixed
     */
    public function deleteByUuid(string $uuid)
    {
        return $this->entity->where('uuid', $uuid)->first()->delete();
    }

    public function getLast()
    {
        return $this->entity->latest();
    }

    public function saveNewFileUploaded(array $dbfilesdata)
    {
        $this->fileEntity = new File();

        return $this->fileEntity->create($dbfilesdata);
    }

    public function attachNewFileUploaded(File $file, $id)
    {
        $model = $this->findOneById($id);

        return $model->files()->attach($file);
    }

    /**
     * @param int $id
     */
    public function replicateById(int $id, string $identifyValue)
    {
        $entity = $this->entity->where('id', $id)->first();

        if ($entity) {
            $newEntity = $entity->replicate();
            $newEntity->{$this->getIdentify()} = $newEntity->{$this->getIdentify()} . $identifyValue;

            if (isset($newEntity->create_at)) {
                $newEntity->create_at = Carbon::now();
            }

            if (!empty($this->getUnique())) {
                $data = $newEntity->toArray();
                $data['deleted_at'] = null;
                $object = $this->entity->withTrashed()->updateOrCreate(
                    [$this->getUnique() => $data[$this->getUnique()]],
                    $data
                );

                if ($object && $object->deleted_at != null) {
                    $object->restore();
                }

                return $object;
            }

            if ($newEntity->save()) {
                return $newEntity;
            }

            return false;
        }

        return null;
    }

    /**
     * @param string $uuid
     */
    public function replicateByUuid(string $uuid, string $identifyValue)
    {
        $entity = $this->entity->where('uuid', $uuid)->first();

        if ($entity) {
            $newEntity = $entity->replicate();
            $newEntity->{$this->getIdentify()} = $newEntity->{$this->getIdentify()} . $identifyValue;

            if (isset($newEntity->create_at)) {
                $newEntity->create_at = Carbon::now();
            }

            if (!empty($this->getUnique())) {
                $data = $newEntity->toArray();
                $data['deleted_at'] = null;
                $object = $this->entity->withTrashed()->updateOrCreate(
                    [$this->getUnique() => $data[$this->getUnique()]],
                    $data
                );

                if ($object && $object->deleted_at != null) {
                    $object->restore();
                }

                return $object;
            }

            if ($newEntity->save()) {
                return $newEntity;
            }

            return false;
        }

        return null;
    }

    /**
     * @param int $id
     */
    public function ativarInativarEntidadeById(int $id, string $activeValue, string $inactiveValue)
    {
        $entity = $this->entity->where('id', $id)->first();

        if ($entity) {
            return tap($entity)->updateOrFail([
                $this->getIdentify() => ($entity->{$this->getIdentify()} == $activeValue
                    ? $inactiveValue : $activeValue),
            ]);
        }

        return null;
    }

    /**
     * @param string $uuid
     */
    public function ativarInativarEntidadeByUuid(string $uuid, string $activeValue, string $inactiveValue)
    {
        $entity = $this->entity->where('uuid', $uuid)->first();

        if ($entity) {
            return tap($entity)->updateOrFail([
                $this->getIdentify() => ($entity->{$this->getIdentify()} == $activeValue
                    ? $inactiveValue : $activeValue),
            ]);
        }

        return null;
    }

    public function findWhereIn(string $column, array $values)
    {
        $query = $this->entity->whereIn($column, $values)->get();
        if (!empty($this->eagerLoading)) {
            $query->loadMissing($this->eagerLoading);
        }
        return $query;
    }
}
