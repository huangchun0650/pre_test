<?php


namespace App\Inquirer\Criteria;

use App\Inquirer\Inquirer;
use Illuminate\Support\Facades\Request;

/**
 * 以名稱當條件搜尋
 */
class SearchByNameCriteria implements CriteriaInterface
{
    public function apply(Inquirer $inquirer): void
    {
        $name = Request::get('name');
        $inquirer->when(filled($name), function ($query) use ($name) {
            $query->where('name', 'like', "%{$name}%")
                ->orWhere('lang_zh_tw', 'like', "%{$name}%")
                ->orWhere('lang_zh_cn', 'like', "%{$name}%")
                ->orWhere('lang_en_us', 'like', "%{$name}%");
        });
    }
}
