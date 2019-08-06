<?php
App::uses('AppHelper', 'View/Helper');
class AdminCategoryHelper extends AppHelper {
    function outputOptionType($tree, $mod, $arrDisable = null, $select_id = 0, $limit_level = 2, $dash = null, $level = 1, $result = null)
	{
		foreach($tree as $item)
		{
            //disable
            $disable = null;
            if($arrDisable != null && in_array($level, $arrDisable))
            {
                $disable = "disabled='disabled'";
            }
            //select
			$select = null;
			if($item[$mod]['id'] == $select_id)
			{
				$select = "selected='selected'";
			}
			$result .= "<option ".$select." ".$disable." value='".$item[$mod]['id']."'>".$dash.$item[$mod]['name']."</option>";
			if(count($item['children']) > 0 && $level < $limit_level)
			{
                $dash .= "----";
                $result = $this->outputOptionType($item['children'], $mod, $arrDisable, $select_id, $limit_level, $dash, $level+=1, $result);
                $dash = substr($dash, 0, strlen($dash) - 4);
                $level -= 1;
			}
		}
		return $result;
	}
    
    function outputTableType($tree, $mod, $limit_level = 2, $dash = null, $level = 1, $result = null, $allowFields = array(), $activeFields = array())
	{
		foreach($tree as $item)
		{
            $htmlEnable = '';
            if(!empty($item[$mod]['enable']) || !empty($item[$mod]['publish']))
            {
                $htmlEnable = 
                    '<a onclick="jQuery.admin.action(\''.$item[$mod]['id'].'\', \'disable\')" href="javascript:void(0)">
                        <i title="'.__d('store', 'Disable').'" class="fa fa fa-check"></i>
                    </a>';
            }
            else 
            {
                $htmlEnable = 
                    '<a onclick="jQuery.admin.action(\''.$item[$mod]['id'].'\', \'enable\')" href="javascript:void(0)">
                        <i title="'.__d('store', 'Enable').'" class="fa fa fa-close"></i>
                    </a>';
            }
            
            $htmlActiveFields = '';
            if($activeFields != null)
            {
                foreach($activeFields as $key => $activeField)
                {
                    $htmlActiveFields .= '<td>';
                    if(!empty($item[$mod][$key]))
                    {
                        $htmlActiveFields .= 
                            '<a onclick="jQuery.admin.action(\''.$item[$mod]['id'].'\', \''.$activeField[1].'\')" href="javascript:void(0)">
                                <i title="'.$activeField[3].'" class="fa fa fa-check"></i>
                            </a>';
                    }
                    else 
                    {
                        $htmlActiveFields .= 
                            '<a onclick="jQuery.admin.action(\''.$item[$mod]['id'].'\', \''.$activeField[0].'\')" href="javascript:void(0)">
                                <i title="'.$activeField[2].'" class="fa fa fa-close"></i>
                            </a>';
                    }
                    $htmlActiveFields .= '</td>';
                }
            }
			
			$fields = array_keys($item[$mod]);
			$not_include_fields = array('created','updated','lft','rght','parent_id','store_id','ordering','enable','id','force_to_buy','publish');
			$fields = array_diff($fields, $not_include_fields);
			if(!empty($allowFields))
				$fields = $allowFields;
            $style_child = '';
            if($dash != null)
            {
                $style_child = 'padding-left:40px';
            }
            $parent_checkbox = $htm_delete = '';
            if(empty($item['children']))
            {
                $parent_checkbox = '<input type="checkbox" value="'.$item[$mod]['id'].'" class="multi_cb" id="cb'.$item[$mod]['id'].'" name="data[cid][]">';
                $htm_delete = 
                    '|
                    <a onclick="jQuery.admin.action(\''.$item[$mod]['id'].'\', \'delete\')" href="javascript:void(0)">
                        '.__d('store', 'Delete').'                                    
                    </a>';
            }
            else
            {
                $parent_checkbox = '<input type="checkbox" value="'.$item[$mod]['id'].'" class="multi_cb" id="cb'.$item[$mod]['id'].'" name="data[cid][]" style="display:none">';
            }
			$td_field = '';
			foreach($fields as $field)
			{
					$td_field .= '<td style="text-align:left;'.$style_child.'">
                        '.$dash.$item[$mod][$field].'                                
                    </td>' ;
			}
            $result .= 
                '<tr>
                    <td>
                        '.$parent_checkbox.'
                    </td>
                    '.$td_field.'
                    <td style="text-align:center;">
                        '.$htmlEnable.'
                    </td>
                    '.$htmlActiveFields.'
                    <td style="text-align:center;">
                        <input type="text" value="'.$item[$mod]['ordering'].'" class="input_ordering form-control" name="data[ordering][]">                                
                    </td>
                    <td style="text-align:center;">
                        <a onclick="jQuery.admin.action(\''.$item[$mod]['id'].'\', \'create\')" href="javascript:void(0)">
                            '.__d('store', 'Edit').'                                    
                        </a>
                        '.$htm_delete.'
                    </td>
                </tr>';
			if(!empty($item['children']) && count($item['children']) > 0 && $level < $limit_level)
			{
                $dash = "|----";
                $result = $this->outputTableType($item['children'], $mod, $limit_level, $dash, $level+=1, $result, $allowFields, $activeFields);
                $dash = substr($dash, 0, strlen($dash) - 5);
                $level -= 1;
			}
		}
		return $result;
	}
    
    function loadProductCategoryBreadCrumb($item, $mod, $result = '')
    {
        $result .= '<a href="'.STORE_URL.'products/category/'.$item[$mod]['id'].'">'.$item[$mod]['name'].'</a>';
        if(!empty($item[$mod]['children']))
        {
            $result .= '<span class="separator">/</span>';
            $result = $this->loadProductCategoryBreadCrumb($item[$mod]['children'], $mod, $result);
        }
		return $result;
    }
}
