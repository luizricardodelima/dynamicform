<?php
require_once "dynamicformitem.class.php";
require_once "dynamicformvalidationerror.class.php";

class DynamicFormItemGroupedText extends DynamicFormItem
{

    public function getHtmlFormattedContent()
    {
        $result = "<ul>";
        foreach ($this->spec->items as $j => $groupedtextItem)
            $result .= "<li>{$groupedtextItem}: ".htmlentities($this->content[$j], ENT_QUOTES, 'utf-8')."</li>";
        $result .= "</ul>";
        return $result;
    }

    public static function getType()
    {
        return 'groupedtext'; 
    }

    public static function javascriptEditMethod()
    {
        return 'edt_grt';
    }

    public static function outputAddEditControls($html_id) 
    {
        $result = "
        <script>
        function add_grt_{$html_id}()
		{	
			var item_description = prompt('".DynamicFormHelper::_('structure.table.message.add')."');
			if (item_description != null)
			{
				str_{$html_id}.push
				({
					type:'".self::getType()."',
					description:item_description,
					unrestrict:true,
					mandatory:true,
					spec:{items:[]}
				});
				update_table_{$html_id}();
				update_field_{$html_id}();
			}
        }
        function edt_grt_{$html_id}(i)
        {
            document.getElementById('grt_dlg_{$html_id}').style.display = 'block';
            document.getElementById('grt_des_{$html_id}').value = str_{$html_id}[i].description;
            document.getElementById('grt_itm_{$html_id}').value = str_{$html_id}[i].spec.items.join('\\n');
            document.getElementById('grt_unr_{$html_id}').checked = str_{$html_id}[i].unrestrict;
            document.getElementById('grt_man_{$html_id}').checked = str_{$html_id}[i].mandatory;
            document.getElementById('grt_sav_{$html_id}').onclick = function(){sav_grt_{$html_id}(i);};
        }
        function sav_grt_{$html_id}(i)
        {
            str_{$html_id}[i].description = document.getElementById('grt_des_{$html_id}').value;
            str_{$html_id}[i].spec.items = document.getElementById('grt_itm_{$html_id}').value.split('\\n');
			str_{$html_id}[i].unrestrict = document.getElementById('grt_unr_{$html_id}').checked;
			str_{$html_id}[i].mandatory = document.getElementById('grt_man_{$html_id}').checked;
			document.getElementById('grt_dlg_{$html_id}').style.display = 'none';
			update_table_{$html_id}();
			update_field_{$html_id}();
        }
        </script>

        <div id=\"grt_dlg_{$html_id}\" style=\"display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4);\">
		<div style=\"background-color: white; margin: 15% auto; padding: 20px; border: 1px solid #333; width: 80%; 	display: grid; grid-gap: 0.5em; grid-template-columns: 1fr;\">
		<span><input type=\"checkbox\" id=\"grt_unr_{$html_id}\"/><label for=\"grt_unr_{$html_id}\">".DynamicFormHelper::_('item.unrestrict')."</label></span>
		<span><input type=\"checkbox\" id=\"grt_man_{$html_id}\"/><label for=\"grt_man_{$html_id}\">".DynamicFormHelper::_('item.mandatory')."</label></span>
		<label for=\"grt_des_{$html_id}\">".DynamicFormHelper::_('item.description')."</label>
        <input  id=\"grt_des_{$html_id}\" type=\"text\"/>
        <label for=\"grt_itm_{$html_id}\">".DynamicFormHelper::_('item.groupedtext.spec.items')." <small>".DynamicFormHelper::_('item.groupedtext.spec.items.help')."</small></label>
		<textarea id=\"grt_itm_{$html_id}\" rows=\"3\"></textarea>
		<button type=\"button\" id=\"grt_sav_{$html_id}\">".DynamicFormHelper::_('item.action.save')."</button>
		<button type=\"button\" onclick=\"document.getElementById('grt_dlg_{$html_id}').style.display = 'none';\">".DynamicFormHelper::_('item.action.cancel')."</button>
		</div>
		</div>
        ";
        $result .= "<button type=\"button\" onclick=\"add_grt_{$html_id}();\">";
        $result .= DynamicFormHelper::_('structure.table.button.add.groupedtext');
        $result .= "</button>";
        return $result;
    }

    public function outputControls($htmlName, $index, $active) {
        
        $result = "
        <label>{$this->description}";
        if ($this->mandatory) $result .= "<small>".DynamicFormHelper::_('control.restriction.start').DynamicFormHelper::_('control.restriction.mandatory').DynamicFormHelper::_('control.restriction.end')."</small>";
        $result .= "</label>
        <div style=\"display: flex; flex-wrap: wrap; justify-content: space-between; align-items: stretch;\">";
        foreach ($this->spec->items as $j => $groupedtextItem)
		{
            $result .= "
            <label for=\"{$htmlName}[{$index}][{$j}]\" style=\"text-align: right;\">$groupedtextItem:&nbsp;
            <input type=\"text\" name=\"{$htmlName}[{$index}][{$j}]\" id=\"{$htmlName}[{$index}][{$j}]\"";
            $result .= " value=\"".htmlentities($this->content[$j], ENT_QUOTES, 'utf-8')."\"";
            $result .= ($active) ? "" : " disabled=\"disabled\"";
            $result .= "/></label>";
		}
        $result .= "</div>";
        return $result;
    }

    public function validate()
    {
        $validationErrors = array();
        $atLeastOneIsEmpty = false;
        foreach ($this->content as $groupedtextItem)
            if ($groupedtextItem == '') $atLeastOneIsEmpty = true;
		if ($this->mandatory && $atLeastOneIsEmpty)
			$validationErrors[] = DynamicFormValidationError::MANDATORY;
        return $validationErrors;
    }

    function __construct($object, $content)
	{
        parent::__construct($object, $content);
        $this->type = self::getType();
        if (is_null ($content)) {
            $this->content = array();
            foreach ($this->spec->items as $item)
                $this->content[] = '';
        }
        else {
            $this->content = $content;
        }
	}
}

?>