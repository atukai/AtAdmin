<?php

namespace AtAdmin\DataGrid\DataSource;

abstract class AbstractDataSource
{
    /**
     * Rows list types
     */
    const LIST_TYPE_PLAIN = 'plain';
    const LIST_TYPE_TREE  = 'tree';

    /**
     * All columns
     *
     * @var array
     */
    protected $columns = array();

    /**
     * @var null|\Zend\Paginator\Paginator
     */
    protected $paginator = null;

    /**
     * Constructor
     */
	public function __construct($options)
	{
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
            $this->setOptions($options);
        } else {
            throw new \Exception(
                'Data source parameters must be in an array or a \Zend\Config\Config object'
            );
        }
	}

    /**
     * @param array $options
     * @return AbstractDataSource
     */
    public function setOptions(array $options)
    {
        unset($options['options']);
        unset($options['config']);

        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * @return null|\Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @abstract
     * @return mixed
     */
    abstract public function loadColumns();

    /**
     * @abstract
     * @param $key
     * @return mixed
     */
    abstract public function getRow($key);
    
    /**
     * @abstract
     * @param $listType
     * @param $order
     * @param $currentPage
     * @param $itemsPerPage
     * @param $pageRange
     * @return mixed
     */
    abstract public function getRows($listType, $order, $currentPage, $itemsPerPage, $pageRange);
    
    /**
     * @abstract
     * @param $data
     * @return mixed
     */
    abstract public function insert($data);
    
    /**
     * @abstract
     * @param $data
     * @param $key
     * @return mixed
     */
    abstract public function update($data, $key);
    
    /**
     * @abstract
     * @param $key
     * @return mixed
     */
    abstract public function delete($key);
}
