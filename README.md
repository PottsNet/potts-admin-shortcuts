# Potts Administration Shortcuts for webtrees

Potts Administration Shortcuts provides a configurable administrator-only **My Page** block for webtrees 2.2.x. It gives administrators quick access to commonly used module settings and user administration pages.

## Features

- Lists enabled modules that provide a webtrees settings page.
- Lets each administrator choose their most-used module settings links.
- Includes **User administration** as an optional shortcut.
- Stores choices in the individual My Page block configuration.
- Hides the block content from non-administrators.
- Works with any webtrees theme.
- Includes Custom Module Manager update-service metadata.

## Requirements

- webtrees 2.2.x
- PHP version supported by your webtrees installation
- Administrator access to enable modules and add My Page blocks

## Installation

1. Download the release ZIP from GitHub.
2. Extract the ZIP.
3. Upload the `potts_admin_shortcuts` folder to `modules_v4/`.
4. Open **Control panel > Modules > All modules** and enable **Administration shortcuts**.
5. Open **My page**, choose **Change the blocks on this page** and add **Administration shortcuts**.
6. Use the block preferences button to select the links to display.

## Custom Module Manager

This module includes update-service support for webtrees Custom Module Manager through `latest-version.txt`.

## Notes

- The block is intended for administrator My Page use only.
- The shortcut list is built from enabled modules that expose a webtrees settings page.
- Each block instance stores its own selected shortcut list.

## Support

Please report bugs or feature requests through GitHub Issues:

https://github.com/PottsNet/potts-admin-shortcuts/issues

## Licence

Potts Administration Shortcuts is free software licensed under the GNU General Public License, version 3 or later.
