<?php


namespace App\Inquirer;

use App\Inquirer\Criteria\CriteriaInterface;
use App\Traits\Inquirer\QueryTrait;
use App\Traits\Inquirer\RequestCriteriaTrait;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Psy\Exception\FatalErrorException;

/**
 * Class Inquirer
 * @package App\Inquirer
 *
 * QueryTrait
 * @method Inquirer selectConditions(array $where, $boolean = 'and')
 * @method Inquirer orSelectConditions(array $where, $boolean = 'or')
 * @method Inquirer orderByWith($relation, $column, $direction = 'asc')
 * @method Inquirer selectMorphMany(int|string|array $id, string $modelType)
 *
 * RequestCriteriaTrait
 * @method Inquirer skipCriteria()
 * @method Inquirer pushCriteria(string|CriteriaInterface $criteria)
 *
 * Builder
 * @method Inquirer select(...$columns)
 * @method Inquirer selectRaw($expression, array $bindings = [])
 * @method Inquirer selectSub($query, $as)
 * @method Inquirer addSelect(...$columns)
 * @method Inquirer distinct()
 * @method Inquirer join($table, $one, $operator = null, $two = null, $type = 'inner', $where = false)
 * @method Inquirer joinWhere($table, $one, $operator, $two, $type = 'inner')
 * @method Inquirer leftJoin($table, $first, $operator = null, $second = null)
 * @method Inquirer leftJoinWhere($table, $one, $operator, $two)
 * @method Inquirer rightJoin($table, $first, $operator = null, $second = null)
 * @method Inquirer rightJoinWhere($table, $one, $operator, $two)
 * @method Inquirer crossJoin($table, $first = null, $operator = null, $second = null)
 * @method Inquirer whereColumn($first, $operator = null, $second = null, $boolean = 'and')
 * @method Inquirer orWhereColumn($first, $operator = null, $second = null)
 * @method Inquirer whereRaw($sql, $bindings = [], $boolean = 'and')
 * @method Inquirer orWhereRaw($sql, array $bindings = [])
 * @method Inquirer whereBetween($column, array $values, $boolean = 'and', $not = false)
 * @method Inquirer orWhereBetween($column, array $values)
 * @method Inquirer whereNotBetween($column, array $values, $boolean = 'and')
 * @method Inquirer orWhereNotBetween($column, array $values)
 * @method Inquirer whereNested(\Closure $callback, $boolean = 'and')
 * @method Inquirer forNestedWhere()
 * @method Inquirer addNestedWhereQuery($query, $boolean = 'and')
 * @method Inquirer whereExists(\Closure $callback, $boolean = 'and', $not = false)
 * @method Inquirer orWhereExists(\Closure $callback, $not = false)
 * @method Inquirer whereNotExists(\Closure $callback, $boolean = 'and')
 * @method Inquirer orWhereNotExists(\Closure $callback)
 * @method Inquirer addWhereExistsQuery(QueryBuilder $query, $boolean = 'and', $not = false)
 * @method Inquirer with($relations)
 * @method Inquirer when($value, callable $callback = null, callable $default = null)
 * @method Inquirer where($column, $operator = null, $value = null, $boolean = 'and')
 * @method Inquirer orWhere($column, $operator = null, $value = null)
 * @method Inquirer whereIn($column, $values, $boolean = 'and', $not = false)
 * @method Inquirer orWhereIn($column, $values)
 * @method Inquirer whereNotIn($column, $values, $boolean = 'and')
 * @method Inquirer orWhereNotIn($column, $values)
 * @method Inquirer whereNull($column, $boolean = 'and', $not = false)
 * @method Inquirer orWhereNull($column)
 * @method Inquirer whereNotNull($column, $boolean = 'and')
 * @method Inquirer orWhereNotNull($column)
 * @method Inquirer whereDate($column, $operator, $value = null, $boolean = 'and')
 * @method Inquirer orWhereDate($column, $operator, $value)
 * @method Inquirer whereTime($column, $operator, $value, $boolean = 'and')
 * @method Inquirer orWhereTime($column, $operator, $value)
 * @method Inquirer whereDay($column, $operator, $value = null, $boolean = 'and')
 * @method Inquirer whereMonth($column, $operator, $value = null, $boolean = 'and')
 * @method Inquirer whereYear($column, $operator, $value = null, $boolean = 'and')
 * @method Inquirer dynamicWhere($method, $parameters)
 * @method Inquirer whereHas($relation, $callback = null, $operator = '>=', $count = 1)
 * @method Inquirer groupBy(...$groups)
 * @method Inquirer having($column, $operator = null, $value = null, $boolean = 'and')
 * @method Inquirer orHaving($column, $operator = null, $value = null)
 * @method Inquirer havingRaw($sql, array $bindings = [], $boolean = 'and')
 * @method Inquirer orHavingRaw($sql, array $bindings = [])
 * @method Inquirer orderBy($column, $direction = 'asc')
 * @method Inquirer latest($column = 'created_at')
 * @method Inquirer oldest($column = 'created_at')
 * @method Inquirer inRandomOrder($seed = '')
 * @method Inquirer orderByRaw($sql, $bindings = [])
 * @method Inquirer offset($value)
 * @method Inquirer skip($value)
 * @method Inquirer limit($value)
 * @method Inquirer take($value)
 * @method Inquirer forPage($page, $perPage = 15)
 * @method Inquirer forPageAfterId($perPage = 15, $lastId = 0, $column = 'id')
 * @method Inquirer union($query, $all = false)
 * @method Inquirer unionAll($query)
 * @method Inquirer lock($value = true)
 * @method Inquirer lockForUpdate()
 * @method Inquirer sharedLock()
 * @method string toSql()
 * @method mixed value($column)
 * @method LengthAwarePaginator paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
 * @method Paginator simplePaginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
 * @method Collection get($columns = ['*'])
 * @method \Illuminate\Database\Eloquent\Collection findMany($ids, $columns = ['*'])
 * @method Model firstOrNew(array $attributes)
 * @method Model firstOrCreate(array $attributes, array $values = [])
 * @method Model|null first($columns = ['*'])
 * @method Model|null firstOrFail($columns = ['*'])
 * @method Model findOrNew($id, $columns = ['*'])
 * @method Model|null find($id, $columns = ['*'])
 * @method Model|null findOrFail($id, $columns = ['*'])
 * @method Model updateOrCreate(array $attributes, array $values = [])
 * @method int getCountForPagination($columns = ['*'])
 * @method bool chunk($count, callable $callback)
 * @method bool chunkById($count, callable $callback, $column = 'id', $alias = null)
 * @method bool each(callable $callback, $count = 1000)
 * @method \Illuminate\Database\Eloquent\Collection pluck($column, $key = null)
 * @method string implode($column, $glue = '')
 * @method bool exists()
 * @method int count($columns = '*')
 * @method string min($column)
 * @method string max($column)
 * @method mixed sum($column)
 * @method mixed avg($column)
 * @method mixed average($column)
 * @method mixed aggregate($function, $columns = ['*'])
 * @method float|int numericAggregate($function, $columns = ['*'])
 * @method bool insert(array $values)
 * @method int insertGetId(array $values, $sequence = null)
 * @method int update(array $values)
 * @method bool updateOrInsert(array $attributes, array $values = [])
 * @method int increment($column, $amount = 1, array $extra = [])
 * @method int decrement($column, $amount = 1, array $extra = [])
 * @method int delete($id = null)
 * @method void truncate()
 * @method Inquirer mergeWheres($wheres, $bindings)
 * @method Expression raw($value)
 * @method array getBindings()
 * @method array getRawBindings()
 * @method Inquirer setBindings(array $bindings, $type = 'where')
 * @method Inquirer addBinding($value, $type = 'where')
 * @method Inquirer mergeBindings(QueryBuilder $query)
 * @method ConnectionInterface getConnection()
 * @method Processor getProcessor()
 * @method Grammar getGrammar()
 * @method string toSqlString(bool $dd = false)
 */
class Inquirer
{
    use QueryTrait;
    use RequestCriteriaTrait;

    protected Model|null $model = null;

    protected EloquentBuilder|null $eloquent = null;

    protected QueryBuilder|null $query = null;

    /**
     * 是否跳過嚴格模式 (預設啟用嚴格模式)
     *
     * @var bool
     */
    protected $skipStrict = false;

    /**
     * 設置哪些方法使用閉包時接收的 query 將轉成 Inquirer
     *
     * @var array
     */
    protected array $convertClosureQueryMethods = [
        'where',
        'when',
        'whereHas'
    ];

    /**
     * @param  null  $building
     * @param  array  $parameters
     * @return Inquirer
     * @throws \ReflectionException
     */
    public static function build($building = null, array $parameters = []): static
    {
        if (is_null($building) || static::isModel($building) || static::isBuilder($building) || static::isRelation($building)) {
            $instance = app(static::class, $parameters)->table($building);
        } elseif (is_string($building) && class_exists($building)) {
            $instance = static::build(app($building, $parameters));
        } else {
            throw new \ReflectionException('could not build valid Inquirer');
        }

        if (static::checkUseCriteria($instance)) {
            /** @var Inquirer|RequestCriteriaTrait $instance */
            $instance->bootCriteria();
        }

        return $instance;
    }

    /**
     * 輸出結果的 Methods
     *
     * @var array
     */
    protected array $resultMethods = [
        'all',
        'first',
        'get',
        'paginate',
        'simplePaginate',
        'find',
        'count',
        'getCountForPagination',
        'value',
        'chunk',
        'chunkById',
        'each',
        'pluck',
        'implode',
        'min',
        'max',
        'sum',
        'avg',
        'average',
        'aggregate',
        'numericAggregate',
        'toSqlString',
        'toSql',
        'exists'
    ];

    /**
     * 檢查實例是否有使用Criteria
     *
     * @param $instance
     * @return bool
     */
    protected static function checkUseCriteria($instance): bool
    {
        return in_array(RequestCriteriaTrait::class, class_uses_recursive($instance));
    }

    /**
     * @param  Model  $model
     * @return $this
     */
    protected function setModel(Model $model): static
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @param  EloquentBuilder  $eloquent
     * @return $this
     */
    protected function setEloquent(EloquentBuilder $eloquent): static
    {
        $this->eloquent = $eloquent;
        return $this;
    }

    /**
     * @param  QueryBuilder  $query
     * @return $this
     */
    protected function setQuery(QueryBuilder $query): static
    {
        $this->query = $query;
        return $this;
    }

    /**
     * 設置查詢對象
     *
     * @param mixed $building
     * @return $this
     */
    protected function table(mixed $building): static
    {
        if (is_string($building)) {
            $building = resolve($building);
        }

        if (static::isModel($building)) {
            /** @var Model $building */
            $this->setModel($building);
            $this->setEloquent($building->newModelQuery());
            $this->setQuery($this->eloquent->getQuery()->newQuery());

        } elseif (static::isEloquentBuilder($building)) {
            /** @var EloquentBuilder $building */
            $this->setModel($building->getModel());
            $this->setEloquent($building);
            $this->setQuery($building->getQuery());

        } elseif (static::isQueryBuilder($building)) {
            /** @var QueryBuilder $building */
            $this->setQuery($building);

        } elseif (static::isRelation($building)) {
            /** @var Relation $building */
            $this->setModel($building->getModel());
            $this->setEloquent($building->getQuery());
            $this->setQuery($building->getBaseQuery());
        }

        return $this;
    }

    /**
     * @return Model|null
     */
    public function getModel(): Model|null
    {
        return $this->model;
    }

    /**
     * @return EloquentBuilder|null
     */
    public function getEloquent(): EloquentBuilder|null
    {
        return $this->eloquent;
    }

    /**
     * @return QueryBuilder|null
     */
    public function getQuery(): QueryBuilder|null
    {
        return $this->getEloquent() ? $this->getEloquent()->getQuery() : $this->query;
    }

    /**
     * clone一個查詢器
     *
     * @return static
     */
    public function copy(): static
    {
        $instance = clone $this;
        $instance->setModel(clone $this->getModel());
        $instance->setEloquent(clone $this->getEloquent());
        $instance->setQuery(clone $this->getQuery());

        return $instance;
    }

    /**
     * 跳過嚴格模式
     *
     * @return $this
     */
    public function skipStrict(): static
    {
        $this->skipStrict = true;

        return $this;
    }

    /**
     * 重置搜尋器
     *
     * @return $this
     */
    public function reset(): static
    {
        $this->getEloquent() && $this->setEloquent($this->model->newModelQuery());
        $this->getQuery() && $this->setQuery($this->query->newQuery());
        if ($this->skipStrict) {
            change_db_strict(true);
            $this->skipStrict = false;
        }

        return $this;
    }

    /**
     * 將閉包返回的 query 轉換為 Inquirer class
     *
     * @param  array  $arguments
     * @return array
     */
    protected function convertClosureQueryToInquirer(array $arguments): array
    {
        foreach ($arguments as $index => $argument) {
            if ($argument instanceof \Closure) {
                $arguments[$index] = fn ($query) => $argument(static::build($query));
            }
        }

        return $arguments;
    }

    /**
     * @param  string  $method
     * @param  array  $arguments
     * @return mixed
     * @throws FatalErrorException
     */
    public function __call(string $method, array $arguments): mixed
    {
        if (method_exists($this, $method)) {
            $this->callMethod($this, $method, $this->getEloquent() ?? $this->getQuery(), ...$arguments);
            return $this;

        } elseif ($this->getEloquent() && (method_exists($this->getEloquent(), $method) || $this->getEloquent()->hasMacro($method))) {
            $result = $this->callMethod($this->getEloquent(), $method, ...$arguments);
            return static::isBuilder($result) ? $this : $this->completed($method, $result);

        } elseif ($this->getQuery() && (method_exists($this->getQuery(), $method) || $this->getQuery()->hasMacro($method))) {
            $result = $this->callMethod($this->getQuery(), $method, ...$arguments);
            return static::isBuilder($result) ? $this : $this->completed($method, $result);
        }

        throw new FatalErrorException(new \Exception('Call to undefined method ' . __class__ . '::' . $method . '()'));
    }

    /**
     * @param $instance
     * @param  string  $method
     * @param  mixed  ...$arguments
     * @return mixed
     */
    protected function callMethod($instance, string $method, ...$arguments): mixed
    {
        if (in_array($method, $this->convertClosureQueryMethods)) {
            $arguments = $this->convertClosureQueryToInquirer($arguments);
        }

        if (in_array($method, $this->resultMethods)) {
            /** @var Inquirer|RequestCriteriaTrait $this */
            $this->applyEachCriteria();
            $this->skipStrict && change_db_strict(false);
        }

        return $instance->{$method}(...$arguments);
    }

    /**
     * 查詢完畢後處理
     *
     * @param $method
     * @param $result
     * @return mixed
     */
    private function completed($method, $result): mixed
    {
        $this->reset();

        return $result;
    }

    protected function bootCriteria(): void
    {
        // TODO: Implement bootCriteria() method.
    }
}
