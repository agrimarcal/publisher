<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright       The XUUPS Project http://sourceforge.net/projects/xuups/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         Publisher
 * @subpackage      Blocks
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 * @author          phppp
 * @version         $Id: search.php 10374 2012-12-12 23:39:48Z trabis $
 */

// defined("XOOPS_ROOT_PATH") || exit("XOOPS root path not defined");

include_once dirname(__DIR__) . '/include/common.php';

/**
 * @param $options
 *
 * @return array
 */
function publisher_search_show($options) {
    $block      = array();
    $publisher  = PublisherPublisher::getInstance();
    $categories = $publisher->getHandler('category')->getCategoriesForSearch();
    if (count($categories) == 0) {
        return $block;
    }

    xoops_loadLanguage('search');

    $andor    = XoopsRequest::getString('andor', XoopsRequest::getString('andor', '', 'GET'), 'POST');
    $username = XoopsRequest::getString('uname', XoopsRequest::getString('uname', null, 'GET'), 'POST');
//  $searchin = isset($_POST["searchin"]) ? $_POST["searchin"] : (isset($_GET["searchin"]) ? explode("|", $_GET["searchin"]) : array());
//  $searchin = XoopsRequest::getArray('searchin', (explode("|", XoopsRequest::getString('searchin', array(), 'GET'))), 'POST');

    $searchin = XoopsRequest::getArray('searchin', '', 'POST');
    if (!isset($searchin)) {
        $searchin = XoopsRequest::getString('searchin', array(), 'GET');
        $searchin = isset($searchin) ? explode("|", $searchin) : array();
    }

    $sortby   = XoopsRequest::getString('sortby', XoopsRequest::getString('sortby', null, 'GET'), 'POST');
    $term     = XoopsRequest::getString('term', XoopsRequest::getString('term', '', 'GET'));

    //mb TODO simplify next lines with category
    $category = XoopsRequest::getArray('category', array(), 'POST') ? XoopsRequest::getArray('category', array(), 'POST') : (XoopsRequest::getArray('category', null, 'GET'));
    if (empty($category) || (is_array($category) && in_array("all", $category))) {
        $category = array();
    } else {
        $category = (!is_array($category)) ? explode(",", $category) : $category;
        $category = array_map("intval", $category);
    }

    $andor  = (in_array(strtoupper($andor), array("OR", "AND", "EXACT"))) ? strtoupper($andor) : "OR";
    $sortby = (in_array(strtolower($sortby), array("itemid", "datesub", "title", "categoryid"))) ? strtolower($sortby) : "itemid";

    /* type */
    $type_select = "<select name=\"andor\">";
    $type_select .= "<option value=\"OR\"";
    if ("OR" == $andor) {
        $type_select .= " selected=\"selected\"";
    }
    $type_select .= ">" . _SR_ANY . "</option>";
    $type_select .= "<option value=\"AND\"";
    if ("AND" == $andor) {
        $type_select .= " selected=\"selected\"";
    }
    $type_select .= ">" . _SR_ALL . "</option>";
    $type_select .= "<option value=\"EXACT\"";
    if ("exact" == $andor) {
        $type_select .= " selected=\"selected\"";
    }
    $type_select .= ">" . _SR_EXACT . "</option>";
    $type_select .= "</select>";

    /* category */

    $select_category = "<select name=\"category[]\" size=\"5\" multiple=\"multiple\" width=\"150\" style=\"width:150px;\">";
    $select_category .= "<option value=\"all\"";
    if (empty($category) || count($category) == 0) {
        $select_category .= "selected=\"selected\"";
    }
    $select_category .= ">" . _ALL . "</option>";
    foreach ($categories as $id => $cat) {
        $select_category .= "<option value=\"" . $id . "\"";
        if (in_array($id, $category)) {
            $select_category .= "selected=\"selected\"";
        }
        $select_category .= ">" . $cat . "</option>";
    }
    unset($id, $cat);
    $select_category .= "</select>";

    /* scope */
    $searchin_select = "";
    $searchin_select .= "<input type=\"checkbox\" name=\"searchin[]\" value=\"title\"";
    if (is_array($searchin) && in_array("title", $searchin)) {
        $searchin_select .= " checked";
    }
    $searchin_select .= " />" . _CO_PUBLISHER_TITLE . "&nbsp;&nbsp;";
    $searchin_select .= "<input type=\"checkbox\" name=\"searchin[]\" value=\"subtitle\"";
    if (is_array($searchin) && in_array("subtitle", $searchin)) {
        $searchin_select .= " checked";
    }
    $searchin_select .= " />" . _CO_PUBLISHER_SUBTITLE . "&nbsp;&nbsp;";
    $searchin_select .= "<input type=\"checkbox\" name=\"searchin[]\" value=\"summary\"";
    if (is_array($searchin) && in_array("summary", $searchin)) {
        $searchin_select .= " checked";
    }
    $searchin_select .= " />" . _CO_PUBLISHER_SUMMARY . "&nbsp;&nbsp;";
    $searchin_select .= "<input type=\"checkbox\" name=\"searchin[]\" value=\"text\"";
    if (is_array($searchin) && in_array("body", $searchin)) {
        $searchin_select .= " checked";
    }
    $searchin_select .= " />" . _CO_PUBLISHER_BODY . "&nbsp;&nbsp;";
    $searchin_select .= "<input type=\"checkbox\" name=\"searchin[]\" value=\"keywords\"";
    if (is_array($searchin) && in_array("meta_keywords", $searchin)) {
        $searchin_select .= " checked";
    }
    $searchin_select .= " />" . _CO_PUBLISHER_ITEM_META_KEYWORDS . "&nbsp;&nbsp;";
    $searchin_select .= "<input type=\"checkbox\" name=\"searchin[]\" value=\"all\"";
    if ((is_array($searchin) && in_array("all", $searchin)) || empty($searchin)) {
        $searchin_select .= " checked";
    }
    $searchin_select .= " />" . _ALL . "&nbsp;&nbsp;";

    /* sortby */
    $sortby_select = "<select name=\"sortby\">";
    $sortby_select .= "<option value=\"itemid\"";
    if ("itemid" == $sortby || empty($sortby)) {
        $sortby_select .= " selected=\"selected\"";
    }
    $sortby_select .= ">" . _NONE . "</option>";
    $sortby_select .= "<option value=\"datesub\"";
    if ("datesub" == $sortby) {
        $sortby_select .= " selected=\"selected\"";
    }
    $sortby_select .= ">" . _CO_PUBLISHER_DATESUB . "</option>";
    $sortby_select .= "<option value=\"title\"";
    if ("title" == $sortby) {
        $sortby_select .= " selected=\"selected\"";
    }
    $sortby_select .= ">" . _CO_PUBLISHER_TITLE . "</option>";
    $sortby_select .= "<option value=\"categoryid\"";
    if ("categoryid" == $sortby) {
        $sortby_select .= " selected=\"selected\"";
    }
    $sortby_select .= ">" . _CO_PUBLISHER_CATEGORY . "</option>";
    $sortby_select .= "</select>";

    $block["type_select"]     = $type_select;
    $block["searchin_select"] = $searchin_select;
    $block["category_select"] = $select_category;
    $block["sortby_select"]   = $sortby_select;
    $block["search_term"]     = $term;
    $block["search_user"]     = $username;
    $block["publisher_url"]   = PUBLISHER_URL;

    return $block;
}