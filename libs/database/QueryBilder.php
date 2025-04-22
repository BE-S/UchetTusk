<?php

namespace Libs\Database;

use Libs\Database\EscapeSql;

/*
	Класс реилизован для того, чтобы убрать sql код из основной части проекта.
	Каждый метод записывает в переменную sql код и после вызова метода get в
	бд отправляется построенный sql запрос.
*/

abstract class QueryBilder extends Database
{
	private $table;
	private $columns;
	private $data;
	
	private $optionDistinct;
	private $optionSelect;
    private $optionJoin;
	private $optionWhere;
	private $optionLimit;
	private $optionOrderBy;
	private $optionGroupBy;

	function __construct()
	{
        $reflection = new \ReflectionClass($this);
        $this->table = strtolower($reflection->getShortName());

        parent::__construct();
	}
	
	private function tableName($class) //Преобразует имя класса в имя таблицы.
	{
		return strtolower($class);
	}
	
	private function selectOptionsCreate($values) //Метод преобразует переданные данные в строку
	{
		$columns = "";
		$data = "";
		$i = 0;
		
		foreach ($values as $column => $value) {
			$columns = $columns . $column;
			$data = $data . "'" . $value . "'";
			++$i;
			
			if ($i < count($values)) {
				$columns .= ",";
				$data .= ",";
			}
		}
		
		$this->columns = EscapeSql::escape($columns);
		$this->data = EscapeSql::escape($data);
	}
	
	private function selectOptionsUpdate($values) //Метод преобразует переданные данные в строку
	{
		$data = "";
		$i = 0;
		
		foreach ($values as $column => $value) {
			$data = $data . $column . "=" . "'" . $value . "'";
			++$i;
			
			if ($i < count($values)) {
				$data .= ",";
			}
		}
		
		$this->data = EscapeSql::escape($data);
	}
	
	private function addOperator($conditions, string $operator) //Метод преобразует переданные данные в строку
	{
		$data = "";
		$i = 0;
		
		foreach ($conditions as $condition) {
			if ($i > 0) {
				$data .= " " . $operator . " ";
			}
			
			$data = $data . EscapeSql::escape($condition[0]) . " " . EscapeSql::escape($condition[1]) . " " . "'" . EscapeSql::escape($condition[2]) . "'";
			++$i;
		}
		
		$this->data = EscapeSql::escape($data);
	}
	
	private function clearOptions() //Очищает переменные в которых хранятся части sql запроса
	{
		$this->optionDistinct = null;
		$this->optionSelect = null;
        $this->optionJoin = null;
		$this->optionWhere = null;
		$this->optionLimit = null;
		$this->optionOrderBy = null;
		$this->optionGroupBy = null;
	}

    public function first()
    {
        $result = $this->get();

        return $result[0] ?? [];
    }
	
	public function get() // Используя переменные класса в которых хранятся части sql кода, этот класс строит полноценный sql запрос
	{
		$sql = "select ";
		
		if ($this->optionDistinct) {
			$sql = $sql . $this->optionDistinct . " ";
		}
		if ($this->optionSelect) {
			$sql = $sql . $this->optionSelect;
		} else {
			$sql = $sql . "*";
		}
		
		$sql = $sql . " from " . $this->table . " ";

        if ($this->optionJoin) {
            $sql = $sql . $this->optionJoin . " ";
        }
		if ($this->optionWhere) {
			$sql = $sql . $this->optionWhere . " ";
		}
		if ($this->optionOrderBy && !$this->optionGroupBy) {
			$sql = $sql . $this->optionOrderBy . " ";
		}
		if ($this->optionGroupBy && !$this->optionOrderBy) {
			$sql = $sql . $this->optionGroupBy . " ";
		}
		
		if ($this->optionLimit) {
			$sql = $sql . $this->optionLimit;
		}
		
		$this->clearOptions();

		return $this->transferSql($sql);
	}
	
	public function find(int $id) //Используется для нахождения записи по его id
	{
		$result = $this->where('id', '=', $id)->get();
		
		return $result[0];
	}
	
	public function limit(int $valueLimit, int $valueOffset = null) //Устанавливает лимит записей
	{
		$this->optionLimit = "limit $valueLimit";

        if ($valueOffset) {
            $this->optionLimit .= ", $valueOffset";
        }

		return $this;
	}
	
	public function where($column, $operator, $value) //Поиск по полю
	{
		$this->optionWhere = "where " . EscapeSql::escape($column) . " " . EscapeSql::escape($operator) . " '" . EscapeSql::escape($value) . "'";
		
		return $this;
	}
	
	public function multipleWhere(array $conditions) //Этот метод реализует поиск по нескольким полям
	{
		$this->addOperator($conditions, "and");
		$this->optionWhere = "where " . $this->data;

		return $this;
	}
	
	public function orderBy(array|string $columns, string $filter = "asc") //Сортировка
	{
        // Проверка на asc или desc
		$columns = is_array($columns) ? implode(",", $columns) : $columns;
		$this->optionOrderBy = "order by " . EscapeSql::escape($columns) . " " . $filter;
		
		return $this;
	}
	
	public function select(array|string $columns) //Выбор полей
	{
		$columns = is_array($columns) ? implode(",", $columns) : $columns;
		$this->optionSelect = EscapeSql::escape($columns);
		
		return $this;
	}
	
	public function allCountRows() //Получить количество записей в бд (Такой же результат можно получить и через метод select)
	{
		$sql = "select count(*) as cnt from " . $this->table;
		$result = $this->transferSql($sql);
		
		return $result[0]->cnt;
	}

    public function join(string $table, string $columnFirst, string $comparison, string $columnSecond) //объединить табилицы
    {
        $this->optionJoin = "join $table on $columnFirst $comparison $columnSecond";

        return $this;
    }
	
	public function create(array $values) //Создать новую запись в бд
	{
		$this->selectOptionsCreate($values);
		$sql = "INSERT INTO " . $this->table . "(" . $this->columns . ") values (" . $this->data . ")";
		$this->transferSql($sql);
		
		return true;
	}
	
	public function update(int $id, array $values) //Обновить запись в бд
	{
		$this->selectOptionsUpdate($values);
		$sql = "update " . $this->table . " set "  . $this->data  . " where id = " . $id;
		$this->transferSql($sql);
		
		return true;
	}
	
	public function remove(int $id) //Удалить запись в бд
	{
		$sql = "delete from " . $this->table . " where id = " . $id;
		$this->transferSql($sql);
		
		return true;
	}
}