<?php


namespace App\Traits\Inquirer;


use App\Inquirer\Inquirer;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

/**
 * Trait QueryTrait
 * @package App\Traits\Inquirer
 */
trait QueryTrait
{
    /**
     * 檢測對象是否為 Query Builder (DB)
     *
     * @param $instance
     * @return bool
     */
    public static function isQueryBuilder($instance): bool
    {
        return $instance instanceof QueryBuilder;
    }

    /**
     * 檢測對象是否為 Eloquent Builder (Model ORM)
     *
     * @param $instance
     * @return bool
     */
    public static function isEloquentBuilder($instance): bool
    {
        return $instance instanceof EloquentBuilder;
    }

    /**
     * 檢測對象是否為查詢庫
     *
     * @param $instance
     * @return bool
     */
    public static function isBuilder($instance): bool
    {
        return static::isQueryBuilder($instance) || static::isEloquentBuilder($instance);
    }

    /**
     * 檢測對象是否為 Model
     *
     * @param $instance
     * @return bool
     */
    public static function isModel($instance): bool
    {
        return $instance instanceof Model;
    }


    /**
     * 檢測對象是否為關聯查詢庫
     *
     * @param $instance
     * @return bool
     */
    public static function isRelation($instance): bool
    {
        return $instance instanceof Relation;
    }

    /**
     * 若傳進來是Model則產生Query
     *
     * @param $query
     * @return EloquentBuilder|QueryBuilder
     * @throws \ReflectionException
     */
    protected function convertBuilder($query): EloquentBuilder|QueryBuilder
    {
        if (self::isModel($query)) {
            return $query->newQuery();
        } elseif (self::isEloquentBuilder($query) || self::isQueryBuilder($query)) {
            return $query;
        } else {
            throw new \ReflectionException(__METHOD__ . ' Must be Model or Builder');
        }
    }

    /**
     * 取得資料表名稱
     *
     * @param $query
     * @return string
     * @throws \ReflectionException
     */
    protected function getTableName($query): string
    {
        if (self::isModel($query)) {
            /** @var Model $query */
            return $query->getTable();

        } elseif (self::isEloquentBuilder($query)) {
            /** @var EloquentBuilder $query */
            return $query->getModel()->getTable();

        } elseif (self::isQueryBuilder($query)) {
            /** @var QueryBuilder $query */
            return $query->from;
        }

        throw new \ReflectionException(__METHOD__ . ' Must be Model or Builder');
    }

    /**
     * 處理 where 條件
     *
     * @param $query
     * @param $method
     * @param $boolean
     * @param  mixed  ...$value
     * @return mixed
     */
    private function executeWhereQuery($query, $method, $boolean, ...$value): mixed
    {
        if (strtolower($boolean) === 'or') {
            return $query->{('or' . ucfirst($method))}(...$value);
        } else {
            return $query->{$method}(...$value);
        }
    }

    /**
     * 選擇多個條件
     *
     * @param $query
     * @param  array  $where
     * @param $boolean
     * @return EloquentBuilder|QueryBuilder
     * @throws \ReflectionException
     */
    private function selectConditions($query, array $where, $boolean = 'and'): EloquentBuilder|QueryBuilder
    {
        $query = $this->convertBuilder($query);
        $operators = ['>', '>=', '=', '<', '<=', '!=', 'like', '~', '!~'];
        $count = 0;
        foreach ($where as $field => $value) {
            if ($count++ > 0) $boolean = 'and';

            if (is_array($value) && count($value) == 3 && in_array($value[1], $operators)) {
                list($field, $condition, $val) = $value;
                if ($condition === '~') {
                    $this->executeWhereQuery($query, 'whereBetween', $boolean, $field, $val);
                } elseif ($condition === '!~') {
                    $this->executeWhereQuery($query, 'whereNotBetween', $boolean, $field, $val);
                } elseif (is_array($val) || $val instanceof Collection) {
                    $this->executeWhereQuery($query, 'whereNotIn', $boolean, $field, $val);
                } else {
                    $this->executeWhereQuery($query, 'where', $boolean, $field, $condition, $val);
                }
            } elseif (is_array($value) || $value instanceof Collection) {
                $this->executeWhereQuery($query, 'whereIn', $boolean, $field, $value);
            } elseif ($value instanceof \Closure)  {
                $this->executeWhereQuery($query, 'where', $boolean, $value);
            } else {
                $this->executeWhereQuery($query, 'where', $boolean, $field, $value);
            }
        }

        return $query;
    }

    /**
     * or 選擇多個條件
     *
     * @param $query
     * @param  array  $where
     * @return EloquentBuilder|QueryBuilder
     * @throws \ReflectionException
     */
    private function orSelectConditions($query, array $where): EloquentBuilder|QueryBuilder
    {
        return $this->selectConditions($query, $where, 'or');
    }

    /**
     * 透過關聯排序
     *
     * @param $query
     * @param $relation
     * @param $column
     * @param string $direction
     */
    private function orderByWith($query, $relation, $column, $direction = 'asc')
    {
        if (is_string($relation)) {
            $relation = $query->getRelationWithoutConstraints($relation);
        }

        if (is_array($relation)) {
            [$relation, $closure] = [array_key_first($relation), collect($relation)->first()];
            $relation = $closure($query->getRelationWithoutConstraints($relation));
        }

        return $query->orderBy(
            $relation->getRelationExistenceQuery(
                $relation->getQuery(),
                $query,
                $column
            ),
            $direction
        );
    }

    /**
     * 透過變形查詢歷程對象
     *
     * @param $query
     * @param  int|string|array  $id
     * @param  string  $modelType
     * @return Inquirer
     */
    private function selectMorphMany($query, int|string|array $id, string $modelType)
    {
        return $this->selectConditions($query, [
            'model_id'   => $id,
            'model_type' => $modelType
        ]);
    }
}
