<?php

include '../../../../inc/includes.php';

$report = new PluginReportsAutoReport(__('Custom Asset Report', 'reports'));

// Fetch item types
$item_types = PluginReportsDropdown::getAllItemTypes(); // Assuming this method exists and returns [classname => translated_name]

// Create dropdown criteria for asset types
$item_type_criteria = new PluginReportsDropdownCriteria([
    'name'        => 'item_types',
    'label'       => __('Asset Types', 'reports'),
    'item_types'  => $item_types, // Expects [key => value] for dropdown options
    'multiple'    => true,
]);

$report->addCriteria($item_type_criteria);

// Define predefined list of common fields
$predefined_fields = [
    'name'           => __('Name', 'reports'),
    'serial'         => __('Serial Number', 'reports'),
    'otherserial'    => __('Inventory Number', 'reports'),
    'locations_id'   => __('Location', 'reports'),
    'states_id'      => __('Status', 'reports'),
];

// Create multiple string criteria for fields selection
$fields_selection_criteria = new PluginReportsMultipleStringCriteria([
    'name'   => 'fields_selection',
    'label'  => __('Fields to Display', 'reports'),
    'values' => $predefined_fields, // Expects [key => value] for checkbox options
]);

$report->addCriteria($fields_selection_criteria);

$report->displayCriteriasForm();

if ($report->criteriasValidated()) {
    // Retrieve selected asset types
    $selected_item_types = $report->criterias['item_types']; // Assuming 'item_types' is the name used in addCriteria
    
    // Retrieve selected fields
    $selected_fields = $report->criterias['fields_selection']; // Assuming 'fields_selection' is the name used in addCriteria

    // Initialize variables
    $table_name = '';
    $query = '';

    // Handle selected asset type (currently processing only the first one)
    if (!empty($selected_item_types)) {
        $item_type = $selected_item_types[0]; // TODO: Implement handling for multiple selected item types
        // Assuming getItemTypeForTable() exists and can convert item type string to table name.
        // Common GLPI functions include `getTableForItemType` or similar.
        $table_name = getTableForItemType($item_type); 
    }

    // Construct SQL query if table name and fields are available
    if (!empty($table_name) && !empty($selected_fields)) {
        $select_clause = 'SELECT `' . implode('`, `', $selected_fields) . '`';
        $from_clause = 'FROM `' . $table_name . '`';
        $query = $select_clause . ' ' . $from_clause;

        // Dynamically Define Report Columns
        // $predefined_fields is available from the outer scope where it's defined for criteria
        foreach ($selected_fields as $field_machine_name) {
            $display_name = isset($predefined_fields[$field_machine_name]) 
                            ? $predefined_fields[$field_machine_name] 
                            : ucfirst(str_replace('_', ' ', $field_machine_name)); // Fallback display name
            $report_columns[] = new PluginReportsColumn([
                'name'         => $field_machine_name,
                'display_name' => $display_name,
                'sorton'       => $field_machine_name, // Added for sorting
            ]);
        }

        if (!empty($report_columns)) {
            $report->setColumns($report_columns);
        }
        
        // Determine default sort column and add ORDER BY clause
        if (!empty($selected_fields)) { // Ensure fields are selected before trying to sort
            $default_sort_column_name = '';
            if (in_array('name', $selected_fields)) {
                $default_sort_column_name = 'name';
            } else {
                // Fallback to the first selected field if 'name' is not present
                $default_sort_column_name = $selected_fields[0];
            }
            
            // $report->getOrderBy() should handle the actual sort field and direction from request
            // or use the provided default.
            $order_by_clause = $report->getOrderBy($default_sort_column_name);
            if (!empty($order_by_clause)) {
                $query .= " " . $order_by_clause; // Append to existing query
            }
        }

    } // End of query and column construction

    // Set SQL Request and Execute if query and columns are ready
    if (!empty($query) && !empty($report_columns)) {
        $report->setSqlRequest($query);
        $report->execute(); // This should handle display and footer if successful
    } else {
        // Error messages or handling if query/columns are not set
        if (empty($table_name) && !empty($selected_item_types) && empty($query)) { // Check empty query to avoid duplicate error
            echo "<p>Error: Could not determine table name for the selected asset type: " . Html::clean($selected_item_types[0]) . "</p>";
        }
        if (empty($selected_fields) && empty($query)) { // Check empty query
            echo "<p>Error: No fields selected for the report.</p>";
        }
        if (empty($report_columns) && !empty($selected_fields)) { // Only show if fields were selected but columns not made
             echo "<p>Error: Could not define report columns based on selected fields.</p>";
        }
        // If execute() is not called (due to errors or missing query/columns), we need to ensure the footer is.
        Html::footer(); 
    }
} else {
    Html::footer();
}

// TEST COVERAGE:
//
// 1. Criteria Form:
//    - TODO: Verify Asset Types dropdown is populated correctly from PluginReportsDropdown::getAllItemTypes().
//    - TODO: Verify 'Fields to Display' checkboxes (PluginReportsMultipleStringCriteria) show the $predefined_fields correctly.
//    - TODO: Test selecting one asset type.
//    - TODO: Test selecting multiple asset types (once multi-type handling is implemented).
//    - TODO: Test selecting one field to display.
//    - TODO: Test selecting multiple fields to display.
//    - TODO: Test selecting no fields to display (should show an error or no data).
//    - TODO: Test selecting no asset types (should show an error or no data).
//
// 2. Report Logic (PHP - within if ($report->criteriasValidated()) block):
//    - TODO: Verify $selected_item_types correctly captures selected asset class names.
//    - TODO: Verify $selected_fields correctly captures selected field machine names.
//    - TODO: Single Asset Type Processing:
//        - TODO: Verify getTableForItemType($item_type) returns the correct table name for various known item types (e.g., 'Computer', 'Monitor').
//        - TODO: Test with an invalid/unknown item type (if possible to select, how it's handled).
//    - TODO: Multiple Asset Type Processing (Future Enhancement):
//        - TODO: (Once implemented) Verify logic for handling multiple $selected_item_types (e.g., UNION queries or separate tables).
//    - TODO: Field Selection Variations:
//        - TODO: Test with all predefined fields selected.
//        - TODO: Test with a subset of predefined fields.
//    - TODO: SQL Query Construction:
//        - TODO: Verify SELECT clause correctly includes backticked field names from $selected_fields.
//        - TODO: Verify FROM clause correctly uses the backticked $table_name.
//        - TODO: Verify ORDER BY clause is correctly appended, using $report->getOrderBy() with the default sort column.
//        - TODO: Verify default sort column logic (uses 'name' if selected, else first field).
//    - TODO: PluginReportsColumn Instantiation:
//        - TODO: Verify each column in $report_columns has 'name' set to field_machine_name.
//        - TODO: Verify 'display_name' uses $predefined_fields value, or the fallback formatted name.
//        - TODO: Verify 'sorton' is set to field_machine_name.
//    - TODO: Report Execution:
//        - TODO: Verify $report->setColumns() is called with the correct $report_columns.
//        - TODO: Verify $report->setSqlRequest() is called with the correctly constructed $query.
//        - TODO: Verify $report->execute() is called.
//    - TODO: Error Handling:
//        - TODO: Test scenario where $table_name is empty (e.g., getTableForItemType fails) but $selected_item_types is not.
//        - TODO: Test scenario where $selected_fields is empty.
//        - TODO: Test scenario where $report_columns ends up empty even if $selected_fields is not.
//
// 3. Report Output (Generated by $report->execute()):
//    - TODO: Correct Columns Displayed:
//        - TODO: Verify column headers match the 'display_name' for selected fields.
//        - TODO: Verify only selected fields appear as columns.
//    - TODO: Data Accuracy:
//        - TODO: Manually query the database with the generated SQL and compare results with the report output for a few test assets.
//    - TODO: Sorting Functionality:
//        - TODO: Test sorting by clicking on each column header (ascending and descending).
//        - TODO: Verify default sort order is applied correctly before any user interaction.
//    - TODO: Data Type Handling (Future - if specific formatting/linking is added to columns):
//        - TODO: E.g., If 'locations_id' is made to link to the location, verify link.
//        - TODO: E.g., If dates are formatted, verify format.
//    - TODO: No Data Scenario:
//        - TODO: Test criteria that result in no data and verify the report displays a "no results" message.
//
// 4. General:
//    - TODO: Ensure all translatable strings `__('Text', 'reports')` are correctly loaded and displayed in the user's language.
//    - TODO: Check for PHP errors/warnings/notices with display_errors enabled and error_reporting set to E_ALL.
