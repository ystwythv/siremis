<?php
/**
 * PHPOpenBiz Framework
 *
 * LICENSE
 *
 * This source file is subject to the BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @package   openbiz.bin.easy
 * @copyright Copyright &copy; 2005-2009, Rocky Swen
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @link      http://www.phpopenbiz.org/
 * @version   $Id$
 */

/**
 * Panel class is the base class of Panel that embeded in EasyForm
 *
 * @package openbiz.bin.easy
 * @author Rocky Swen
 * @copyright Copyright (c) 2005-2009
 * @since 1.2
 * @access public
 */
class Panel extends MetaIterator implements iUIControl
{ 
	public $m_FormName;
	
	public function __construct(&$xmlArr, $childClassName, $parentObj=null)
    {
    	parent::__construct($xmlArr, $childClassName, $parentObj);
    	$this->m_FormName = $parentObj->m_Name;    	
    }
    
    protected function getFormObj()
    {
        return BizSystem::objectFactory()->getObject($this->m_FormName);
    }	
    /**
     * Render the Panel and return array of rendering element (html text)
     *
     * @return string - html text
     */
    public function render()
    {
        $panel = array();

        /* @var $elem Element */
        foreach($this->m_var as $elem)
        {
            if ($elem->canDisplayed())
            {
                $panel[$elem->m_Name]['element'] = $elem->render();
                $panel[$elem->m_Name]['type'] = $elem->m_Class;
                $panel[$elem->m_Name]['elementset'] = $elem->m_ElementSet;
                if (isset($elem->m_Label) && $elem->m_Label !== null)
                    $panel[$elem->m_Name]['label'] = $elem->renderLabel();
                if (isset($elem->m_Value) && $elem->m_Value !== null)
                    $panel[$elem->m_Name]['value'] = $elem->m_Value;
                if (isset($elem->m_Description) && $elem->m_Description !== null)
                    $panel[$elem->m_Name]['description'] = $elem->m_Description;
                if (isset($elem->m_Required))
                    $panel[$elem->m_Name]['required'] = $elem->m_Required;
                if (isset($elem->m_ColumnStyle))
                    $panel[$elem->m_Name]['colstyle'] = $elem->m_ColumnStyle;
            }
        }

        return $panel;
    }

    /**
     * Render record
     *
     * @param array $recArr
     * @return string - html text
     */
    public function renderRecord(&$recArr)
    {
        if ($recArr)
            $this->setRecordArr($recArr);
        return $this->render();
    }

    /**
     * Render table and return an array with 3 sub arraies (elems, data, ids)
     *
     * @param array $recSet
     * @return array
     */
    public function renderTable(&$recSet)
    {
        $table['elems'] = $this->render();

        $counter = 0;
        while (true)
        {
            $arr = $recSet[$counter];
            
            if (!$arr) break;

            foreach($this->m_var as $elem)  // reset the elements
                $elem->reset();
            $this->setRecordArr($arr);
            $table['ids'][] = $arr['Id'];

            /* @var $elem Element */
            foreach($this->m_var as $elem)
            {
                if ($elem->canDisplayed())
                    $tableRow[$elem->m_Name] = $elem->render();
            }
            $table['data'][] = $tableRow;
            unset($tableRow);
            $counter++;
        }
        return $table;
    }

    /**
     * Set record array
     * TODO: change field=>value to element=>value
     *
     * @param array $recArr
     * @return void
     */
    public function setRecordArr(&$recArr)
    {
    	if(!$recArr)
    		return ;
        // reset elements first to avoid use stale data
        foreach ($this->m_var as $elem)
        	$elem->reset();
        /* @var $elem Element */
        $this->getFormObj()->setFormInputs();
        foreach ($this->m_var as $elem)
        {
            //if (!$recArr)
            //    $elem->setValue("");
            if (key_exists($elem->m_FieldName, $recArr)) {
                $elem->setValue($recArr[$elem->m_FieldName]);
            }
            else if (key_exists($elem->m_Name, $recArr)) {
                $elem->setValue($recArr[$elem->m_Name]); 
            }
        }        
    }

    /**
     * Get element by field,
     *
     * @param <type> $fieldName
     * @todo change name to getElementByField() or Add new wrapping method
     * @return Element
     */
    public function getByField($fieldName)
    {
        /* @var $elem Element */
        foreach ($this->m_var as $elem)
        {
            if($elem->m_FieldName == $fieldName && $elem->m_Class!='RowCheckbox')
            {
                return $elem;
            }
        }
    }
}
?>