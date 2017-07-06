<?php

namespace SheaDawson\Blocks\Extensions;

use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Extension;

class BlocksContentControllerExtension extends Extension
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'handleBlock',
    ];

    public function onAfterInit()
    {
    	$ssAdmin = ModuleLoader::getModule('silverstripe-admin');
    	$blocksModule = ModuleLoader::getModule('blocks');
        if ($this->owner->data()->canEdit() && $this->owner->getRequest()->getVar('block_preview') == 1) {
            Requirements::javascript($ssAdmin->getRelativeResourcePath('thirdparty/jquery/jquery.js'));
            Requirements::javascript($blocksModule->getRelativeResourcePath('javascript/block-preview.js'));
            Requirements::css($blocksModule->getRelativeResourcePath('css/block-preview.css'));
        }
    }

    /**
     * Handles blocks attached to a page
     * Assumes URLs in the following format: <URLSegment>/block/<block-ID>.
     *
     * @return RequestHandler
     */
    public function handleBlock()
    {
        if ($id = $this->owner->getRequest()->param('ID')) {
            $blocks = $this->owner->data()->getBlockList(null, true, true, true);
            if ($block = $blocks->find('ID', $id)) {
                return $block->getController();
            }
        }

        return $block->getController();
    }
}
