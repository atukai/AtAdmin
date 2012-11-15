<?php

namespace AtAdmin\DataGrid\DataSource;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use AtAdmin\DataGrid\Column;

class ZendDbTableGateway extends AbstractDataSource
{
    /**
     * @var null
     */
    protected $dbAdapter = null;

    /**
     * @var null|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway = null;

    /**
     * @var \Zend\Db\Sql\Select
     */
    protected $select = null;

    /**
     * Base table columns
     *
     * @var array
     */
    protected $tableColumns = array();

    /**
     * Joined tables
     *
     * @var array
     */
    protected $joinedTables = array();

    /**
     * Joined table columns
     *
     * @var array
     */
    protected $joinedColumns = array();

    /**
     * @param $options
     */
	public function __construct($options)
	{
		parent::__construct($options);

        //$this->tableGateway = new TableGateway($options['table'], $this->getDbAdapter(), new Feature\MetadataFeature());
        $this->tableGateway = new TableGateway($options['table'], $this->getDbAdapter());
        $this->select = $this->tableGateway->getSql()->select();
        $this->columns = $this->loadColumns();
	}

    /**
     * @param $adapter
     * @return ZendDbTableGateway
     */
    public function setDbAdapter($adapter)
    {
        $this->dbAdapter = $adapter;
        return $this;
    }

    /**
     * @return null
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @return null|\Zend\Db\TableGateway\TableGateway
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
    }

    /**
     * @return null|\Zend\Db\Sql\Select
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * Join other table and collect joined columns
     *
     * @param $tableClass
     * @param $alias
     * @param $on
     * @param null $columns
     * @return void
     */
    public function with($tableClass, $alias, $keyName, $foreignKeyName, $columns = null)
    {
        $joinTable = new $tableClass;
        $joinTableName = $joinTable->info(Zend_Db_Table_Abstract::NAME);
        $joinTableCols = $joinTable->info(Zend_Db_Table_Abstract::COLS);

        $this->joinedTables[$alias] = $tableClass;

        // Колонки из приджойненных таблиц
        $joinedColumns = array();        
        
        foreach ($joinTableCols as $col) {
        	// Добавляем только указанные колонки
            if (null != $columns) {
                if (in_array($col, $columns)) {
                   $joinedColumns[] = $alias . '.' . $col . ' AS ' . $alias . '__' . $col;
                   $this->joinedColumns[] = $alias . '__' . $col;
                }
            // Добавляем все колонки    
            } else {
                $joinedColumns[] = $alias . '.' . $col . ' AS ' . $alias . '__' . $col;
                $this->joinedColumns[] = $alias . '__' . $col;
            }
        }

        $this->getSelect()->join(
            array($alias => $joinTableName),
            $this->getTable()->getName(). '.' . $keyName . '='. $alias . '.' . $foreignKeyName,
            $joinedColumns);
    }

    /**
     * @return array
     */
    public function loadColumns()
    {
        $tableMetadata = new \Zend\Db\Metadata\Metadata($this->getDbAdapter());
        $baseTableColumns = $tableMetadata->getColumns($this->getTableGateway()->getTable());
        //$baseTableColumns = $this->getTableGateway()->getColumns();

        // Setup default settings for base table column fields
        foreach ($baseTableColumns as $columnObject) {
            $columnName = $columnObject->getName();
        	$columnDataType = $columnObject->getDataType();

            $this->tableColumns[] = $columnName;

        	switch (true) {
        		case in_array($columnDataType, array('datetime', 'timestamp', 'time')):
        		    $column = new Column\DateTime($columnName);
        		    break;
        		    
                case in_array($columnDataType, array('date', 'year')):
                    $column = new Column\Date($columnName);
                    break;

                case in_array($columnDataType, array('mediumtext', 'text')):
                    $column = new Column\Textarea($columnName);
                    break;

                default:
        			$column = new Column\Literal($columnName);
      		        break;
        	}

            $column->setLabel($columnName);

            $columns[$columnName] = $column;
        }

        // Setup default settings for joined table column fields
        foreach ($this->joinedColumns as $columnName) {
		    $column = new Column\Literal($columnName);
            $column->setLabel($columnName);

            $columns[$columnName] = $column;
       	}

        //$this->setCommentAsLabel($columns);

        return $columns;
    }

    /**
     * @param $columns
     * @return void
     */
    protected function setCommentAsLabel($columns)
    {
        // Get current database name
        $query = 'SELECT DATABASE();';
        $schema = $this->getTableGateway()->getAdapter()->fetchOne($query);

        // Set table field comments as column label.
        $select = $this->getTableGateway()->getAdapter()->select();
        $select->from('information_schema.COLUMNS', array('name' => 'COLUMN_NAME', 'comment' => 'COLUMN_COMMENT'));
        $select->where('TABLE_SCHEMA = ?', $schema);
        $select->where('TABLE_NAME = ?', $this->getTableGateway()->getTable());
        
        $columnsInfo = $select->query()->fetchAll();
        $select->reset(); // ???
        
        if ($columnsInfo) {
            foreach ($columnsInfo as $column) {
                if (!empty($column['comment'])) {
                    $columns[$column['name']]->setLabel($column['comment']);
                }
            }
        }
    }

    /**
     * Return row by primary key
     */
    public function getRow($key)
    {
        if (is_array($key)) {
            return $this->getTableGateway()->find($key);
        }

        return $this->getTableGateway()->find($key)->current();
    }
    
    /**
     * @param $listType
     * @param $order
     * @param $currentPage
     * @param $itemsPerPage
     * @param $pageRange
     * @return array|Traversable
     */
    public function getRows($listType, $order, $currentPage, $itemsPerPage, $pageRange)
    {
    	if ($listType == AbstractDataSource::LIST_TYPE_PLAIN) {
	        $select = $this->getSelect();

            if ($order) {
                $select->order($order);
            }

	        $this->paginator = new \Zend\Paginator\Paginator(
                new \Zend\Paginator\Adapter\DbSelect($select, $this->getDbAdapter())
            );
	        $this->paginator->setCurrentPageNumber($currentPage)
                            ->setItemCountPerPage($itemsPerPage)
                            ->setPageRange($pageRange);

	        return $this->paginator->getItemsByPage($currentPage);
	    		
    	} elseif ($listType == AbstractDataSource::LIST_TYPE_TREE) {
    	    $items = $this->getTableGateway()->fetchAll();
            // @todo: implement forming data for tree
            return $items;
    	}
    }

    /**
     * Return only columns which present in table
     *
     * @param array $data
     * @return array
     */
    protected function cleanDataForSql($data = array())
    {
        $cleanData = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $this->tableColumns)) {
                $cleanData[$key] = $value;
            }
        }

        return $cleanData;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function insert($data)
    {
    	$table = $this->getTableGateway();
        $table->insert($this->cleanDataForSql($data));

        return $table->getLastInsertValue();
    }

    /**
     * @param $data
     * @param $key
     * @return mixed|void
     */
    public function update($data, $key)
    {
    	$rowset = $this->getRow($key);

        if ($rowset instanceof Zend_Db_Table_Rowset) {
            foreach($rowset as $row) {
                $row->setFromArray($data);
                $row->save();
            }

        } else {
            $row = $rowset;
            $row->setFromArray($data);
            $row->save();
        }

        return $key;
    }

    /**
     * @param $key
     * @return mixed|void
     */
    public function delete($key)
    {
        $this->getTableGateway()->find($key)->current()->delete();
    }    
}