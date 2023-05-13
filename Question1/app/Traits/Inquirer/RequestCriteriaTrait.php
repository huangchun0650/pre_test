<?php
namespace App\Traits\Inquirer;

use App\Inquirer\Criteria\CriteriaInterface;

trait RequestCriteriaTrait
{
    protected array $criteria = [];

    /**
     * 初始定義使用哪些 Criteria
     *
     * @return void
     */
    abstract protected function bootCriteria(): void;

    /**
     * 跳過使用Criteria
     *
     * @return $this
     */
    public function skipCriteria(): static
    {
        $this->criteria = [];
        return $this;
    }

    /**
     * 注入 Criteria
     *
     * @param  string|CriteriaInterface  $criteria
     * @return $this
     */
    public function pushCriteria(string|CriteriaInterface $criteria): static
    {
        if (is_string($criteria)) {
            $criteria = app($criteria);
        }

        $this->criteria[] = $criteria;

        return $this;
    }

    /**
     * 移除 Criteria
     *
     * @param  string|CriteriaInterface  $criteria
     * @return RequestCriteriaTrait
     */
    public function pullCriteria(string|CriteriaInterface $criteria): static
    {
        if (is_string($criteria)) {
            $criteria = app($criteria);
        }

        $this->criteria = collect($this->criteria)->reject(function ($item) use ($criteria) {
            return get_class($item) === get_class($criteria);
        })->values()->toArray();

        return $this;
    }

    /**
     * 應用每一個 Criteria
     */
    protected function applyEachCriteria()
    {
        /** @var CriteriaInterface $criteria */
        foreach ($this->criteria as $criteria) {
            $criteria->apply($this);
        }
    }
}
