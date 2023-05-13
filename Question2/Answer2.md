# PHP 當中的 interface 和 abstract ，分別適合於什麼時機使用。請描述對於這兩個保留字的看法。

**Answer:**
interface 和 abstract 都是抽象化的機制

interface ：用於定義、處理 耦合類之間的依賴關係，實現多重繼承。然後 interface 是純抽象類型，只定義名稱 返回值的類型 參數，沒有實際去實現的方法。

ex: Q1專案中 定義 interface 方式，如 查詢器 Inquirer 中形式 表示：
路徑:``pre_test/Question1/app/Inquirer/Criteria/CriteriaInterface.php``

後續 implements 關鍵字 遵從 ``CriteriaInterface`` interface。
路徑:``pre_test/Question1/app/Inquirer/Criteria/SearchByNameCriteria.php``

abstract ：用於實現模板化的方法，讓子類按你寫的規範去實現方法。

要定義類別的共享行為時，會使用interface；要定義類別的基礎方法，則使用abstract。


ex: Q1專案中 定義 abstract 方式，如 composer 套件中形式 表示：
路徑:``pre_test/Question1/vendor/huang_chun/transform-api/src/Transform.php``