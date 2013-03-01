Get Options will generate the options belong to a custom field. Perfect for advanced search forms!
		
		Usage:
		
		{exp:get_options field_name="field_name"}
		<option value="{option_value}">{option_name}</option>
		{/exp:get_options}
		
		field_name="" - The short name of the custom field to pull values from.
		
		Optional parameters:
				
		sort="alpha" - Sort the values alphabetically. If this is not set, they will be displayed in the order they appear in the database.
				