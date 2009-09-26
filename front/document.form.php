<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2009 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: Julien Dombre
// Purpose of file:
// ----------------------------------------------------------------------


$NEEDED_ITEMS = array ('budget', 'cartridge', 'computer', 'consumable', 'contact', 'contract',
   'document', 'enterprise', 'entity', 'knowbase', 'monitor', 'networking', 'peripheral', 'phone',
   'printer', 'software', 'tracking');

define('GLPI_ROOT', '..');
include (GLPI_ROOT . "/inc/includes.php");

if(!isset($_GET["id"])) {
   $_GET["id"] = -1;
}

$doc = new Document();
$documentitem = new DocumentItem();

if (isset($_POST["add"])) {
   $doc->check(-1,'w',$_POST);

   if (isset($_POST['itemtype']) && isset($_POST['items_id'])  // From item
       && isset($_FILES['filename']['tmp_name'])
       && $doc->getFromDBbyContent($_POST["entities_id"], $_FILES['filename']['tmp_name'])) {
      addDeviceDocument($doc->fields['id'], $_POST['itemtype'], $_POST['items_id']);
   } else {
      $newID=$doc->add($_POST);
      $name="";
      if (isset($_POST["name"])){
         $name=$_POST["name"];
      } else if (isset($_FILES['filename']) && isset($_FILES['filename']['name'])) {
         $name=$_FILES['filename']['name'];
      }
      logEvent($newID, "documents", 4, "document",
               $_SESSION["glpiname"]." ".$LANG['log'][20]." ".$name.".");
   }

   glpi_header($_SERVER['HTTP_REFERER']);

} else if (isset($_POST["delete"])) {
	$doc->check($_POST["id"],'w');

	$doc->delete($_POST);
	logEvent($_POST["id"], "documents", 4, "document", $_SESSION["glpiname"]." ".$LANG['log'][22]);
	glpi_header($CFG_GLPI["root_doc"]."/front/document.php");

} else if (isset($_POST["restore"])) {
   $doc->check($_POST["id"],'w');

   $doc->restore($_POST);
   logEvent($_POST["id"], "documents", 4, "document", $_SESSION["glpiname"]." ".$LANG['log'][23]);
   glpi_header($CFG_GLPI["root_doc"]."/front/document.php");

} else if (isset($_POST["purge"])) {
   $doc->check($_POST["id"],'w');

   $doc->delete($_POST,1);
   logEvent($_POST["id"], "documents", 4, "document", $_SESSION["glpiname"]." ".$LANG['log'][24]);
   glpi_header($CFG_GLPI["root_doc"]."/front/document.php");

} else if (isset($_POST["update"])) {
   $doc->check($_POST["id"],'w');

   $doc->update($_POST);
   logEvent($_POST["id"], "documents", 4, "document", $_SESSION["glpiname"]." ".$LANG['log'][21]);
   glpi_header($_SERVER['HTTP_REFERER']);

} else if (isset($_POST["adddocumentitem"])) {
   $documentitem->check(-1,'w',$_POST);
   if ($documentitem->add($_POST)) {
      logEvent($_POST["documents_id"], "documents", 4, "document",
               $_SESSION["glpiname"]." ".$LANG['log'][32]);
   }
   glpi_header($_SERVER['HTTP_REFERER']);

} else if (isset($_POST["deletedocumentitem"])) {

   if (count($_POST["item"])){
      foreach ($_POST["item"] as $key => $val) {
         if ($documentitem->can($key, 'w')) {
            $documentitem->delete(array('id'=>$key));
         }
      }
   }
   logEvent($_POST["documents_id"], "documents", 4, "document",
            $_SESSION["glpiname"]." ".$LANG['log'][33]);
   glpi_header($_SERVER['HTTP_REFERER']);

} else if (isset($_GET["deletedocumentitem"])
           && isset($_GET["documents_id"]) && isset($_GET["id"])) {

   $documentitem->check($_GET["id"],'w');
   if ($documentitem->delete(array('id'=>$_GET["id"]))) {
      logEvent($_GET["documents_id"], "documents", 4, "document",
               $_SESSION["glpiname"]." ".$LANG['log'][33]);
   }
   glpi_header($_SERVER['HTTP_REFERER']);

} else {
   commonHeader($LANG['Menu'][27],$_SERVER['PHP_SELF'],"financial","document");
   $doc->showForm($_SERVER['PHP_SELF'],$_GET["id"]);
   commonFooter();
}

?>
