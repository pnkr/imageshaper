<?php
/*
* @package		Joomla
* @copyright	Copyright (C) 2019 Kiriakopoulos Panayiotis All rights reserved.
* @subpackage	plg_imageshaper
* @license		GNU General Public License
*/

// Tutorial : https://github.com/joomla-framework/image

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


use Joomla\Image\Image;
use Joomla\CMS\Filesystem\File;


class plgContentImageShaper extends JPlugin
{

	public function onContentAfterSave($context, &$article, $isNew)
	{
		
		$imagesMIME = array('image/jpeg', 'image/png', 'image/gif');
		
		if (!isset($article->type)) {
			return;
		}
		
		$type = $article->type;
		
		

				
		// Αν δεν είναι εικόνα ή δεν υποστηρίζεται... it's not our business :)
		if (!in_array($type,$imagesMIME)) {
			return true;
		}

		 $current_image = new Image($article->filepath);
		 
		// Φόρτωση μεγέθους εικόνας
		$width = $current_image->getWidth();
		$height = $current_image->getHeight();

		// Φόρτωση επιθυμητών μεγεθών από ρυθμίσεις
		$newwidth 	= $this->params->get('width', $width);
		$newheight 	= $this->params->get('height', $height);

		if ( !$newwidth && !$newheight ) { 
			return;
		}

		if ( $width > $newwidth || $height > $newheight ) {


			// Οδηγίες για το πώς να παρουμε συγκεκριμένα στοιχεία από το αρχείο (δεν χρειάζονται προς το παρόν) αλλά κρατάμε για την νέα έκδοση

			$currentfile_name 		= basename($article->filepath); // Πλήρης διαδρομή του αρχείου
			$currentfile_ext  		= JFile::getExt($currentfile_name); //Κατάληξη του αρχείου
			$currentfile_onlyname 	= JFile::stripExt($currentfile_name); // Ονομα αρχείου χωρίς κατάληξη
			$currentfile_onlypath	= dirname($article->filepath); // Διαδρομή του αρχείου χωρίς το αρχείο 


			/*
				Μέθοδοι αλλαγής μεγέθους
				Image::SCALE_FILL - Gives you a thumbnail of the exact size, stretched or squished to fit the parameters.
				Image::SCALE_INSIDE - Fits your thumbnail within your given parameters. It will not be any taller or wider than the size passed, whichever is larger.
				Image::SCALE_OUTSIDE - Fits your thumbnail to the given parameters. It will be as tall or as wide as the size passed, whichever is smaller.
				Image::SCALE_FIT - Fits your thumbnail to given boundaries maintaining aspect ratio. Result will be aligned vertically to center and horizontally to middle.
				Image::CROP - Gives you a thumbnail of the exact size, cropped from the center of the full sized image.
				Image::CROP_RESIZE - As above, but gives a clean resize and crop from center.				
			*/

			// Αλλαγής μεγέθους εικόνας
			$resized_image = $current_image->resize($newwidth, $newheight, true, Image::SCALE_INSIDE);
			// Αποθήκευση ξανα με το νέο μέγεθος
			$resized_image->toFile($article->filepath);
		}; 

		// Δημιουργία επιλεγμένων διαστάσεων thumbnails.
		$sizes = array('800x464','500x290','300x174', '150x150');
		$current_image->createThumbs($sizes, Image::SCALE_FIT);

		return true;
	}
}
?>
