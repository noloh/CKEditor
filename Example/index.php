<?php
//Path to NOLOH Kernel, change to your path
require_once("/var/www/htdocs/Stable/NOLOH/NOLOH.php");
require_once("../CKEditor.php");

class CKEditorTest extends WebPage
{
	function CKEditorTest()
	{
		parent::WebPage('CKEditor Example');
		//Instantiate first CKEditor
		$editor1 = new CKEditor('', 10, 40);
		//Instantiate second CKEditor
		$editor2 = new CKEditor('Text that is already entered', $editor1->Right + 10, 40, 300, 600);
		//Set the second CKEditor's Skin to Office theme
		$editor2->Skin = CKEditor::Office;
		//Instantiate third CKEditor
		$editor3 = new CKEditor('Text that is already entered', $editor2->Right + 50, 40);
		//Set the third CKEditor's Skin to V2 theme
		$editor3->Skin = CKEditor::V2;
		//Adds the 3 Editors to the WebPage
		$this->Controls->AddRange($editor1, $editor2, $editor3);
		/*For each of the above 3 editors we're going to add a ComboBox above
		the editor that allows you to change the theme and a button that will switch
		the CKEditor to the Basic/Advanced Toolbar*/
		foreach($this->Controls as $control)
			if($control instanceof CKEditor)
			{
				$this->Controls->Add($skins = new ComboBox($control->Left, 5, 150));
				$skins->Items->AddRange(new Item('-- Select Theme --', null), CKEditor::Kama, CKEditor::Office, CKEditor::V2);
				$skins->Change = new ServerEvent($this, 'ChangeSkin', $skins, $control);
				//Button to switch to Basic Toolbar
				$this->Controls->Add($basic = new Button('Basic', $skins->Right + 15, 5))
					->Click = new ServerEvent($control, 'SetToolbar', CKEditor::Basic);
				//Button to switch to Advanced Toolbar	
				$this->Controls->Add(new Button('Advanced', $basic->Right + 10, 5))
					->Click = new ServerEvent($control, 'SetToolbar', CKEditor::Full);
			}
	}
	/**
	* Changes a CKEditor's instance theme to the one selected by the skin selector;
	* This is triggered when skin selector is changed.
	* 
	* @param ComboBox $selection
	* @param CKEditor $editor
	*/
	function ChangeSkin($selection, $editor)
	{
		if(($value = $selection->SelectedValue) !== null)
			$editor->Skin = $value;
	}
}
?>