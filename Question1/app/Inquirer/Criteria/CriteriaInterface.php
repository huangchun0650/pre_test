<?php


namespace App\Inquirer\Criteria;


use App\Inquirer\Inquirer;

interface CriteriaInterface
{
    public function apply(Inquirer $inquirer): void;
}
