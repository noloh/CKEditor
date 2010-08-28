<?php
/**
*  CKEditor Nodule Class
*/
class CKEditor extends Panel
{
	/**
	*  Default Themes
	*/
	const Kama = 'kama', Office = 'office2003', V2 = 'v2';
	/**
	*  Default Toolbars
	*/
	const Basic = 'Basic', Full = 'Full';
	/**
	* Toolbar Strip Items
	*/
	const Source = 'Source',
	Seperator = '-',
	Save = 'Save',
	NewPage = 'NewPage',
	Preview = 'Preview',
	Templates = 'Templates',
	Cut = 'Cut',
	Copy = 'Copy',
	Paste = 'Paste',
	PasteText = 'PasteText',
	PasteFromWord = 'PasteFromWord',
	Prints = 'Print',
	SpellChecker = 'SpellChecker',
	Scayt = 'Scayt',
	Undo = 'Undo',
	Redo = 'Redo',
	Find = 'Find',
	Replace = 'Replace',
	SelectAll = 'SelectAll',
	RemoveFormat = 'RemoveFormat',
	Form = 'Form',
	Checkbox = 'Checkbox',
	Radio = 'Radio',
	TextField = 'TextField',
	Textarea = 'Textarea',
	Select = 'Select',
	Button = 'Button',
	ImageButton = 'ImageButton',
	HiddenField = 'HiddenField',
	Bold = 'Bold',
	Italic = 'Italic',
	Underline = 'Underline',
	Strike = 'Strike',
	Subscript = 'Subscript',
	Superscript = 'Superscript',
	NumberedList = 'NumberedList',
	BulletedList = 'BulletedList',
	Outdent = 'Outdent',
	Indent = 'Indent',
	Blockquote = 'Blockquote',
	JustifyLeft = 'JustifyLeft',
	JustifyCenter = 'JustifyCenter',
	JustifyRight = 'JustifyRight',
	JustifyBlock = 'JustifyBlock',
	Link = 'Link',
	Unlink = 'Unlink',
	Anchor = 'Anchor',
	Image = 'Image',
	Flash = 'Flash',
	Table = 'Table',
	HorizontalRule = 'HorizontalRule',
	Smiley = 'Smiley',
	SpecialChar = 'SpecialChar',
	PageBreak = 'PageBreak',
	RowBreak = '/',
	Styles = 'Styles',
	Format = 'Format',
	Font = 'Font',
	FontSize = 'FontSize',
	TextColor = 'TextColor',
	BGColor = 'BGColor',
	Maximize = 'Maximize',
	ShowBlocks = 'ShowBlocks',
	About = 'About';
	/**
	* TextArea that holds raw text
	* 
	* @var TextArea
	*/
	private $TextHolder;
	/**
	* Arraylist Holder for Toolbar Strips
	* 
	* @var ImplicitArrayList
	*/
	private $Strips;
	/**
	* Array holder for Configuration options
	* 
	* @var string|array
	*/
	private $Config;
	/**
	* Constructor
	* 
	* @param string $text
	* @param integer $left
	* @param integer $top
	* @param integer $width
	* @param integer $height
	* @return CKEditor
	*/
	function CKEditor($text = '', $left=0, $top=0, $width=400, $height=500)
	{
		parent::Panel($left, $top, $width, $height);
		$this->TextHolder = new TextArea($text);
		$this->TextHolder->ParentId = $this->Id;
		$this->TextHolder->Visible = false;
		$this->Strips = new ImplicitArrayList($this, 'AddStrip', 'RemoveStrip', 'ClearStrips');
		$this->Strips->InsertFunctionName = 'InsertStrip';
		$this->SetDefaults();
	}
	/**
	* Sets the defaults of this CKEditor instance
	*/
	private function SetDefaults()
	{
		$this->SetConfig('resize_enabled', false);
		//$this->SetToolBar(self::Basic);
	}
	/**
	* Returns the raw contents of the CKEditor
	* @return string
	*/
	function GetText()	{return $this->TextHolder->Text;}
	/**
	* Sets the Text of the CKEditor
	* 
	* @param string $text
	*/
	function SetText($text)	
	{
		$this->TextHolder->Text = $text;
		if($this->TextHolder->ShowStatus == Component::Shown)
			ClientScript::Queue($this->TextHolder, "CKEDITOR.instances.{$this->TextHolder}.setData", array($text));
	}
	function GetStrips()	{return $this->Strips;}
	/**
	* Strips->Add() Delegate. Use $object->Strips->Add() instead of calling this method.
	*/
	function AddStrip($strip)
	{
		$this->Strips->Add($strip, true);
		$this->SetToolbar($this->Strips->Elements);
	}
	/**
	* Strips->Insert() Delegate. Use $object->Strips->Insert() instead of calling this method.
	*/
	function InsertStrip($strip, $index)
	{
		$this->Strips->Insert($strip, true);
		$this->SetToolbar($this->Strips->Elements);
	}
	/**
	* Strips->Remove() Delegate. Use $object->Strips->Remove() instead of calling this method.
	*/
	function RemoveStrip($strip)
	{
		$this->Strips->Remove($strip, true);
		$this->SetToolbar($this->Strips->Elements);
	}
	/**
	* Strips->Clear() Delegate. Use $object->Strips->Clear() instead of calling this method.
	*/
	function ClearStrips()
	{
		$this->Strips->Clear(true);
		$this->SetToolbar($this->Strips->Elements);
	}
	/**
	* Set a CKEditor configuration option. See {@link http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html} for all options.
	* 
	* @param mixed $option
	* @param mixed $value
	*/
	function SetConfig($option, $value)
	{
		$this->Config[$option] = $value;
		$this->Refresh();
	}
	/**
	* Re-renders the CKEditor instance. In most cases there is no need to call manually.
	* 
	* @param mixed $race Whether to render with race condition safeguard.
	*/
	function Refresh($race = false)
	{
		if($this->TextHolder->ShowStatus == Component::Shown)
		{
			ClientScript::Queue($this->TextHolder, "CKEDITOR.instances.{$this->TextHolder}.destroy");
			if($race)
				ClientScript::RaceQueue($this->TextHolder, 'CKEDITOR', 'CKEDITOR.replace', array($this->TextHolder->Id, $this->Config), true, Priority::Low);
			else
				ClientScript::Queue($this->TextHolder, 'CKEDITOR.replace', array($this->TextHolder->Id, $this->Config), true, Priority::Low);
		}
	}
	/**
	* Override of default SetHeight(). If $height is less than 200, but greater than 60 the  editor area is reduced.
	* 
	* @param integer $height
	*/
	function SetHeight($height)
	{
		parent::SetHeight($height);
		if(is_int($height) && $height > 60 && $height < 200)
		    $this->SetConfig('height', $height - 60);
	}
	/**
	* Sets the current Skin of your CKEditor instance.
	* 
	* @param CKEditor::Kama|CKEditor::Office|CKEditor::V2 $skin
	*/
	function SetSkin($skin)
	{
		$this->Config['skin'] = $skin;
		$this->Refresh();
	}
	/**
	* Sets the Toolbar of the CKEditor instance. 
	* 
	* If string or Theme Constant is provided then Toolbar is set to that style Toolbar.
	* 
	* However, if an array of arrays containting Toolbar Items is provided then Toolbar will be set to contain those items. 
	* In most cases this should be done via the Strips->Add syntax, and not directly through ->Toolbar.
	* 
	* @param string|array $toolbar
	*/
	function SetToolbar($toolbar)
	{
		if(is_array($toolbar))
		{
			$el = each($toolbar);
			if(!is_array($el['value']))
				$toolbar = array($toolbar);
		}	
		$this->Config['toolbar'] = $toolbar;
		$this->Refresh(true);
	}
	/**
	* Create a new global Toolbar that can be used across all CKEditor instances.
	* 
	* After CreateToolbar() is used, you can then set your CKEditor instances to use your new toolbar via $object->Toolbar = $name;.
	* 
	* @param string $name
	* @param array|array(arrays) $strips
	*/
	static function CreateToolbar($name, $strips)
	{
		ClientScript::RaceQueue(WebPage::That(), 'CKEDITOR', 'CKEDITOR.config.toolbar_' . $name .'=' . ClientEvent::ClientFormat($strips) . ';');		
	}
	/**
	* Do not call manually! Override of default Show(). Triggers when CKEditor instance is initially shown.
	*/
	function Show()
	{
		parent::Show();
		$relativePath = System::GetRelativePath(getcwd(), dirname(__FILE__));
		//Add ckeditor script files
		ClientScript::AddSource($relativePath . '/ckeditor/ckeditor.js', false);
		//Add NOLOH bridge script file
		ClientScript::AddSource($relativePath . '/Bridge/bridge.js');
//		ClientScript::AddSource($relativePath . '/Bridge/bridge-min.js');
		//Trigger client ckeditor instantiation
		ClientScript::RaceQueue($this->TextHolder, 'CKEDITOR', 'CKEDITOR.replace', array($this->TextHolder->Id, $this->Config));
	}
}
?>
