<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Get Options Plugin
 *
 * @author    Ahmads Saad Aldeen <ahmadsaad983@hotmail.com>
 * @copyright Copyright (c) 2013 Ahmads Saad Aldeen
 * @license   
 *
 */

$plugin_info = array(
	'pi_name'			=> 'Get Options',
	'pi_version'		=> '1.0.0',
	'pi_author'			=> 'Ahmads Saad Aldeen',
	'pi_author_url'		=> 'http://github.com/ahmadsa/get_options',
	'pi_description'	=> 'Generates the options belong to a custom field.',
	'pi_usage'			=> get_options::usage()
);

class get_options {
	
	public $option_fields = array(
			'multi_select',
			'select',
			'radio',
			'checkboxes'
		);
	public $site_id;
	public $sort;
	public $field_name;
		
	function get_options()
	{
		$this->EE =& get_instance();
        $this->EE->lang->loadfile(__CLASS__);
        // Store the template parameters
        $sort = $this->EE->TMPL->fetch_param('sort');
        $field_name = $this->EE->TMPL->fetch_param('field_name');
        if (!$field_name) return $this->EE->output->fatal_error(lang('get_options_require_field_name'));
        				
		$field_options = $this->get_field_options($field_name);
		
		if ($sort == 'alpha')
        {
            sort($field_options);
        }

        $this->return_data = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $field_options);
	}
	
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function get_field_data($field_name)
	{
		$this->EE->db->from('channel_fields');
		$this->EE->db->where('field_name', $field_name);			
		$query = $this->EE->db->get();
		if ($query->num_rows() > 0)
		{
			return $query->first_row('array');
		}
		return $this->EE->output->fatal_error(lang('get_options_require_field_name'));	
	}		
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function get_field_options($field_name)
	{
	
		$field = $this->get_field_data($field_name);
		
		$options = array();
		
		if (in_array($field['field_type'], $this->option_fields))
		{
			if ($field['field_pre_populate'] == 'y')
			{
				$query = $this->EE->db->select('field_id_'.$field['field_pre_field_id'])
						->distinct()
						->from('channel_data')
						->where('channel_id', $field['field_pre_channel_id'])
						->where('field_id_'.$field['field_pre_field_id'].' !=', '')
						->get();
				
			//	$current = explode('|', $this->entry($field['field_name']));
				
				foreach ($query->result_array() as $row)
				{
					$options[] = array(
						'option_value' => $row['field_id_'.$field['field_pre_field_id']],
						'option_name' => str_replace(array("\r\n", "\r", "\n", "\t"), ' ' , substr($row['field_id_'.$field['field_pre_field_id']], 0, 110)),
					/*	'selected' => (in_array($row['field_id_'.$field['field_pre_field_id']], $current)) ? ' selected="selected"' : '',
						'checked' => (in_array($row['field_id_'.$field['field_pre_field_id']], $current)) ? ' checked="checked"' : '',*/
					);
				}
			}
			
			else if ($field['field_list_items'])
			{
				foreach (preg_split('/[\r\n]+/', $field['field_list_items']) as $row)
				{
					$row = trim($row);
					
					if ( ! $row)
					{
						continue;
					}
					
			//		$field_data = (is_array($this->entry($field_name))) ? $this->entry($field_name) : explode('|', $this->entry($field_name));
					
					$options[] = array(
						'option_value' => $row,
						'option_name' => $row,
					/*	'selected' => (in_array($row, $field_data)) ? ' selected="selected"' : '',
						'checked' => (in_array($row, $field_data)) ? ' checked="checked"' : '',*/
					);
				}
			}
			
		}
		
		else
		{
			return $this->EE->output->fatal_error(lang('get_options_vaild_field_name_type'));
		}
		
		return $options;	
	}
		
	function usage()
	{
		ob_start(); 
	?>
	
		Get Options will generate the options belong to a custom field. Perfect for advanced search forms!
		
		Usage:
		
		{exp:get_options field_name="field_name"}
		<option value="{option_value}">{option_name}</option>
		{/exp:get_options}
		
		field_name="" - The short name of the custom field to pull values from.
		
		Optional parameters:
				
		sort="alpha" - Sort the values alphabetically. If this is not set, they will be displayed in the order they appear in the database.
				
	<?php
		$buffer = ob_get_contents();
		ob_end_clean(); 
		return $buffer;
	}

}

/* End of file get_options.php */
/* Location: ./system/expressionengine/third_party/get_options/get_options.php */
