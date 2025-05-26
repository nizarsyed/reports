# reports

Reports. This plugin enables additional reports. It also allow you to add new reports in a simply way.

## Features

- It also plugin allow you to add new reports in a simply way (one PHP script for the report and one for the translation).
- It handle the right for each new report
- It provides some new reports (as sample)

## Available Reports (Examples from v1.3.0)

- Detailed license report
- Duplicate computers
- Financial information
- History of last hardware’s installations
- History of last software’s installations
- Licenses by expiration date
- List all devices of a group, order by users
- List of groups and members
- Location tree
- Number of equipments by location
- Number of items by entity
- Rule’s catalog

## Installation

1.  Download the latest release from the [releases page](https://github.com/yllen/reports/releases). (Note: This link is from reports.xml, confirm if it's the correct one or use a generic placeholder if unsure).
2.  Extract the archive to the `plugins` directory in your GLPI installation. (e.g., `<glpi_root>/plugins/reports`)
3.  Navigate to the *Setup > Plugins* page in GLPI.
4.  Install and activate the "Reports" plugin.

## Usage

### Accessing Reports
Once installed and activated, you should find the additional reports under the main "Reports" section or a similar menu in GLPI (actual menu may vary depending on GLPI version and other plugins).

### Adding New Reports
This plugin allows you to add new reports easily:
1.  Create a PHP script for your report logic. Place it within a new subdirectory in the plugin's `report/` directory (e.g., `plugins/reports/report/myreport/myreport.php`).
2.  Create a corresponding language file for translations if needed.
3.  The plugin should automatically detect new reports. Rights management for new reports is also handled by the plugin.

Refer to the existing reports in the `report/` directory for examples.

## Contributing

Contributions are welcome! If you have improvements or new reports to add:

1.  **Fork** the repository on GitHub.
2.  Create a new **branch** for your feature or bug fix.
3.  Make your changes, including clear comments and tests if applicable.
4.  Submit a **pull request** for review.

## License

This plugin is licensed under the [GPL v3+](https://www.gnu.org/licenses/gpl-3.0.html).

## More Information

-   **Authors:** Nelly Mahu-Lasson, Remi Collet
-   **Homepage:** [https://github.com/yllen/reports](https://github.com/yllen/reports)
-   **Download:** [https://github.com/yllen/reports/releases](https://github.com/yllen/reports/releases)
