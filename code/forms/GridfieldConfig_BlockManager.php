<?php
/**
 * GridFieldConfig_BlockManager
 * Provides a reusable GridFieldConfig for managing Blocks.
 *
 * @author Shea Dawson <shea@livesource.co.nz>
 */
class GridFieldConfig_BlockManager extends GridFieldConfig
{
    public $blockManager;

    public function __construct($canAdd = true, $canEdit = true, $canDelete = true, $editableRows = false, $aboveOrBelow = false)
    {
        parent::__construct();

        $this->blockManager = Injector::inst()->get('BlockManager');
        $controllerClass = Controller::curr()->class;
        // Get available Areas (for page) or all in case of ModelAdmin
        if ($controllerClass == 'CMSPageEditController') {
            $currentPage = Controller::curr()->currentPage();
            $areasFieldSource = $this->blockManager->getAreasForPageType($currentPage->ClassName);
        } else {
            $areasFieldSource = $this->blockManager->getAreasForTheme();
        }

		$this->blockManager = Injector::inst()->get('BlockManager');
		$controllerClass = Controller::curr()->class;
		
		// EditableColumns only makes sense on Saveable parents (eg Page), or inline changes won't be saved
		if($editableRows){
			$this->addComponent($editable = new GridFieldEditableColumns());
			
			// Get available Areas (for page) or all in case of ModelAdmin
			$currentPageName = null;
			if($controllerClass == 'CMSPageEditController'){
				$currentPage = Controller::curr()->currentPage();
				$currentPageName = $currentPage->ClassName;
			}
			$displayfields = $this->blockManager->getGridDisplayFields($currentPageName, true, $aboveOrBelow);

			$editable->setDisplayFields($displayfields);
		} else {
			$this->addComponent($dcols = new GridFieldDataColumns());
			
			$displayfields = $this->blockManager->getGridDisplayFields();
			$dcols->setDisplayFields($displayfields);
			$dcols->setFieldCasting(array("UsageListAsString"=>"HTMLText->Raw"));
		}
		
		$this->addComponent(new GridFieldButtonRow('before'));
		$this->addComponent(new GridFieldToolbarHeader());
		$this->addComponent(new GridFieldDetailForm());
		$this->addComponent($sort = new GridFieldSortableHeader());
		$this->addComponent($filter = new GridFieldFilterHeader());
		$this->addComponent(new GridFieldDetailForm());
		if($controllerClass == 'BlockAdmin' && class_exists('GridFieldCopyButton')){
			$this->addComponent(new GridFieldCopyButton());
		}

            $displayfields = array(
                'TypeForGridfield' => array('title' => _t('Block.BlockType', 'Block Type'), 'field' => 'LiteralField'),
                'Title' => _t('Block.Title', 'Title'),
                'BlockArea' => _t('Block.BlockArea', 'Block Area'),
                'isPublishedIcon' => array('title' => _t('Block.IsPublishedField', 'Published'), 'field' => 'LiteralField'),
                'UsageListAsString' => _t('Block.UsageListAsString', 'Used on'),
            );
            $dcols->setDisplayFields($displayfields);
            $dcols->setFieldCasting(array('UsageListAsString' => 'HTMLText->Raw'));

        $this->addComponent(new GridFieldButtonRow('before'));
        $this->addComponent(new GridFieldToolbarHeader());
        $this->addComponent(new GridFieldDetailForm());
        $this->addComponent($sort = new GridFieldSortableHeader());
        $this->addComponent($filter = new GridFieldFilterHeader());
        $this->addComponent(new GridFieldDetailForm());
        if ($controllerClass == 'BlockAdmin' && class_exists('GridFieldCopyButton')) {
            $this->addComponent(new GridFieldCopyButton());
        }

        $filter->setThrowExceptionOnBadDataType(false);
        $sort->setThrowExceptionOnBadDataType(false);

        if ($canAdd) {
            $multiClass = new GridFieldAddNewMultiClass();
            $classes = $this->blockManager->getBlockClasses();
            $multiClass->setClasses($classes);
            $this->addComponent($multiClass);
            //$this->addComponent(new GridFieldAddNewButton());
        }

        if ($canEdit) {
            $this->addComponent(new GridFieldEditButton());
        }

        if ($canDelete) {
            $this->addComponent(new GridFieldDeleteAction(true));
        }

        return $this;
    }

    /**
     * Add the GridFieldAddExistingSearchButton component to this grid config.
     *
     * @return $this
     **/
    public function addExisting()
    {
        $classes = $this->blockManager->getBlockClasses();
        
        $this->addComponent($add = new GridFieldAddExistingSearchButton());
        $add->setSearchList(Block::get()->filter(array(
            'ClassName' => array_keys($classes),
        )));

        return $this;
    }

    /**
     * Add the GridFieldBulkManager component to this grid config.
     *
     * @return $this
     **/
    public function addBulkEditing()
    {
        if (class_exists('GridFieldBulkManager')) {
            $this->addComponent(new GridFieldBulkManager());
        }

        return $this;
    }
}
